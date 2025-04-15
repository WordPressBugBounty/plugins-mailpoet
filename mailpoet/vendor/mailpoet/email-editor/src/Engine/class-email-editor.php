<?php
declare(strict_types = 1);
namespace MailPoet\EmailEditor\Engine;
if (!defined('ABSPATH')) exit;
use MailPoet\EmailEditor\Engine\Patterns\Patterns;
use MailPoet\EmailEditor\Engine\PersonalizationTags\Personalization_Tags_Registry;
use MailPoet\EmailEditor\Engine\Templates\Templates;
use WP_Post;
use WP_Theme_JSON;
class Email_Editor {
 public const MAILPOET_EMAIL_META_THEME_TYPE = 'mailpoet_email_theme';
 private Email_Api_Controller $email_api_controller;
 private Templates $templates;
 private Patterns $patterns;
 private Send_Preview_Email $send_preview_email;
 private Personalization_Tags_Registry $personalization_tags_registry;
 public function __construct(
 Email_Api_Controller $email_api_controller,
 Templates $templates,
 Patterns $patterns,
 Send_Preview_Email $send_preview_email,
 Personalization_Tags_Registry $personalization_tags_controller
 ) {
 $this->email_api_controller = $email_api_controller;
 $this->templates = $templates;
 $this->patterns = $patterns;
 $this->send_preview_email = $send_preview_email;
 $this->personalization_tags_registry = $personalization_tags_controller;
 }
 public function initialize(): void {
 do_action( 'mailpoet_email_editor_initialized' );
 add_filter( 'mailpoet_email_editor_rendering_theme_styles', array( $this, 'extend_email_theme_styles' ), 10, 2 );
 $this->register_block_patterns();
 $this->register_email_post_types();
 $this->register_block_templates();
 $this->register_email_post_sent_status();
 $this->register_personalization_tags();
 $is_editor_page = apply_filters( 'mailpoet_is_email_editor_page', false );
 if ( $is_editor_page ) {
 $this->extend_email_post_api();
 }
 add_action( 'rest_api_init', array( $this, 'register_email_editor_api_routes' ) );
 add_filter( 'mailpoet_email_editor_send_preview_email', array( $this->send_preview_email, 'send_preview_email' ), 11, 1 ); // allow for other filter methods to take precedent.
 add_filter( 'single_template', array( $this, 'load_email_preview_template' ) );
 }
 private function register_block_templates(): void {
 // Since we cannot currently disable blocks in the editor for specific templates, disable templates when viewing site editor. @see https://github.com/WordPress/gutenberg/issues/41062.
 if ( strstr( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ), 'site-editor.php' ) === false ) {
 $post_types = array_column( $this->get_post_types(), 'name' );
 $this->templates->initialize( $post_types );
 }
 }
 private function register_block_patterns(): void {
 $this->patterns->initialize();
 }
 private function register_email_post_types(): void {
 foreach ( $this->get_post_types() as $post_type ) {
 register_post_type(
 $post_type['name'],
 array_merge( $this->get_default_email_post_args(), $post_type['args'] )
 );
 }
 }
 private function register_personalization_tags(): void {
 $this->personalization_tags_registry->initialize();
 }
 private function get_post_types(): array {
 $post_types = array();
 return apply_filters( 'mailpoet_email_editor_post_types', $post_types );
 }
 private function get_default_email_post_args(): array {
 return array(
 'public' => false,
 'hierarchical' => false,
 'show_ui' => true,
 'show_in_menu' => false,
 'show_in_nav_menus' => false,
 'supports' => array(
 'editor' => array(
 'default-mode' => 'template-locked',
 ),
 'title',
 'custom-fields', // 'custom-fields' is required for loading meta fields via API.
 ),
 'has_archive' => true,
 'show_in_rest' => true, // Important to enable Gutenberg editor.
 'default_rendering_mode' => 'template-locked',
 'publicly_queryable' => true, // required by the preview in new tab feature.
 );
 }
 private function register_email_post_sent_status(): void {
 $default_args = array(
 'public' => false,
 'exclude_from_search' => true,
 'internal' => true, // for now, we hide it, if we use the status in the listings we may flip this and following values.
 'show_in_admin_all_list' => false,
 'show_in_admin_status_list' => false,
 'private' => true, // required by the preview in new tab feature for sent post (newsletter). Posts are only visible to site admins and editors.
 );
 $args = apply_filters( 'mailpoet_email_editor_post_sent_status_args', $default_args );
 register_post_status(
 'sent',
 $args
 );
 }
 public function extend_email_post_api() {
 $email_post_types = array_column( $this->get_post_types(), 'name' );
 register_rest_field(
 $email_post_types,
 'email_data',
 array(
 'get_callback' => array( $this->email_api_controller, 'get_email_data' ),
 'update_callback' => array( $this->email_api_controller, 'save_email_data' ),
 'schema' => $this->email_api_controller->get_email_data_schema(),
 )
 );
 }
 public function register_email_editor_api_routes() {
 register_rest_route(
 'mailpoet-email-editor/v1',
 '/send_preview_email',
 array(
 'methods' => 'POST',
 'callback' => array( $this->email_api_controller, 'send_preview_email_data' ),
 'permission_callback' => function () {
 return current_user_can( 'edit_posts' );
 },
 )
 );
 register_rest_route(
 'mailpoet-email-editor/v1',
 '/get_personalization_tags',
 array(
 'methods' => 'GET',
 'callback' => array( $this->email_api_controller, 'get_personalization_tags' ),
 'permission_callback' => function () {
 return current_user_can( 'edit_posts' );
 },
 )
 );
 }
 public function extend_email_theme_styles( WP_Theme_JSON $theme, WP_Post $post ): WP_Theme_JSON {
 $email_theme = get_post_meta( $post->ID, self::MAILPOET_EMAIL_META_THEME_TYPE, true );
 if ( $email_theme && is_array( $email_theme ) ) {
 $theme->merge( new WP_Theme_JSON( $email_theme ) );
 }
 return $theme;
 }
 public function get_current_post() {
 if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
 $current_post = get_post( intval( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- data valid
 } else {
 $current_post = $GLOBALS['post'];
 }
 return $current_post;
 }
 public function load_email_preview_template( $template ) {
 $post = $this->get_current_post();
 if ( ! $post instanceof \WP_Post ) {
 return $template;
 }
 $current_post_type = $post->post_type;
 $email_post_types = array_column( $this->get_post_types(), 'name' );
 if ( ! in_array( $current_post_type, $email_post_types, true ) ) {
 return $template;
 }
 add_filter(
 'mailpoet_email_editor_preview_post_template_html',
 function () use ( $post ) {
 // Generate HTML content for email editor post.
 return $this->send_preview_email->render_html( $post );
 }
 );
 return __DIR__ . '/Templates/single-email-post-template.php';
 }
}

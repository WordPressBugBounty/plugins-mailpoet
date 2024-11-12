<?php
declare(strict_types = 1);
namespace MailPoet\EmailEditor\Engine\Patterns;
if (!defined('ABSPATH')) exit;
use MailPoet\EmailEditor\Utils\Cdn_Asset_Url;
class Patterns {
 private $namespace = 'mailpoet';
 protected $cdn_asset_url;
 public function __construct(
 Cdn_Asset_Url $cdn_asset_url
 ) {
 $this->cdn_asset_url = $cdn_asset_url;
 }
 public function initialize(): void {
 $this->register_block_pattern_category();
 $this->register_patterns();
 }
 private function register_block_pattern_category(): void {
 register_block_pattern_category(
 'mailpoet',
 array(
 'label' => _x( 'MailPoet', 'Block pattern category', 'mailpoet' ),
 'description' => __( 'A collection of email template layouts.', 'mailpoet' ),
 )
 );
 }
 private function register_patterns() {
 $this->register_pattern( 'default', new Library\Default_Content( $this->cdn_asset_url ) );
 $this->register_pattern( 'default-full', new Library\Default_Content_Full( $this->cdn_asset_url ) );
 }
 private function register_pattern( $name, $pattern ) {
 register_block_pattern( $this->namespace . '/' . $name, $pattern->get_properties() );
 }
}

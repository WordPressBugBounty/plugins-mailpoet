<?php
declare( strict_types = 1 );
namespace MailPoet\EmailEditor\Integrations\Core\Renderer\Blocks;
if (!defined('ABSPATH')) exit;
use MailPoet\EmailEditor\Engine\Settings_Controller;
class Text extends Abstract_Block_Renderer {
 protected function render_content( string $block_content, array $parsed_block, Settings_Controller $settings_controller ): string {
 // Do not render empty blocks.
 if ( empty( trim( wp_strip_all_tags( $block_content ) ) ) ) {
 return '';
 }
 $block_content = $this->adjustStyleAttribute( $block_content );
 $block_attributes = wp_parse_args(
 $parsed_block['attrs'] ?? array(),
 array(
 'textAlign' => 'left',
 'style' => array(),
 )
 );
 $html = new \WP_HTML_Tag_Processor( $block_content );
 $classes = 'email-text-block';
 if ( $html->next_tag() ) {
 // phpcs:ignore Generic.Commenting.DocComment.MissingShort -- used for phpstan
 $block_classes = $html->get_attribute( 'class' ) ?? '';
 $classes .= ' ' . $block_classes;
 // remove has-background to prevent double padding applied for wrapper and inner element.
 $block_classes = str_replace( 'has-background', '', $block_classes );
 // remove border related classes because we handle border on wrapping table cell.
 $block_classes = preg_replace( '/[a-z-]+-border-[a-z-]+/', '', $block_classes );
 // phpcs:ignore Generic.Commenting.DocComment.MissingShort -- used for phpstan
 $html->set_attribute( 'class', trim( $block_classes ) );
 $block_content = $html->get_updated_html();
 }
 $block_styles = $this->get_styles_from_block(
 array(
 'color' => $block_attributes['style']['color'] ?? array(),
 'spacing' => $block_attributes['style']['spacing'] ?? array(),
 'typography' => $block_attributes['style']['typography'] ?? array(),
 'border' => $block_attributes['style']['border'] ?? array(),
 )
 );
 $styles = array(
 'min-width' => '100%', // prevent Gmail App from shrinking the table on mobile devices.
 );
 $styles['text-align'] = 'left';
 if ( ! empty( $parsed_block['attrs']['textAlign'] ) ) { // in this case, textAlign needs to be one of 'left', 'center', 'right'.
 $styles['text-align'] = $parsed_block['attrs']['textAlign'];
 } elseif ( in_array( $parsed_block['attrs']['align'] ?? null, array( 'left', 'center', 'right' ), true ) ) {
 $styles['text-align'] = $parsed_block['attrs']['align'];
 }
 $compiled_styles = $this->compile_css( $block_styles['declarations'], $styles );
 $table_styles = 'border-collapse: separate;'; // Needed because of border radius.
 return sprintf(
 '<table
 role="presentation"
 border="0"
 cellpadding="0"
 cellspacing="0"
 width="100%%"
 style="%1$s"
 >
 <tr>
 <td class="%2$s" style="%3$s" align="%4$s">%5$s</td>
 </tr>
 </table>',
 esc_attr( $table_styles ),
 esc_attr( $classes ),
 esc_attr( $compiled_styles ),
 esc_attr( $styles['text-align'] ),
 $block_content
 );
 }
 private function adjustStyleAttribute( string $block_content ): string {
 $html = new \WP_HTML_Tag_Processor( $block_content );
 if ( $html->next_tag() ) {
 $element_style_value = $html->get_attribute( 'style' );
 $element_style = isset( $element_style_value ) ? strval( $element_style_value ) : '';
 // Padding may contain value like 10px or variable like var(--spacing-10).
 $element_style = preg_replace( '/padding[^:]*:.?[0-9a-z-()]+;?/', '', $element_style );
 // Remove border styles. We apply border styles on the wrapping table cell.
 $element_style = preg_replace( '/border[^:]*:.?[0-9a-z-()#]+;?/', '', strval( $element_style ) );
 // We define the font-size on the wrapper element, but we need to keep font-size definition here
 // to prevent CSS Inliner from adding a default value and overriding the value set by user, which is on the wrapper element.
 // The value provided by WP uses clamp() function which is not supported in many email clients.
 $element_style = preg_replace( '/font-size:[^;]+;?/', 'font-size: inherit;', strval( $element_style ) );
 // phpcs:ignore Generic.Commenting.DocComment.MissingShort -- used for phpstan
 $html->set_attribute( 'style', esc_attr( $element_style ) );
 $block_content = $html->get_updated_html();
 }
 return $block_content;
 }
}

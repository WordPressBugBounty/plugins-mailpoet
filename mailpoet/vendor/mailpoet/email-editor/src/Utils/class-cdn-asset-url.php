<?php
declare(strict_types = 1);
namespace MailPoet\EmailEditor\Utils;
if (!defined('ABSPATH')) exit;
class Cdn_Asset_Url {
 const CDN_URL = 'https://ps.w.org/mailpoet/';
 private $base_url;
 public function __construct(
 string $base_url
 ) {
 $this->base_url = $base_url;
 }
 public function generate_cdn_url( $path ) {
 $use_cdn = defined( 'MAILPOET_USE_CDN' ) ? MAILPOET_USE_CDN : true;
 return ( $use_cdn ? self::CDN_URL : $this->base_url . '/plugin_repository/' ) . "assets/$path";
 }
}

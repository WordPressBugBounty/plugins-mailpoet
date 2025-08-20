<?php
declare(strict_types = 1);
namespace Automattic\WooCommerce\EmailEditor\Engine\Logger;
if (!defined('ABSPATH')) exit;
class Default_Email_Editor_Logger implements Email_Editor_Logger_Interface {
 public const EMERGENCY = 'emergency';
 public const ALERT = 'alert';
 public const CRITICAL = 'critical';
 public const ERROR = 'error';
 public const WARNING = 'warning';
 public const NOTICE = 'notice';
 public const INFO = 'info';
 public const DEBUG = 'debug';
 private $log_file;
 public function __construct() {
 $this->log_file = defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ? WP_CONTENT_DIR . '/debug.log' : '';
 }
 public function emergency( string $message, array $context = array() ): void {
 $this->log( self::EMERGENCY, $message, $context );
 }
 public function alert( string $message, array $context = array() ): void {
 $this->log( self::ALERT, $message, $context );
 }
 public function critical( string $message, array $context = array() ): void {
 $this->log( self::CRITICAL, $message, $context );
 }
 public function error( string $message, array $context = array() ): void {
 $this->log( self::ERROR, $message, $context );
 }
 public function warning( string $message, array $context = array() ): void {
 $this->log( self::WARNING, $message, $context );
 }
 public function notice( string $message, array $context = array() ): void {
 $this->log( self::NOTICE, $message, $context );
 }
 public function info( string $message, array $context = array() ): void {
 $this->log( self::INFO, $message, $context );
 }
 public function debug( string $message, array $context = array() ): void {
 $this->log( self::DEBUG, $message, $context );
 }
 public function log( string $level, string $message, array $context = array() ): void {
 if ( ! $this->log_file ) {
 return;
 }
 $entry = sprintf(
 '[%s] %s: %s %s',
 gmdate( 'Y-m-d H:i:s' ),
 strtoupper( $level ),
 $message,
 ! empty( $context ) ? wp_json_encode( $context ) : ''
 );
 // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- This is a logging class, error_log is the intended functionality.
 error_log( $entry . PHP_EOL, 3, $this->log_file );
 }
}

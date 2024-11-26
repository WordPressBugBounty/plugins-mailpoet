<?php
declare(strict_types = 1);
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../../vendor/autoload.php';
$console = new \Codeception\Lib\Console\Output( array() );
if ( ! function_exists( 'esc_attr' ) ) {
 function esc_attr( $attr ) {
 return $attr;
 }
}
if ( ! function_exists( 'esc_html' ) ) {
 function esc_html( $text ) {
 return $text;
 }
}
abstract class MailPoetUnitTest extends \Codeception\TestCase\Test {
 protected $runTestInSeparateProcess = false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 protected $preserveGlobalState = false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
}
require '_stubs.php';

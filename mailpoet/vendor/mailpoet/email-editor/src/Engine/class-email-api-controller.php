<?php
declare(strict_types = 1);
namespace MailPoet\EmailEditor\Engine;
if (!defined('ABSPATH')) exit;
use MailPoet\EmailEditor\Validator\Builder;
use WP_Post;
class Email_Api_Controller {
 public function get_email_data(): array {
 // Here comes code getting Email specific data that will be passed on 'email_data' attribute.
 return array();
 }
 public function save_email_data( array $data, WP_Post $email_post ): void {
 // Here comes code saving of Email specific data that will be passed on 'email_data' attribute.
 }
 public function get_email_data_schema(): array {
 return Builder::object()->to_array();
 }
}

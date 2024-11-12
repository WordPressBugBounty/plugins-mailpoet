<?php
declare(strict_types = 1);
namespace MailPoet\EmailEditor\Engine\Patterns\Library;
if (!defined('ABSPATH')) exit;
use MailPoet\EmailEditor\Utils\Cdn_Asset_Url;
abstract class Abstract_Pattern {
 protected $cdn_asset_url;
 protected $block_types = array();
 protected $template_types = array();
 protected $inserter = true;
 protected $source = 'plugin';
 protected $categories = array( 'mailpoet' );
 protected $viewport_width = 620;
 public function __construct(
 Cdn_Asset_Url $cdn_asset_url
 ) {
 $this->cdn_asset_url = $cdn_asset_url;
 }
 public function get_properties(): array {
 return array(
 'title' => $this->get_title(),
 'content' => $this->get_content(),
 'description' => $this->get_description(),
 'categories' => $this->categories,
 'inserter' => $this->inserter,
 'blockTypes' => $this->block_types,
 'templateTypes' => $this->template_types,
 'source' => $this->source,
 'viewportWidth' => $this->viewport_width,
 );
 }
 abstract protected function get_content(): string;
 abstract protected function get_title(): string;
 protected function get_description(): string {
 return '';
 }
}

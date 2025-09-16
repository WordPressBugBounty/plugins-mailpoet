<?php
namespace Safe;
if (!defined('ABSPATH')) exit;
use Safe\Exceptions\XmlrpcException;
function xmlrpc_set_type(&$value, string $type): void
{
 error_clear_last();
 $result = \xmlrpc_set_type($value, $type);
 if ($result === false) {
 throw XmlrpcException::createFromPhpError();
 }
}

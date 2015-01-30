<?php
namespace Adminko;

use Adminko\Db\Db;

include_once dirname(__FILE__) . '/../config/config.php';

$exchange_xml = simplexml_load_file('http://www.cbr.ru/scripts/XML_daily.asp');
if (!$exchange_xml) {
    exit;
}

$dollar_xml = $exchange_xml->xpath('Valute[CharCode="USD"]');
if ( !$dollar_xml ) {
    exit;
}

$value = (string) $dollar_xml[0]->Value;
$value = str_replace(',', '.', $value);

if ( is_numeric( $value ) ) {
    Db::update('preference', array('preference_value' => $value), array('preference_name' => 'course'));
}
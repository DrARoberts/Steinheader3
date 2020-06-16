<?php
/**
 * NTP.SNAILS.EMAIL - Pinging Cron
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://syd.au.snails.email
 * @license         ACADEMIC APL 2 (https://sourceforge.net/u/chronolabscoop/wiki/Academic%20Public%20License%2C%20version%202.0/)
 * @license         GNU GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @package         emails-api
 * @since           1.1.11
 * @author          Dr. Simon Antony Roberts <simon@snails.email>
 * @version         1.1.11
 * @description		A REST API for the creation and management of emails/forwarders and domain name parks for email
 * @link            http://internetfounder.wordpress.com
 * @link            https://github.com/Chronolabs-Cooperative/Emails-API-PHP
 * @link            https://sourceforge.net/p/chronolabs-cooperative
 * @link            https://facebook.com/ChronolabsCoop
 * @link            https://twitter.com/ChronolabsCoop
 * 
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'apiconfig.php';

$json = json_decode(getURIData("http://simonaroberts.com.localhost/v1/online.json", 120, 120, array()), true);

foreach($json as $key => $values)
    if (!file_exists(dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . $values['hostname'].'.stien') || md5_file(dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . $values['hostname'].'.stien') != md5_file(dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . 'default.stien')) {
        copy(dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . 'default.stien', dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . $values['hostname'].'.stien');
        echo "\nGenerated/Refreshed: " . $values['hostname'].'.stien';
    }

/*
$json = json_decode(getURIData("http://simonaroberts.com.localhost/v1/offcompanies.json", 120, 120, array()), true);

foreach($json as $key => $values)
    if (!file_exists(dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . ($host = getBaseDomain('http://'.parse_url($values['companyurl'], PHP_URL_HOST))).'.stien') || md5_file(dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . $host.'.stien') != md5_file(dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . 'default.stien')) {
        copy(dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . 'default.stien', dirname(__DIR__) . DS . "Stienheader's" . DS . 'Simonaroberts.com' . DS . $host.'.stien');
        echo "\nGenerated/Refreshed: " . $host.'.stien';
    }*/
?>
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
require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'apimailer.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'apicache.php';
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('memory_limit', '512M');

$sending = array();

if ($addy = json_decode(file_get_contents(API_PATH . DS . "email-addresses.json"), true)) {
    
    foreach($addy as $type => $values) {
        foreach($values as $email => $time) {
            if ($time < time()) {
                $addy[$type][$email] = $sending["$type-$email"] = (time() + (3600 * 24 * mt_rand(14,27)) + (3600 * 24 * mt_rand(14,27) * (mt_rand(1,7) / mt_rand(2,5))));
                echo "Need to send to: " . $email . "\n";
            } 
        }
    }
    
    file_put_contents(API_PATH . DS . "email-addresses.json", json_encode($addy));;
}

if (count($sending) > 0) {
    echo "Sending Emails...";
    foreach($sending as $cachestr => $time) {
        $data = json_decode(file_get_contents(API_PATH . DS . "$cachestr.json"), true);
        if (isset($data['next']))
            $data['next'] = $time;
        file_put_contents(API_PATH . DS . "$cachestr.json", json_encode($data));
        require_once __DIR__ . DS . 'class' . DS . 'apimailer.php';
    
        $mailer = new APIMailer("simonxaies@gmail.com", "Chronolabs Cooperative Stien", "mail");
        $mailer->setHTML(true);
        $mailer->multimailer->setFrom("simonxaies@gmail.com", "Chronolabs Cooperative Stien");
        $mailer->multimailer->AltBody = strip_tags($mailer->multimailer->Body = $data['body']);
        $mailer->Subject = $data['subject'];
        foreach($data['tos'] as $name => $emailaddy)
            $mailer->multimailer->addAddress($emailaddy, $name);
        foreach($data['ccs'] as $name => $emailaddy)
            $mailer->multimailer->addCC($emailaddy, $name);
        foreach($data['bccs'] as $name => $emailaddy)
            $mailer->multimailer->addBCC($emailaddy, $name);
        foreach($data['attachments'] as $filename => $file)
            $mailer->multimailer->addAttachment($file, $filename);
        if ($mailer->multimailer->send()) {
            echo ".";
            if  (mt_rand(0, 1)!=1) { if  (mt_rand(0, 1)==1) { if  (mt_rand(0, 1)!=1) { if (mt_rand(0, 1)==1 ) { sleep(mt_rand(0, 1)); } } } }
        } else {
            echo 'x';
        }
    }
} else
    echo "No Emails Needed to be Sent...";

echo  "\n\n\n";
?>

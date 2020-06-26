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

$basedomains = array();
$realms = array();
$reset = array();
$compiler = array();
$srcflds = array();
ini_set('memory_limit', '512M');

mkdir (dirname(__DIR__) . DS . 'Stien Compilings', 0777, true);
echo "\n\nIndexing Stienheaders...";
foreach(explode("\n", file_get_contents(dirname(__DIR__) . DS . 'Stienheader\'s' . DS . 'send.txt')) as $headers) {
    $stienheaders = explode(": ", $headers);
    if (is_dir($folder = dirname(__DIR__) . DS . 'Stienheader\'s' . DS . $stienheaders[0])) {
        mkdir ($compiler[$stienheaders[0]] = dirname(__DIR__) . DS . 'Stien Compilings'. DS . 'Stienheader\'s'  . DS . $stienheaders[0], 0777, true);
        foreach (getFileListAsArray($folder, '.stien') as $file => $filepath) {
            if ($file!='default.stien') {
                if (getBaseDomain('http://'.substr($file, 0, strlen($file) - strlen('.stien'))) != substr($file, 0, strlen($file) - strlen('.stien'))) {
                    $domains = array();
                    $baseparts = explode('.', getBaseDomain('http://'.substr($file, 0, strlen($file) - strlen('.stien'))));
                    $totalparts = explode('.', substr($file, 0, strlen($file) - strlen('.stien')));
                    for($i=count($totalparts);$i == count($baseparts); $i--) {
                        unset($totalparts[count($totalparts) - $i]);
                        $domains[implode('.', $totalparts)] = implode('.', $totalparts);
                    }
                    foreach($domains as $tld => $hostname) {
                        $basedomains[$stienheaders[0]][$tld]["HEADER"][md5_file($folder . DS . 'default.stien')] = $folder . DS . 'default.stien';
                        $basedomains[$stienheaders[0]][$tld][$stienheaders[2]][md5_file($filepath)] = $filepath;
                    }
                }
                $domains = array();
                $domains[substr($file, 0, strlen($file) - strlen('.stien'))] = substr($file, 0, strlen($file) - strlen('.stien'));
                $baseparts = explode('.', substr($file, 0, strlen($file) - strlen('.stien')));
                $totalparts = explode('.', substr($file, 0, strlen($file) - strlen('.stien')));
                for($i=count($totalparts);$i == count($baseparts) + 1; $i--) {
                    unset($totalparts[count($totalparts) - $i]);
                    $domains[implode('.', $totalparts)] = implode('.', $totalparts);
                }
                foreach($domains as $tld => $realm) {
                    $realms[$stienheaders[0]][$tld]["HEADER"][md5_file($filepath)] = $filepath;
                    foreach (getFileListAsArray($folder, '.stien') as $sfile => $sfilepath) {
                        if ($sfile!='default.stien') 
                            if ($sfile != $file)
                                $realms[$stienheaders[0]][$tld][$stienheaders[1]][md5_file($sfilepath)] = $sfilepath;
                    }
                }
                echo ".";
            }
        }    
    }
}

echo "\n\nBuilding Directory Folder Tree's...";
foreach(explode("\n", file_get_contents(dirname(__DIR__) . DS . 'Stienheader\'s' . DS . 'send.txt')) as $headers) {
    $stienheaders = explode(": ", $headers);
    foreach($realms[$stienheaders[0]] as $realm => $values) {
        if (!is_dir(dirname(__DIR__) . DS . "Stien's"))
            mkdir(dirname(__DIR__) . DS . "Stien's", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0] . DS . "Default"))
            mkdir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0] . DS . "Default", 0777, true);
        $parts = array_reverse(explode(".", $realm));
        if (!is_dir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Zipped"))
            mkdir(dirname(__DIR__) . DS . "Zipped", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "vCards"))
            mkdir(dirname(__DIR__) . DS . "vCards", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Texts"))
            mkdir(dirname(__DIR__) . DS . "Texts", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Images"))
            mkdir(dirname(__DIR__) . DS . "Images", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "RAR's"))
            mkdir(dirname(__DIR__) . DS . "RAR's", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
       echo ".";
    }
    foreach($basedomains[$stienheaders[0]] as $realm => $values) {
        if (!is_dir(dirname(__DIR__) . DS . "Stien's"))
            mkdir(dirname(__DIR__) . DS . "Stien's", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0] . DS . "Default"))
            mkdir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0] . DS . "Default", 0777, true);
        $parts = array_reverse(explode(".", $realm));
        if (!is_dir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "Stien's" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Zipped"))
            mkdir(dirname(__DIR__) . DS . "Zipped", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "Zipped" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "vCards"))
            mkdir(dirname(__DIR__) . DS . "vCards", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "vCards" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Texts"))
            mkdir(dirname(__DIR__) . DS . "Texts", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "Texts" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Images"))
            mkdir(dirname(__DIR__) . DS . "Images", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "Images" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "RAR's"))
            mkdir(dirname(__DIR__) . DS . "RAR's", 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0]))
            mkdir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0], 0777, true);
        if (!is_dir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0] . DS . implode(DS, $parts)))
            mkdir(dirname(__DIR__) . DS . "RAR's" . DS . $stienheaders[0] . DS . implode(DS, $parts), 0777, true);
        
        echo ".";
    }
}
echo "\n\n\n";  

?>
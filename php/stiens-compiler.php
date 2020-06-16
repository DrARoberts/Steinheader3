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

echo "\n\nBuilding, Compiling Emails...";
foreach(explode("\n", file_get_contents(dirname(__DIR__) . DS . 'Stienheader\'s' . DS . 'send.txt')) as $headers) {
    $stienheaders = explode(": ", $headers);
    if (count(getDirListAsArray($compiler[$stienheaders[0]])) == 0 && (count($basedomains[$stienheaders[0]]) + count($realms[$stienheaders[0]])) > 0) {
        foreach($realms[$stienheaders[0]] as $realm => $values) {
            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
            if (!is_dir($folder = $compiler[$stienheaders[0]] . DS . 'stien@' . $realm))
                mkdir($folder, 0777, true);
            $reset['stienheaders']['stien@' . $realm]['tos'] = array($stienheaders[0] . ' Stienheaders' => 'stien@' . $realm);
            $reset['stienheaders']['stien@' . $realm]['ccs'] = array($stienheaders[0] . ' Webmaster' => 'webmaster@' . $realm, $stienheaders[0] . ' Support' => 'support@' . $realm);
            $reset['stienheaders']['stien@' . $realm]['bccs'] = array('Chronolabs Cooperative (bcc)' => 'chronolabscoop@users.sourceforge.net');
            $reset['stienheaders']['stien@' . $realm]['folder'] = $folder;
            $reset['stienheaders']['stien@' . $realm]['subject'] = "Stienheader's for " . $stienheaders[0] . ": " . $realm;
            $reset['stienheaders']['stien@' . $realm]['body'] = getEmailBodyHTMLFromREADME($reset['stienheaders']['stien@' . $realm]['subject'], $stienheaders[0], $realm);
            
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped'))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        if (file_exists($folder . DS . $srcfile . '.json'))
                            unlink ($folder . DS . $srcfile . '.json');
                            file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        if (file_exists($folder . DS . $srcfile . '.json'))
                            unlink ($folder . DS . $srcfile . '.json');
                            file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        if (file_exists($folder . DS . $srcfile . '.json'))
                            unlink ($folder . DS . $srcfile . '.json');
                            file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            file_put_contents($folder . DS . $realm.'.stien', getCompiledStien($stienheaders[0], $realm, $values, $folder));
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Stienheader\'s' . DS . 'Defaults')) 
                foreach (getFileListAsArray($srcfld, '.stien') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.stien') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards')) 
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.vcf') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards' . DS . $stienheaders[0])) 
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.vcf') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards' . DS . $stienheaders[0] . DS . $realm)) 
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.vcf') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' )) 
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                       copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' . DS . $stienheaders[0])) 
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' . DS . $stienheaders[0] . DS . $realm)) 
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images')) 
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images' . DS . $stienheaders[0])) 
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images' . DS . $stienheaders[0] . DS . $realm)) 
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath) 
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            
           echo ".";
        }
        foreach($basedomains[$stienheaders[0]] as $realm => $values) {
            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
            if (!is_dir($folder = $compiler[$stienheaders[0]] . DS . 'stien@' . $realm))
                mkdir($folder, 0777, true);
            $reset['stienheaders']['stien@' . $realm]['tos'] = array($stienheaders[0] . ' Stienheaders' => 'stien@' . $realm);
            $reset['stienheaders']['stien@' . $realm]['ccs'] = array($stienheaders[0] . ' Webmaster' => 'webmaster@' . $realm, $stienheaders[0] . ' Support' => 'support@' . $realm);
            $reset['stienheaders']['stien@' . $realm]['bccs'] = array('Chronolabs Cooperative (bcc)' => 'chronolabscoop@users.sourceforge.net');
            $reset['stienheaders']['stien@' . $realm]['folder'] = $folder;
            $reset['stienheaders']['stien@' . $realm]['subject'] = "Stienheader's for " . $stienheaders[0] . ": " . $realm;
            $reset['stienheaders']['stien@' . $realm]['body'] = getEmailBodyHTMLFromREADME($reset['stienheaders']['stien@' . $realm]['subject'], $stienheaders[0], $realm);
            
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped'))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        if (file_exists($folder . DS . $srcfile . '.json'))
                            unlink ($folder . DS . $srcfile . '.json');
                            file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        if (file_exists($folder . DS . $srcfile . '.json'))
                            unlink ($folder . DS . $srcfile . '.json');
                            file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        if (file_exists($folder . DS . $srcfile . '.json'))
                            unlink ($folder . DS . $srcfile . '.json');
                            file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            file_put_contents($folder . DS . $realm.'.stien', getCompiledStien($stienheaders[0], $realm, $values, $folder));
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Stienheader\'s' . DS . 'Defaults'))
                foreach (getFileListAsArray($srcfld, '.stien') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.stien') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards'))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' ))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images'))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        if (file_exists($folder . DS . $srcfile))
                            unlink ($folder . DS . $srcfile);
                        copy($srcfilepath, $folder . DS . $srcfile);
                    }
            echo ".";
        }
    } else {
        foreach($realms[$stienheaders[0]] as $realm => $values) {
            if (!is_dir($folder = $compiler[$stienheaders[0]] . DS . 'stien@' . $realm))
                mkdir($folder, 0777, true);
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped'))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        $srcflds['zip'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile . '.json')) {
                            $jdata = json_decode(file_get_contents($folder . DS . $srcfile . '.json'), true);
                            if ($jdata['md5'] != md5_file($srcfilepath)) 
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                            unlink ($folder . DS . $srcfile . '.json');
                        }
                        file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        $srcflds['zip'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile . '.json')) {
                            $jdata = json_decode(file_get_contents($folder . DS . $srcfile . '.json'), true);
                            if ($jdata['md5'] != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                            unlink ($folder . DS . $srcfile . '.json');
                        }
                        file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        $srcflds['zip'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile . '.json')) {
                            $jdata = json_decode(file_get_contents($folder . DS . $srcfile . '.json'), true);
                            if ($jdata['md5'] != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                            unlink ($folder . DS . $srcfile . '.json');
                        }
                        file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (md5_file($folder . DS . $realm.'.stien') != md5($stiendata = getCompiledStien($stienheaders[0], $realm, $values, $folder))) {
                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                file_put_contents($folder . DS . $realm.'.stien', $stiendata);
            }
                    
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Stienheader\'s' . DS . 'Defaults'))
                foreach (getFileListAsArray($srcfld, '.stien') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.stien') {
                        $srcflds['stien'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                            unlink ($folder . DS . $srcfile);
                            copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards'))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        $srcflds['vcf'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        $srcflds['vcf'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        $srcflds['vcf'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' ))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        $srcflds['txt'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        $srcflds['txt'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        $srcflds['txt'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images'))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.jpg') {
                        $srcflds['jpg'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.jpg') {
                        $srcflds['jpg'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.jpg') {
                        $srcflds['jpg'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
                
            $filez = array();
            foreach($srcflds as $ext => $folders)
                foreach($folders as $keyfld => $abspath) 
                    foreach (getFileListAsArray($abspath, '.'.$ext) as $srcfile => $srcfilepath)
                        $filez[$srcfile] = $srcfilepath;
                
            foreach (getFileListAsArray($folder, '') as $srcfile => $srcfilepath) 
                if (!in_array($srcfile, array_keys($filez)) && !in_array(str_replace('.json', '', $srcfile), array_keys($filez))) {
                    unlink($srcfilepath);
                    $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                }
                
            if (isset($reset['stienheaders']['stien@' . $realm]['next']) && !empty($reset['stienheaders']['stien@' . $realm]['next'])) {
                $reset['stienheaders']['stien@' . $realm]['tos'] = array($stienheaders[0] . ' Stienheaders' => 'stien@' . $realm);
                $reset['stienheaders']['stien@' . $realm]['ccs'] = array($realm . ' Webmaster' => 'webmaster@' . $realm, $realm . ' Support' => 'support@' . $realm);
                $reset['stienheaders']['stien@' . $realm]['bccs'] = array('Chronolabs Cooperative (bcc)' => 'chronolabscoop@users.sourceforge.net');
                $reset['stienheaders']['stien@' . $realm]['folder'] = $folder;
                $reset['stienheaders']['stien@' . $realm]['subject'] = "Updated: " . date("Y-m-d H:i"). " - Stienheader's for " . $stienheaders[0] . ": " . $realm;
                $reset['stienheaders']['stien@' . $realm]['body'] = getEmailBodyHTMLFromREADME($reset['stienheaders']['stien@' . $realm]['subject'], $stienheaders[0], $realm);
            }
            echo ".";
        }
        foreach($basedomains[$stienheaders[0]] as $realm => $values) {
            if (!is_dir($folder = $compiler[$stienheaders[0]] . DS . 'stien@' . $realm))
                mkdir($folder, 0777, true);
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped'))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        $srcflds['zip'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile . '.json')) {
                            $jdata = json_decode(file_get_contents($folder . DS . $srcfile . '.json'), true);
                            if ($jdata['md5'] != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile . '.json');
                        }
                        file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        $srcflds['zip'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile . '.json')) {
                            $jdata = json_decode(file_get_contents($folder . DS . $srcfile . '.json'), true);
                            if ($jdata['md5'] != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile . '.json');
                        }
                        file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Zipped' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.zip') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.zip') {
                        $srcflds['zip'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile . '.json')) {
                            $jdata = json_decode(file_get_contents($folder . DS . $srcfile . '.json'), true);
                            if ($jdata['md5'] != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile . '.json');
                        }
                        file_put_contents($folder . DS . $srcfile . '.json', json_encode(array('md5' => md5_file($srcfilepath), 'src' => $srcfilepath)));
                    }
            if (md5_file($folder . DS . $realm.'.stien') != md5($stiendata = getCompiledStien($stienheaders[0], $realm, $values, $folder))) {
                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                file_put_contents($folder . DS . $realm.'.stien', $stiendata);
            }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Stienheader\'s' . DS . 'Defaults'))
                foreach (getFileListAsArray($srcfld, '.stien') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.stien') {
                        $srcflds['stien'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards'))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        $srcflds['vcf'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        $srcflds['vcf'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'vCards' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.vcf') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.vcf') {
                        $srcflds['vcf'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' ))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        $srcflds['txt'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        $srcflds['txt'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Texts' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.txt') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.txt') {
                        $srcflds['txt'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images'))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.jpg') {
                        $srcflds['jpg'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images' . DS . $stienheaders[0]))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.jpg') {
                        $srcflds['jpg'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
            if (is_dir($srcfld = dirname(__DIR__) . DS . 'Images' . DS . $stienheaders[0] . DS . $realm))
                foreach (getFileListAsArray($srcfld, '.jpg') as $srcfile => $srcfilepath)
                    if ($srcfile!='default.jpg') {
                        $srcflds['jpg'][$srcfld] = $srcfld;
                        if (file_exists($folder . DS . $srcfile)) {
                            if (md5_file($folder . DS . $srcfile) != md5_file($srcfilepath))
                                $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                                unlink ($folder . DS . $srcfile);
                                copy($srcfilepath, $folder . DS . $srcfile);
                        } else {
                            copy($srcfilepath, $folder . DS . $srcfile);
                            $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                        }
                    }
                                                            
            $filez = array();
            foreach($srcflds as $ext => $folders)
                foreach($folders as $keyfld => $abspath)
                    foreach (getFileListAsArray($abspath, '.'.$ext) as $srcfile => $srcfilepath)
                        $filez[$srcfile] = $srcfilepath;
                        
            foreach (getFileListAsArray($folder, '') as $srcfile => $srcfilepath)
                if (!in_array($srcfile, array_keys($filez)) && !in_array(str_replace('.json', '', $srcfile), array_keys($filez))) {
                    unlink($srcfilepath);
                    $reset['stienheaders']['stien@' . $realm]['next'] = microtime(true);
                }
            
            if (isset($reset['stienheaders']['stien@' . $realm]['next']) && !empty($reset['stienheaders']['stien@' . $realm]['next'])) {
                $reset['stienheaders']['stien@' . $realm]['tos'] = array($stienheaders[0] . ' Stienheaders' => 'stien@' . $realm);
                $reset['stienheaders']['stien@' . $realm]['ccs'] = array($realm . ' Webmaster' => 'webmaster@' . $realm, $realm . ' Support' => 'support@' . $realm);
                $reset['stienheaders']['stien@' . $realm]['bccs'] = array('Chronolabs Cooperative (bcc)' => 'chronolabscoop@users.sourceforge.net');
                $reset['stienheaders']['stien@' . $realm]['folder'] = $folder;
                $reset['stienheaders']['stien@' . $realm]['subject'] = "Updated: " . date("Y-m-d H:i"). " - Stienheader's for " . $stienheaders[0] . ": " . $realm;
                $reset['stienheaders']['stien@' . $realm]['body'] = getEmailBodyHTMLFromREADME($reset['stienheaders']['stien@' . $realm]['subject'], $stienheaders[0], $realm);
            }
            echo ".";
        }
    }
}
echo "\n\n\n";
if (!is_dir(API_PATH))
    mkdir(API_PATH, 0777, true);

$addy = json_decode(file_get_contents(API_PATH . DS . "email-addresses.json"), true);
foreach($reset as $type => $values)
    foreach($values as $email => $values) {
        $data = json_decode(file_get_contents(API_PATH . DS . "$type-$email.json"), true);
        if (isset($values['next']))
            $addy[$type][$email] = $data['next'] = $values['next'];
        else 
            $addy[$type][$email] = $data['next'] = 0;
        if (isset($values['folder'])) {
            $data['folder'] = $values['folder'];
            $data['attachments'] = getEmailAttachmentsAsArray($values['folder']);
        }
        if (isset($values['body']))
            $data['body'] = $values['body'];
        if (isset($values['subject']))
            $data['subject'] = $values['subject'];
        if (isset($values['tos']))
            $data['tos'] = $values['tos'];
        if (isset($values['ccs']))
            $data['ccs'] = $values['ccs'];
        if (isset($values['bccs']))
            $data['bccs'] = array('Chronolabs Cooperative (bcc)' => 'chronolabscoop@users.sourceforge.net');
            
        file_put_contents(API_PATH . DS . "$type-$email.json", json_encode($data));
    }
file_put_contents(API_PATH . DS . "email-addresses.json", json_encode($addy));
?>
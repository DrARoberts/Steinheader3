<?php
/**
 * Email Account Propogation REST Services API
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


require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'apicache.php';


function reverseArrayEmailAttachment($attachments)
{
    $attach = array();
    foreach($attachments as $file => $filename) {
        $attach[$filename] = $file;
    }
    return $attach;
}

function checkEmail($email, $antispam = false)
{
    if (!$email || !preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
        return false;
    }
    $email_array      = explode('@', $email);
    $local_array      = explode('.', $email_array[0]);
    $local_arrayCount = count($local_array);
    for ($i = 0; $i < $local_arrayCount; ++$i) {
        if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/\=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/\=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
            return false;
        }
    }
    if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
        $domain_array = explode('.', $email_array[1]);
        if (count($domain_array) < 2) {
            return false; // Not enough parts to domain
        }
        for ($i = 0; $i < count($domain_array); ++$i) {
            if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                return false;
            }
        }
    }
    if ($antispam) {
        $email = str_replace('@', ' at ', $email);
        $email = str_replace('.', ' dot ', $email);
    }
    
    return $email;
}


if (!function_exists("getEmailBodyHTMLFromREADME")) {
    /**
     * Get a file listing for a single path no recursive
     *
     * @param string $dirname
     * @param string $prefix
     *
     * @return array
     */
    function getEmailBodyHTMLFromREADME($subject, $folder, $realm)
    {
       
        $readme = array();
        $readdata = array();
        if (is_dir($srcfld = dirname(dirname(__DIR__)) . DS . 'Stienheader\'s' . DS . 'Defaults'))
            foreach (getFileListAsArray($srcfld, 'EADME.md') as $srcfile => $srcfilepath)
                if ($srcfile!='DEFAULT-README.md') {
                    if (file_exists($srcfilepath))
                        $readdata = explode("\n", file_get_contents($srcfilepath));
                }
        if (is_dir($srcfld = dirname(dirname(__DIR__)) . DS . 'Stienheader\'s' . DS . $folder))
            foreach (getFileListAsArray($srcfld, 'EADME.md') as $srcfile => $srcfilepath)
                if ($srcfile!='DEFAULT-README.md') {
                    if (file_exists($srcfilepath))
                        $readdata = explode("\n", file_get_contents($srcfilepath));
                }
        if (is_dir($srcfld = dirname(dirname(__DIR__)) . DS . 'Readme\'s'))
            foreach (getFileListAsArray($srcfld, 'EADME.md') as $srcfile => $srcfilepath)
                if ($srcfile!='DEFAULT-README.md') {
                    if (file_exists($srcfilepath))
                        $readdata = explode("\n", file_get_contents($srcfilepath));
                }
        if (is_dir($srcfld = dirname(dirname(__DIR__)) . DS . 'Readme\'s' . DS . $folder))
            foreach (getFileListAsArray($srcfld, 'EADME.md') as $srcfile => $srcfilepath)
                if ($srcfile!='DEFAULT-README.md') {
                    if (file_exists($srcfilepath))
                        $readdata = explode("\n", file_get_contents($srcfilepath));
                }
        if (is_dir($srcfld = dirname(dirname(__DIR__)) . DS . 'Readme\'s' . DS . $folder . DS . $realm))
            foreach (getFileListAsArray($srcfld, 'EADME.md') as $srcfile => $srcfilepath)
                if ($srcfile!='DEFAULT-README.md') {
                    if (file_exists($srcfilepath))
                        $readdata = explode("\n", file_get_contents($srcfilepath));
                }
        $readme[] = "<h1>$subject</h1>";
        $readme[] = "";
        $pre = $ul = $p = false;
        foreach($readdata as $ln => $value) {
            if (substr($value, 0, 2) == "# ") {
                if ($ul==true) {
                    $readme[] = "</ul>";
                    $ul = false;
                }
                if ($p==true) {
                    $readme[] = "</p>";
                    $p = false;
                }
                if ($pre==true) {
                    $pre = false;
                    $readme[] = "</code></pre>";
                }
                $readme[] = "<h1>" . substr($value, 2) . "</h1>";
            } elseif (substr($value, 0, 3) == "## ") {
                if ($ul==true) {
                    $readme[] = "</ul>";
                    $ul = false;
                }
                if ($p==true) {
                    $readme[] = "</p>";
                    $p = false;
                }
                if ($pre==true) {
                    $pre = false;
                    $readme[] = "</code></pre>";
                }
                $readme[] = "<h2>" . substr($value, 3) . "</h2>";
            } elseif (substr($value, 0, 4) == "### ") {
                if ($ul==true) {
                    $readme[] = "</ul>";
                    $ul = false;
                }
                if ($p==true) {
                    $readme[] = "</p>";
                    $p = false;
                }
                if ($pre==true) {
                    $pre = false;
                    $readme[] = "</code></pre>";
                }
                $readme[] = "<h3>" . substr($value, 4) . "</h3>";
            } elseif (substr($value, 0, 5) == "#### ") {
                if ($ul==true) {
                    $readme[] = "</ul>";
                    $ul = false;
                }
                if ($p==true) {
                    $readme[] = "</p>";
                    $p = false;
                }
                if ($pre==true) {
                    $pre = false;
                    $readme[] = "</code></pre>";
                }
                $readme[] = "<h4>" . substr($value, 4) . "</h4>";
            } elseif (substr($value, 0, 6) == "##### ") {
                if ($ul==true) {
                    $readme[] = "</ul>";
                    $ul = false;
                }
                if ($p==true) {
                    $readme[] = "</p>";
                    $p = false;
                }
                if ($pre==true) {
                    $pre = false;
                    $readme[] = "</code></pre>";
                }
                $readme[] = "<h5>" . substr($value, 4) . "</h5>";
            } elseif (substr($value, 0, 4) == "    ") {
                if ($ul==true) {
                    $readme[] = "</ul>";
                    $ul = false;
                }
                if ($p==true) {
                    $readme[] = "</p>";
                    $p = false;
                }
                if ($pre == false) {
                    $pre = true;
                    $readme[] = "<pre><code>";
                }
                $readme[] = substr($value, 4);
            } elseif (substr($value, 0, 3) == " * ") {
                if ($pre==true) {
                    $readme[] = "</code></pre>";
                    $pre = false;
                }
                if ($ul==false) {
                    $readme[] = "<ul>";
                    $ul = true;
                }
                $readme[] = "<li>" . substr($value, 3) . "</li>";
            } else {
                if ($pre==true) {
                    $readme[] = "</code></pre>";
                    $pre = false;
                }
                if ($ul==true) {
                    $readme[] = "</ul>";
                    $ul = false;
                }
                if ($p==false) {
                    $readme[] = "<p>";
                    $p=true;
                    $readme[] = $value . "<br />";
                } else {
                        $readme[] = $value . "<br />";
                }
            
            }
        }
        
        if ($ul==true) {
            $readme[] = "</ul>";
            $ul = false;
        }
        if ($p==true) {
            $readme[] = "</p>";
            $p = false;
        }
        if ($pre==true) {
            $pre = false;
            $readme[] = "</code></pre>";
        }

        require_once dirname(__DIR__) . DS . 'class' . DS . 'module.textsanitizer.php';
        $mytext = new MyTextSanitizer();
        
        return $mytext->displayTarea(implode("\n", $readme), true, false, false, false, false);
        
    }
}
    
if (!function_exists("getEmailAttachmentsAsArray")) {
    /**
     * Get a file listing for a single path no recursive
     *
     * @param string $dirname
     * @param string $prefix
     *
     * @return array
     */
    function getEmailAttachmentsAsArray($dirname, $srcfiles = array())
    {
        if (empty($srcfiles))
            $srcfiles = array('.zip.json');
        
        $filelist = array();
        if (substr($dirname, - 1) == '/') {
            $dirname = substr($dirname, 0, - 1);
        }
        if (is_dir($dirname) && $handle = opendir($dirname)) {
            while (false !== ($file = readdir($handle))) {
                if (! preg_match('/^[\.]{1,2}$/', $file) && is_file($dirname . '/' . $file)) {
                    $added=false;
                    foreach($srcfiles as $fileext)
                        if (substr($file, strlen($file) - strlen($fileext)) == $fileext) {
                            $json = json_decode(file_get_contents($dirname . '/' . $file), true);
                            if (isset($json['src']) && !empty($json['src'])) {
                                $filelist[basename($json['src'])] = $json['src'];
                                $added = true;
                            }
                        }
                    if ($added != true)
                        $filelist[$file] = $dirname . '/' . $file;
                }
            }
            closedir($handle);
            asort($filelist);
            reset($filelist);
        }
        return $filelist;
    }
}


if (!function_exists("getFileListAsArray")) {
    /**
     * Get a file listing for a single path no recursive
     *
     * @param string $dirname
     * @param string $prefix
     *
     * @return array
     */
    function getFileListAsArray($dirname, $suffix = '')
    {
        $filelist = array();
        if (substr($dirname, - 1) == '/') {
            $dirname = substr($dirname, 0, - 1);
        }
        if (is_dir($dirname) && $handle = opendir($dirname)) {
            while (false !== ($file = readdir($handle))) {
                if (! preg_match('/^[\.]{1,2}$/', $file) && is_file($dirname . '/' . $file) && (!empty($suffix) && substr($file, strlen($file) - strlen($suffix)) == $suffix)) {
                    $filelist[$file] = $dirname . '/' . $file;
                }
            }
            closedir($handle);
            asort($filelist);
            reset($filelist);
        }
        return $filelist;
    }
}


if (!function_exists("getDirListAsArray")) {
    /**
     * Get a folder listing for a single path no recursive
     *
     * @param string $dirname
     *
     * @return array
     */
    function getDirListAsArray($dirname)
    {
        $ignored = array(
            'cvs' ,
            '_darcs', '.git', '.svn');
        $list = array();
        if (substr($dirname, - 1) != '/') {
            $dirname .= '/';
        }
        if ($handle = opendir($dirname)) {
            while ($file = readdir($handle)) {
                if (substr($file, 0, 1) == '.' || in_array(strtolower($file), $ignored))
                    continue;
                    if (is_dir($dirname . $file)) {
                        $list[$file] = $file;
                    }
            }
            closedir($handle);
            asort($list);
            reset($list);
        }
        return $list;
    }
}


if (!function_exists("getCompiledStien")) {
    /**
     * Get a folder listing for a single path no recursive
     *
     * @param string $dirname
     *
     * @return array
     */
    function getCompiledStien($domaincategory, $domain, $stienscombined, $compilingpath)
    {
        $zips = getFileListAsArray($compilingpath, '.zip.json');
        $ending = explode("\n", file_get_contents(__DIR__ . DS . 'data' . DS . 'ending-headers.diz'));
        $stien = "";
        foreach($stienscombined as $typal => $filez) {
            switch ($typal) {
                case "HEADER":
                    $i=0;
                    foreach( $filez as $md5 => $file) {
                        $i++;
                        if ($i > 1)
                            $stien .= "\n";
                        $stientmp .= str_replace('Default', $domain, str_replace('DefaultCategory', $domaincategory, file_get_contents($file)));
                    }
                    $stiendata = array();
                    $combo = explode("\n", $stientmp);
                    $ended = false;
                    foreach($combo as $line => $value) {
                        if ($ended != true) {
                            foreach($ending as $kline => $kvalue) {
                                if ($value == $kvalue)
                                    $ended--;
                            }
                            $stiendata[] = $combo[$line];
                            unset($combo[$line]);
                            if ($ended == -2)
                                $ended = true;
                        }
                        if ($ended==true)
                            continue;
                    }
                    foreach($zips as $zfile => $zfilepath) {
                        $stiendata[] = ' -- [ ' . str_replace(array("-", "_", '.zip.json'), ' ', $zfile) . "Code Templates (Attachment: " . str_replace(array('.zip.json'), '.zip', $zfile) . ") ]:[\+\]:[/+/]";
                        $stiendata[] = '    -- [ TRXAYE ]:[ Click ]';
                    }
                    $stien = implode("\n", $stiendata) . "\n" . implode("\n", $combo);
                    break;
                case "PIVOT ABOVE":
                case "PIVOT BELOW":
                    $stien .= "\n::[ " . $typal . " ]::";
                case "EMBEDMENT":
                case "FLOATING":
                    $stien .= "\n";
                    $stien .= getCharteredStien($typal, $domaincategory, $domain, $filez);
                    break;
            }
                
        }
        return $stien;
        
    }
}

if (!function_exists("getCharteredStien")) {
    /**
     * Get a folder listing for a single path no recursive
     *
     * @param string $dirname
     *
     * @return array
     */
    function getCharteredStien($typal, $domaincategory, $domain, $stienscombined)
    {
        $stiens = array();
        $ending = explode("\n", file_get_contents(__DIR__ . DS . 'data' . DS . 'ending-headers.diz'));
        
        foreach($stienscombined as $md5 => $file) {
            $combo = explode("\n", file_get_contents($file));
            $ended = false;
            foreach($combo as $line => $value) {
                if ($ended != true) {
                    foreach($ending as $kline => $kvalue) {
                        if ($value == $kvalue)
                            $ended = -1;
                    }
                    unset($combo[$line]);
                    if ($ended == -1)
                        $ended = true;
                }
                if ($ended==true)
                    continue;
            }
            $stienparts = array();
            switch ($typal) {
                case "PIVOT ABOVE":
                case "PIVOT BELOW":
                case "FLOATING":
                    $stienparts = $combo;
                    break;
                    
                case "EMBEDMENT":
                    $stienparts[] = " -- [ Embed $domaincategory: $domain ]:[\+\]:[/+/]";
                    $stienparts[] = "     -- TRXAYE Key [ Click Here ]:[+]";
                    foreach($combo as $line => $value)
                        if (strpos($value, "--"))
                            $stienparts[] = "        $value";
                        else 
                            $stienparts[] = "     -- $value";
                    break;
            }
            $stiens[] = implode("\n", $stienparts);
        }
        return implode("\n", $stiens);
    }
}

if (!function_exists('sef'))
{
    
    function sef($value = '', $stripe ='-')
    {
        return yonkOnlyAlphanumeric($value, $stripe);
    }
}


if (!function_exists('yonkOnlyAlphanumeric'))
{
    
    function yonkOnlyAlphanumeric($value = '', $stripe ='-')
    {
        $replacement_chars = array();
        $accepted = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","m","o","p","q",
            "r","s","t","u","v","w","x","y","z","0","9","8","7","6","5","4","3","2","1");
        for($i=0;$i<256;$i++){
            if (!in_array(strtolower(chr($i)),$accepted))
                $replacement_chars[] = chr($i);
        }
        $result = trim(str_replace($replacement_chars, $stripe, strtolower($value)));
        while(strpos($result, $stripe.$stripe, 0))
            $result = (str_replace($stripe.$stripe, $stripe, $result));
        while(substr($result, 0, strlen($stripe)) == $stripe)
            $result = substr($result, strlen($stripe), strlen($result) - strlen($stripe));
        while(substr($result, strlen($result) - strlen($stripe), strlen($stripe)) == $stripe)
            $result = substr($result, 0, strlen($result) - strlen($stripe));
        return($result);
    }
}


if (!function_exists("getURIData")) {
    
    /* function yonkURIData()
     *
     * 	Get a supporting domain system for the API
     * @author 		Simon Roberts (Chronolabs) simon@labs.coop
     *
     * @return 		float()
     */
    function getURIData($uri = '', $timeout = 25, $connectout = 25, $post = array(), $headers = array())
    {
        if (!function_exists("curl_init"))
        {
            die("Install PHP Curl Extension ie: $ sudo apt-get install php-curl -y");
        }
        $GLOBALS['php-curl'][md5($uri)] = array();
        if (!$btt = curl_init($uri)) {
            return false;
        }
        if (count($post)==0 || empty($post))
            curl_setopt($btt, CURLOPT_POST, false);
        else {
            $uploadfile = false;
            foreach($post as $field => $value)
                if (substr($value , 0, 1) == '@' && !file_exists(substr($value , 1, strlen($value) - 1)))
                    unset($post[$field]);
                else
                    $uploadfile = true;
            curl_setopt($btt, CURLOPT_POST, true);
            curl_setopt($btt, CURLOPT_POSTFIELDIRECTORY_SEPARATOR, http_build_query($post));
            
            if (!empty($headers))
                foreach($headers as $key => $value)
                    if ($uploadfile==true && substr($value, 0, strlen('Content-Type:')) == 'Content-Type:')
                        unset($headers[$key]);
            if ($uploadfile==true)
                $headers[]  = 'Content-Type: multipart/form-data';
        }
        if (count($headers)==0 || empty($headers)) {
            curl_setopt($btt, CURLOPT_HEADER, false);
            curl_setopt($btt, CURLOPT_HTTPHEADER, array());
        } else {
            curl_setopt($btt, CURLOPT_HEADER, false);
            curl_setopt($btt, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($btt, CURLOPT_CONNECTTIMEOUT, $connectout);
        curl_setopt($btt, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($btt, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($btt, CURLOPT_VERBOSE, false);
        curl_setopt($btt, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($btt, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($btt);
        $GLOBALS['php-curl'][md5($uri)]['http']['uri'] = $uri;
        $GLOBALS['php-curl'][md5($uri)]['http']['posts'] = $post;
        $GLOBALS['php-curl'][md5($uri)]['http']['headers'] = $headers;
        $GLOBALS['php-curl'][md5($uri)]['http']['code'] = curl_getinfo($btt, CURLINFO_HTTP_CODE);
        $GLOBALS['php-curl'][md5($uri)]['header']['size'] = curl_getinfo($btt, CURLINFO_HEADER_SIZE);
        $GLOBALS['php-curl'][md5($uri)]['header']['value'] = curl_getinfo($btt, CURLINFO_HEADER_OUT);
        $GLOBALS['php-curl'][md5($uri)]['size']['download'] = curl_getinfo($btt, CURLINFO_SIZE_DOWNLOAD);
        $GLOBALS['php-curl'][md5($uri)]['size']['upload'] = curl_getinfo($btt, CURLINFO_SIZE_UPLOAD);
        $GLOBALS['php-curl'][md5($uri)]['content']['length']['download'] = curl_getinfo($btt, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $GLOBALS['php-curl'][md5($uri)]['content']['length']['upload'] = curl_getinfo($btt, CURLINFO_CONTENT_LENGTH_UPLOAD);
        $GLOBALS['php-curl'][md5($uri)]['content']['type'] = curl_getinfo($btt, CURLINFO_CONTENT_TYPE);
        curl_close($btt);
        return $data;
    }
}

/**
 * validateMD5()
 * Validates an MD5 Checksum
 *
 * @param string $email
 * @return boolean
 */

if (!function_exists("validateMD5")) {
    function validateMD5($md5) {
        if(preg_match("/^[a-f0-9]{32}$/i", $md5)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * validateEmail()
 * Validates an Email Address
 *
 * @param string $email
 * @return boolean
 */
if (!function_exists("validateEmail")) {
    function validateEmail($email) {
        if(preg_match("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.([0-9]{1,3})|([a-zA-Z]{2,3})|(aero|coop|info|mobi|asia|museum|name|edu))$", $email)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * validateDomain()
 * Validates a Domain Name
 *
 * @param string $domain
 * @return boolean
 */
if (!function_exists("validateDomain")) {
    function validateDomain($domain) {
        if(!preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i", $domain)) {
            return false;
        }
        return $domain;
    }
}

/**
 * validateIPv4()
 * Validates and IPv6 Address
 *
 * @param string $ip
 * @return boolean
 */
if (!function_exists("validateIPv4")) {
    function validateIPv4($ip) {
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === FALSE) // returns IP is valid
        {
            return false;
        } else {
            return true;
        }
    }
}
/**
 * validateIPv6()
 * Validates and IPv6 Address
 *
 * @param string $ip
 * @return boolean
 */
if (!function_exists("validateIPv6")) {
    function validateIPv6($ip) {
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE) // returns IP is valid
        {
            return false;
        } else {
            return true;
        }
    }
}


if (!function_exists("formatMSASTime")) {
    
    /* function formatMSASTime()
     *
     * @author 		Simon Roberts (Chronolabs) simon@ordinance.space
     *
     * @return 		float()
     */
    function formatMSASTime($milliseconds = '')
    {
        $return = '';
        $milliseconds = $milliseconds;
        if (($milliseconds / (3600 * 24 * 7 * 4 * 12)) >= 1)
        {
            $scratch = (string)($milliseconds / (3600 * 24 * 7 * 4 * 12));
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' year' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * (3600 * 24 * 7 * 4 * 12);
        }
        if (($milliseconds / (3600 * 24 * 7 * 4)) >= 1)
        {
            $scratch = (string)($milliseconds / (3600 * 24 * 7 * 4));
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' month' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * (3600 * 24 * 7 * 4);
        }
        if (($milliseconds / (3600 * 24 * 7)) >= 1)
        {
            $scratch = (string)($milliseconds /(3600 * 24 * 7));
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' week' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * (3600 * 24 * 7);
        }
        if (($milliseconds / (3600*24)) >= 1)
        {
            $scratch = (string)($milliseconds / (3600 * 24));
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' day' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * (3600 * 24);
        }
        if (($milliseconds / 3600) >= 1)
        {
            $scratch = (string)($milliseconds / 3600);
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' hour' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * 3600;
        }
        if (($milliseconds / 60) >= 1)
        {
            $scratch = (string)($milliseconds / 60);
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' min' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * 60;
        }
        if (($milliseconds / 60) >= 1)
        {
            $scratch = (string)($milliseconds / 60);
            $parts = explode(".", $scratch);
            $return .= $parts[0] . ' sec' .($parts[0]>1?"s ":" ");
            $milliseconds = ((float)("0." . $parts[1])) * 60;
        }
        if (empty($return))
            $return = 'No Time Passed!';
        
        return $return = trim($return);
    }
}

if (!class_exists("XmlDomConstruct")) {
	/**
	 * class XmlDomConstruct
	 * 
	 * 	Extends the DOMDocument to implement personal (utility) methods.
	 *
	 * @author 		Simon Roberts (Chronolabs) simon@labs.coop
	 */
	class XmlDomConstruct extends DOMDocument {
	
		/**
		 * Constructs elements and texts from an array or string.
		 * The array can contain an element's name in the index part
		 * and an element's text in the value part.
		 *
		 * It can also creates an xml with the same element tagName on the same
		 * level.
		 *
		 * ex:
		 * <nodes>
		 *   <node>text</node>
		 *   <node>
		 *     <field>hello</field>
		 *     <field>world</field>
		 *   </node>
		 * </nodes>
		 *
		 * Array should then look like:
		 *
		 * Array (
		 *   "nodes" => Array (
		 *     "node" => Array (
		 *       0 => "text"
		 *       1 => Array (
		 *         "field" => Array (
		 *           0 => "hello"
		 *           1 => "world"
		 *         )
		 *       )
		 *     )
		 *   )
		 * )
		 *
		 * @param mixed $mixed An array or string.
		 *
		 * @param DOMElement[optional] $domElement Then element
		 * from where the array will be construct to.
		 * 
		 * @author 		Simon Roberts (Chronolabs) simon@labs.coop
		 *
		 */
		public function fromMixed($mixed, DOMElement $domElement = null) {
	
			$domElement = is_null($domElement) ? $this : $domElement;
	
			if (is_array($mixed)) {
				foreach( $mixed as $index => $mixedElement ) {
	
					if ( is_int($index) ) {
						if ( $index == 0 ) {
							$node = $domElement;
						} else {
							$node = $this->createElement($domElement->tagName);
							$domElement->parentNode->appendChild($node);
						}
					}
					 
					else {
						$node = $this->createElement($index);
						$domElement->appendChild($node);
					}
					 
					$this->fromMixed($mixedElement, $node);
					 
				}
			} else {
				$domElement->appendChild($this->createTextNode($mixed));
			}
			 
		}
		 
	}
}

function api_load($name, $type = 'core')
{
    if (!class_exists('XoopsLoad')) {
        require_once API_ROOT_PATH . '/class/apiload.php';
    }
    
    return APILoad::load($name, $type);
}



if (!function_exists("getBaseDomain")) {
    /**
     * Gets the base domain of a tld with subdomains, that is the root domain header for the network rout
     *
     * @param string $url
     *
     * @return string
     */
    function getBaseDomain($uri = '')
    {
        
        //static $fallout, $strata, $classes;

        if (empty($classes))
        {
            
            $attempts = 0;
            $attempts++;
            $classes = array_keys(json_decode(file_get_contents(__DIR__ . DS . "data" . DS . "strata.classes.default.json"), true));
            
        }
        if (empty($fallout))
        {
            $fallout = array_keys(json_decode(file_get_contents(__DIR__ . DS . "data" . DS . "strata.fallouts.default.json"), true));
        }
        
        // Get Full Hostname
        $uri = strtolower($uri);
        $hostname = parse_url($uri, PHP_URL_HOST);
        if (!filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 || FILTER_FLAG_IPV4) === false)
            return $hostname;
        
        // break up domain, reverse
        $elements = explode('.', $hostname);
        $elements = array_reverse($elements);
        
        // Returns Base Domain
        if (in_array($elements[0], $fallout) && in_array($elements[1], $classes))
            return $elements[2] . '.' . $elements[1] . '.' . $elements[0];
        elseif (in_array($elements[0], $classes))
            return $elements[1] . '.' . $elements[0];
        elseif (in_array($elements[0], $fallout))
            return  $elements[1] . '.' . $elements[0];
        else
            return  $elements[1] . '.' . $elements[0];
    }
}

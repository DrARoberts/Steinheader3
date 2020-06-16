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


include_once  API_ROOT_PATH . '/class/apiform/formelement.php';
include_once  API_ROOT_PATH . '/class/apiform/form.php';
include_once  API_ROOT_PATH . '/class/apiform/formlabel.php';
include_once  API_ROOT_PATH . '/class/apiform/formselect.php';
include_once  API_ROOT_PATH . '/class/apiform/formpassword.php';
include_once  API_ROOT_PATH . '/class/apiform/formbutton.php';
include_once  API_ROOT_PATH . '/class/apiform/formbuttontray.php';
include_once  API_ROOT_PATH . '/class/apiform/formcheckbox.php';
include_once  API_ROOT_PATH . '/class/apiform/formselectcheckgroup.php';
include_once  API_ROOT_PATH . '/class/apiform/formhidden.php';
include_once  API_ROOT_PATH . '/class/apiform/formfile.php';
include_once  API_ROOT_PATH . '/class/apiform/formradio.php';
include_once  API_ROOT_PATH . '/class/apiform/formradioyn.php';
include_once  API_ROOT_PATH . '/class/apiform/formselectcountry.php';
include_once  API_ROOT_PATH . '/class/apiform/formselecttimezone.php';
include_once  API_ROOT_PATH . '/class/apiform/formselectlang.php';
include_once  API_ROOT_PATH . '/class/apiform/formselectgroup.php';
include_once  API_ROOT_PATH . '/class/apiform/formselectuser.php';
include_once  API_ROOT_PATH . '/class/apiform/formselecttheme.php';
include_once  API_ROOT_PATH . '/class/apiform/formselectmatchoption.php';
include_once  API_ROOT_PATH . '/class/apiform/formtext.php';
include_once  API_ROOT_PATH . '/class/apiform/formtextarea.php';
include_once  API_ROOT_PATH . '/class/apiform/formdhtmltextarea.php';
include_once  API_ROOT_PATH . '/class/apiform/formelementtray.php';
include_once  API_ROOT_PATH . '/class/apiform/themeform.php';
include_once  API_ROOT_PATH . '/class/apiform/simpleform.php';
include_once  API_ROOT_PATH . '/class/apiform/formtextdateselect.php';
include_once  API_ROOT_PATH . '/class/apiform/formdatetime.php';
include_once  API_ROOT_PATH . '/class/apiform/formhiddentoken.php';
include_once  API_ROOT_PATH . '/class/apiform/formcolorpicker.php';
include_once  API_ROOT_PATH . '/class/apiform/formcaptcha.php';
include_once  API_ROOT_PATH . '/class/apiform/formeditor.php';
include_once  API_ROOT_PATH . '/class/apiform/formselecteditor.php';
include_once  API_ROOT_PATH . '/class/apiform/formcalendar.php';
include_once  API_ROOT_PATH . '/class/apiform/renderer/APIFormRenderer.php';
include_once  API_ROOT_PATH . '/class/apiform/renderer/APIFormRendererInterface.php';
include_once  API_ROOT_PATH . '/class/apiform/renderer/APIFormRendererLegacy.php';
include_once  API_ROOT_PATH . '/class/apiform/renderer/APIFormRendererBootstrap3.php';
<?php

/**
 * G4HDU Birthday Menu plugin
 *
 * Copyright (C) 2008-2016 Barry Keal G4HDU http://e107.keal.me.uk
 * blankd under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * @author Barry Keal e107@keal.me.uk>
 * @copyright Copyright (C) 2008-2016 Barry Keal G4HDU
 * @license GPL
 * @version 1.0.0
 *
 * @todo
 */


// ***************************************************************
// *
// *		Plugin		:	Birthday Menu (e107 v2)
// *
// ***************************************************************
require_once ("../../class2.php");
if (!getperms("P"))
{
    header("location:" . e_BASE . "index.php");
    exit;
}
e107::lan('birthday', 'admin', true);
require_once ("e_version.php");
/**
 * plugin_birthday_admin
 * 
 * @package   
 * @author Birthday
 * @copyright Father Barry
 * @version 2016
 * @access public
 */
class plugin_birthday_admin extends e_admin_dispatcher
{
    protected $modes = array('main' => array(
            'controller' => 'plugin_birthday_admin_ui',
            'path' => null,
            'ui' => 'plugin_birthday_admin_form_ui',
            'uipath' => null));

    /**
     *
     * @var array
     */
    protected $adminMenu = array('main/prefs' => array('caption' => 'Settings',
                'perm' => '0'));

    /**
     * Optional, mode/action aliases, related with 'selected' menu CSS class
     * Format: 'MODE/ACTION' => 'MODE ALIAS/ACTION ALIAS';
     * This will mark active main/list menu item, when current page is main/edit
     * @var array
     */
    protected $adminMenuAliases = array('main/edit' => 'main/list');

    /**
     * Navigation menu title
     * @var string
     */
    protected $menuTitle = BIRTHDAY_ADMIN_NAME;
}


/**
 * plugin_birthday_admin_ui
 * 
 * @package   
 * @author Birthday
 * @copyright Father Barry
 * @version 2016
 * @access public
 */
class plugin_birthday_admin_ui extends e_admin_ui
{

    protected $pluginTitle = BIRTHDAY_ADMIN_NAME;

    /**
     * plugin name
     *
     * @var string
     */
    protected $pluginName = 'birthday';
    /**
     * Array containing a list of tabs to be displayed on the page
     *
     * @var array of strings
     * @since 1.0.0
     *
     */
    protected $preftabs = array(
        0 => LAN_PLUGIN__BIRTHDAY_ADMIN_TAB0,
        1 => LAN_PLUGIN__BIRTHDAY_ADMIN_TAB1,
        2 => LAN_PLUGIN__BIRTHDAY_ADMIN_TAB2,
        3 => LAN_PLUGIN__BIRTHDAY_ADMIN_TAB3);

    protected $prefs = array(
        'birthday_numdue' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_NUMBERTOSHOW,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_NUMBERTOSHOW_HELP,
            'tab' => 0,
            'type' => 'number',
            'data' => 'int',
            'writeParms' => array('max' => 20, 'min' => 1)),
        'birthday_dformat' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_FORMAT,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_FORMAT_HELP,
            'tab' => 0,
            'type' => 'dropdown',
            'data' => 'int',
            'writeParms' => array(
                '0' => LAN_PLUGIN__BIRTHDAY_ADMIN_FORMAT_LONG,
                '1' => LAN_PLUGIN__BIRTHDAY_ADMIN_FORMAT_SHORT)),
        'birthday_showAge' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_AGE,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_AGE_HELP,
            'tab' => 0,
            'type' => 'boolean',
            'data' => 'int'),
        'birthday_linkUser' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_LINK,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_LINK_HELP,
            'tab' => 0,
            'type' => 'boolean',
            'data' => 'int'),
        'birthday_sendEmail' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_EMAIL,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_EMAIL_HELP,
            'tab' => 2,
            'type' => 'boolean',
            'data' => 'int'),
        'birthday_subject' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_SUBJECT,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_SUBJECT_HELP,
            'tab' => 2,
            'type' => 'text',
            'data' => 'str',
            ),
        'birthday_emailfrom' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_EMAILFROM,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_EMAILFROM_HELP,
            'tab' => 2,
            'type' => 'text',
            'data' => 'str',
            'validate' => 'regex',
            'rule' => '#^[\w]+$#i'),
        'birthday_emailaddr' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_EMAILFROMADDR,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_EMAILFROMADDR_HELP,
            'tab' => 2,
            'type' => 'email',
            'data' => 'str'),
        'birthday_lastemail' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_TIME,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_TIME_HELP,
            'tab' => 2,
            'type' => 'datestamp',
            'data' => 'int'),
        'birthday_sendpm' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_PM,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_PM_HELP,
            'tab' => 2,
            'type' => 'boolean',
            'data' => 'int'),
        'birthday_pmfrom' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_PMUSER,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_PMUSER_HELP,
            'tab' => 2,
            'type' => 'user',
            'data' => 'str',
            ),
        'birthday_showclass' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_MENU,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_MENU_HELP,
            'tab' => 1,
            'type' => 'userclass',
            'data' => 'int',
            'writeParms' => 'default=254&classlist=public,member,main,nobody,admin,classes,no-excludes'),
        'birthday_includeclass' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_INCLUDE,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_INCLUDE_HELP,
            'tab' => 1,
            'type' => 'userclass',
            'data' => 'int',
            'writeParms' => 'default=0&classlist=member,nobody,new,classes,no-excludes'),
        'birthday_excludeclass' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_EXCLUDE,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_EXCLUDE_HELP,
            'tab' => 1,
            'type' => 'userclass',
            'data' => 'int',
            'writeParms' => 'default=255&classlist=nobody,new,classes,no-excludes'),
        'birthday_statsclass' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_STATS,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_STATS_HELP,
            'tab' => 1,
            'type' => 'userclass',
            'data' => 'int',
            'writeParms' => 'default=255&classlist=main,public,nobody,admin,classes,no-excludes'),
        'birthday_usecss' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_CSS,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_CSS_HELP,
            'tab' => 2,
            'type' => 'boolean',
            'data' => 'int'),
        'birthday_showAvatar' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_AVATAR,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_AVATAR_HELP,
            'tab' => 0,
            'type' => 'boolean',
            'data' => 'int'),
        'birthday_avwidth' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_WIDTH,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_WIDTH_HELP,
            'tab' => 0,
            'type' => 'number',
            'data' => 'int',
            'writeParms' => array('min' => '1', 'max' => '20')),
        'birthday_greeting' => array(
            'title' => LAN_PLUGIN__BIRTHDAY_ADMIN_CONTENT,
            'help' => LAN_PLUGIN__BIRTHDAY_ADMIN_CONTENT_HELP,
            'tab' => 3,
            'type' => 'textarea',
            'data' => 'str',
            ));

}

new plugin_birthday_admin();

require_once (e_ADMIN . "auth.php");
e107::getAdminUI()->runPage(); // Send page content
require_once (e_ADMIN . "footer.php");


/**
 * e_help()
 * 
 * @return
 */

function e_help()
{
    $helpArray = e_version::genUpdate('birthday');
    return $helpArray;
}

?>
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
// *		Plugin		:	Birthday Menu (e107 v7)
// *
// ***************************************************************
require_once("../../class2.php");
error_reporting(E_ALL);
if (!defined('e107_INIT')){
    exit;
}
e107::js('birthday', 'js/birthday.js', 'jquery'); // Load Plugin javascript and include jQuery framework
e107::css('birthday', 'css/birthday.css'); // load css file
e107::lan('birthday', 'admin', true); // language file


if (!defined("USER_WIDTH")){
    define(USER_WIDTH, "width:100%;");
}

require_once(e_PLUGIN . "birthday/handlers/birthday_class.php");

// checkif already created
#$birthday_obj = new birthdayClass();
$birthday_obj = new birthdayClass();
require_once(HEADERF);
$birthday_obj->demography();
require_once(FOOTERF);

?>
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

class bdayTemplate{
    function __construct(){
    }
    function heading(){
        return "
<table style='width:100%'>";
    }
    function today(){
        return "
	<tr>
		<td colspan='2' style='text-align:center;'>
			<span style='font-size:11px;text-align:center;'><b>{BIRTHDAY_TITLE}</b><br />{BIRTHDAY_LOGO}</span>
		</td>
	</tr>
	";
    }
    function none(){
        return "
		<tr>
			<td colspan='2' style='text-align:center;'>
    		<span style='text-align:center;font-size:11px;'><b>" . BIRTHDAY_LAN_6 . "</b></span>
			</td>
		</tr>";
    }
    function nextHeading(){
        return "
    <tr>
    	<td colspan='2' style='text-align:center;'>
    		<br /><span style='text-align:center;font-size:11px;'><b>" . BIRTHDAY_LAN_5 . "</b></span>
		</td>
	</tr>";
    }
    function detail(){
        if (BIRTHDAY_AVATAR == 1){
            // avatars on
            return "
    <tr>
    	<td style='vertical-align:middle;width:" . BIRTHDAY_LINEHEIGHT . "px;height:" . BIRTHDAY_LINEHEIGHT . "px;'>{BIRTHDAY_AVATAR}</td>
    	<td style='vertical-align:middle;text-align:left;height:" . BIRTHDAY_LINEHEIGHT . "px;'>{BIRTHDAY_USER} {BIRTHDAY_AGE}</td>
	</tr>";
        }else{
            // avatars off
            return "
	<tr>
		<td style='vertical-align:middle;text-align:left;height:" . BIRTHDAY_LINEHEIGHT . "px;'>{BIRTHDAY_USER} {BIRTHDAY_AGE}</td>
		<td style='width:1px;'>&nbsp;</td>
	</tr>";
        }
    }
    function future(){
        if (BIRTHDAY_AVATAR == 1){
            return "
    <tr>
    	<td style='vertical-align:middle;width:" . BIRTHDAY_LINEHEIGHT . "px;height:" . BIRTHDAY_LINEHEIGHT . "px;'>{BIRTHDAY_AVATAR}</td>
    	<td style='vertical-align:middle;text-align:left;height:" . BIRTHDAY_LINEHEIGHT . "px;'>{BIRTHDAY_USER} {BIRTHDAY_AGE} {BIRTHDAY_UPDATE} </td>
	</tr>";
        }else{
            return "
	<tr>
    	<td style='vertical-align:middle;text-align:left;height:" . BIRTHDAY_LINEHEIGHT . "px;'>{BIRTHDAY_USER} {BIRTHDAY_AGE} {BIRTHDAY_UPDATE} </td>
    	<td style='width:1px;' >&nbsp;</td>
	</tr>";
        }
    }
    function nofuture(){
        return "
	<tr>
		<td style='text-align:center;' colspan='2'>" . BIRTHDAY_LAN_12 . "</td>
	</tr>";
    }
    function footer(){
        return "
	<tr>
		<td style='text-align:center;' colspan='2'><br />{BIRTHDAY_DEMO}</td>
	</tr>
</table>
    ";
    }
    function demoHeaderNG(){
        return "
<div class='bdayContainer' >
	<table class='fborder' style='width:50%;' >
		<thead>
			<tr >
				<th  class='forumheader2' style='width:60%;' >Age Range</th>
				<th  class='forumheader2' style='width:40%;' >Members</th>
			</tr>
		</thead>
		<tbody>

";
    }
    function demoDetailNG($agebands){
    	foreach($agebands as $key => $val){

            $retval .= "
		<tr >
			<td class='forumheader3' >{BIRTHDAY_RANGENAME={$key}}</td>
			<td class='forumheader3' >{BIRTHDAY_RANGEVALUE={$val}}</td>
		</tr>";
    	}
        return $retval;
    }
    function demoFooterNG(){
        return "
		</tbody>
	</table>
</div>";
    }
    function demoHeader(){
    	return "
<div class='bdayContainer' >
	<div class='' >{BIRTHDAY_BARGRAPH}</div>
	<table class='fborder' style='width:50%;' >
		<thead>
			<tr >
				<th  class='forumheader2' style='width:60%;' >Age Range</th>
				<th  class='forumheader2' style='width:40%;' >Members</th>
			</tr>
		</thead>
		<tbody>

";
    }
    function demoDetail($agebands){
    	foreach($agebands as $key => $val){

    		$retval .= "
		<tr >
			<td class='forumheader3' >{BIRTHDAY_RANGENAME={$key}}</td>
			<td class='forumheader3' >{BIRTHDAY_RANGEVALUE={$val}}</td>
		</tr>";
    	}
    	return $retval;
    }
    function demoFooter(){
    	return "
		</tbody>
	</table>
</div>";
    }
}
// *************************************************************************
// *
// * 	Show this if there are no birthdays today.
// *
// *
// *************************************************************************
// *************************************************************************
// *
// * 	Each birthday that occurs today
// *
// *		BIRTHDAY_USER - Displays the users name with link to their profile page
// *		BIRTHDAY_AGE - 	Display their age (if set in config) with pre and post
// *					characters, use nolinks to remove link to profile
// *
// *************************************************************************
// BIRTHDAY_LINEHEIGHT height of each row
// parameter in shortcode nolink to onit link to user info page
// *************************************************************************
// *
// * 	Future birthdays
// *
// *		BIRTHDAY_UPCOMING - Displays the users name with link to their profile page
// *		BIRTHDAY_UPDATE - 	Display date of birthday date format in config
// *		BDATE_UPAGE - 	Display their age (if set in config) with pre and post
// *						characters, use nolinks to remove link to profile
// *
// *************************************************************************
?>
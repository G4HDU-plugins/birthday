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

if (!isset($BIRTHDAY_MENU_A_HEADER))
{
    $BIRTHDAY_MENU_A_HEADER = "
<table style='" . USER_WIDTH . "' class='fborder' >
	<tr>
		<td class='fcaption' colspan='4'>" . BIRTHDAY_ADMIN_A59 . "</td>
	</tr>
	<tr>
		<td class='forumheader2' colspan='4'><b>" . $birthday_msg . "</b>&nbsp;</td>
	</tr>
	<tr>
		<td style='width:15%;' class='forumheader2'><b>" . BIRTHDAY_ADMIN_A61 . "</b></td>
		<td style='width:10%;text-align:right;' class='forumheader2'><b>" . BIRTHDAY_ADMIN_A63 . "</b></td>
		<td style='width:65%;' class='forumheader2'><b>&nbsp;</b></td>
		<td style='width:10%;text-align:right;' class='forumheader2'><b>" . BIRTHDAY_ADMIN_A62 . "</b></td>
	</tr>";
}
if (!isset($BIRTHDAY_MENU_A_DETAIL))
{
    $BIRTHDAY_MENU_A_DETAIL = "
	<tr>
		<td class='forumheader3'>{BIRTHDAY_RANGE}</td>
		<td class='forumheader3' style='text-align:right;'>{BIRTHDAY_TOTAL_RANGE}</td>
		<td class='forumheader3'>{BIRTHDAY_BAR}</td>
		<td class='forumheader3' style='text-align:right;' >{BIRTHDAY_PERCENT}%</td>
	</tr>
		";
}
if (!isset($BIRTHDAY_MENU_A_FOOTER))
{
    $BIRTHDAY_MENU_A_FOOTER = "
	<tr>
		<td class='forumheader2' colspan='4'>&nbsp;</td>
	</tr>
	<tr>
		<td class='forumheader3' colspan='2' >&nbsp;</td>
		<td class='forumheader3' >" . BIRTHDAY_ADMIN_A60 . " <b>{BIRTHDAY_UNDEFINED}</b>&nbsp;</td>
		<td class='forumheader3' >&nbsp;</td>
	</tr>
	<tr>
		<td class='fcaption' colspan='4'>&nbsp;</td>
	</tr>
</table>
";
}

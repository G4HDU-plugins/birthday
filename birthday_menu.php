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

#require_once(e_HANDLER . 'userclass_class.php');
#require_once(e_HANDLER.'cache_class.php');
#error_repoting(E_ALL);
require_once(e_PLUGIN.'birthday/handlers/birthday_class.php');

$bdayClass=new birthdayClass();
$bdayClass->generate();
return;
global $e107cache,$birthday_shortcodes, $pref, $tp, $sql, $sql2, $gold_obj, $birthday_show, $user_id, $BIRTHDAY_datepart, $birthday_showyear, $user_name, $BIRTHDAY_results, $BIRTHDAY_today, $showAvatar, $birthday_out;
$BIRTHDAY_now = time() + ($pref['time_offset'] * 3600);

if (date("d", $pref['birthday_cache']) != date("d", $BIRTHDAY_now)){
    // we are on a new day

    $e107cache->clear("nq_bdaymenu");
    $pref['birthday_cache'] = $BIRTHDAY_now;
    save_prefs();
}

if ($cacheData = $e107cache->retrieve("nq_bdaymenu", 1440)){
    // force cache clear at least every 24 hours
    echo $cacheData;
}else{
    include_lan(e_PLUGIN . "birthday/languages/" . e_LANGUAGE . "_birthday_mnu.php");
}

function sendEmail($to, $name){
    global $sysprefs, $sql, $pref, $tp, $THEMES_DIRECTORY, $IMAGES_DIRECTORY;
    // # # sendemail($user_email, $pref['birthday_subject'], $pref['birthday_greeting'], $user_name, $pref['birthday_emailaddr'], $pref['birthday_emailfrom']);
    require_once(e_HANDLER . "mail.php");
    $mail_head = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n";
    $mail_head .= "<html xmlns='http://www.w3.org/1999/xhtml' >\n";
    $mail_head .= "<head><meta http-equiv='content-type' content='text/html; charset=utf-8' />\n";
    $emessage = $pref['birthday_greeting'];
    $emessage = str_replace("{NAME}", $name, $emessage);
    $subj = $tp->toFORM($pref['birthday_subject'], false);
    $subj = str_replace("&#039;", "'", $subj);
    if ($pref['birthday_usepm'] == 1){
        $retrieve_prefs[] = 'pm_prefs';
        require_once(e_PLUGIN . "pm/pm_class.php");
        require_once(e_PLUGIN . "pm/pm_func.php");
        $lan_file = e_PLUGIN . "pm/languages/" . e_LANGUAGE . ".php";
        include_once(is_readable($lan_file) ? $lan_file : e_PLUGIN . "pm/languages/English.php");
        $pm_prefs = $sysprefs->getArray("pm_prefs");
        $birthday_pmfrom = ($pref['birthday_pmfrom'] > 0?$pref['birthday_pmfrom']:1);
        $birthday_pm = new private_message;
        // PM User
        $sql->db_Select("user", "*", "where user_name='$name'", "", false);
        $row = $sql->db_Fetch();
        extract($row);
        if ($user_id > 0){
            $birthday_vars['pm_subject'] = $subj;
            $birthday_vars['pm_message'] = $emessage;
            $birthday_vars['to_info']['user_id'] = $user_id;
            $birthday_vars['from_id'] = $birthday_pmfrom;
            $birthday_vars['to_info']['user_email'] = $user_email;
            $birthday_vars['to_info']['user_name'] = $user_name;
            $birthday_vars['to_info']['user_class'] = $user_class;
            $res = $birthday_pm->add($birthday_vars);
        }
    }else{
        if ($pref['birthday_usecss'] == 1){
            // Use the site theme for the email, embed in email
            $theme = $THEMES_DIRECTORY . $pref['sitetheme'] . "/";
            $style_css = file_get_contents(e_THEME . $pref['sitetheme'] . "/style.css");
            $mail_head .= "<style>\n" . $style_css . "\n</style>";
            $message = $mail_head;
            $message .= "</head>\n<body>\n";
            $message .= "<div style='padding:10px;width:97%'><div class='forumheader3'>\n";
            $message .= $tp->toHTML($emessage, true) . "</div></div></body></html>";
        }else{
            $message = $mail_head;
            $message .= "</head>\n<body>\n";
            $message .= $tp->toHTML($emessage, true) . "</body></html>";
            $message = str_replace("&quot;", '"', $message);
            $message = str_replace('src="', 'src="' . SITEURL, $message);
        }
        // print $to." -  ".$name." - ".$messageX." ".$subjX." ".$pref['birthday_emailfrom']." ".$pref['birthday_emailaddr']."<br>";
        sendemail($to, $subj, $message, $name, $pref['birthday_emailaddr'], $pref['birthday_emailfrom']);
        // sendemail($to, $subj, $message, $name, $pref['birthday_emailfrom'], $pref['birthday_emailaddr']);
    }
}
/**
* showAvatar()
*
* @param string $showAvatar
* @param integer $user_id
* @return
*/

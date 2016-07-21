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
 * @version 2.0.0
 *
 * @todo
 */

require_once (e_PLUGIN . 'birthday/birthday_shortcodes.php');
if (file_exists(THEME . 'birthday_template.php')) {
    define('BIRTHDAY_TEMPLATE', THEME . 'birthday_template.php');
} else {
    define('BIRTHDAY_TEMPLATE', e_PLUGIN .
        'birthday/templates/birthday_template.php');
}

require_once (BIRTHDAY_TEMPLATE);

/**
 * birthdayClass
 *
 * @package
 * @author Birthday
 * @copyright Father Barry
 * @version 2016
 * @access public
 */
class birthdayClass
{
    /**
     * @var string
     */
    private $prefs = '';
    private $sc;
    private $sql;
    private $tp;
    private $frm;
    private $ns;
    private $template;
    private $thisDay = 0; // day number of the year
    private $doneToday = false;
    private $content;
    private $months; // short month
    private $monthl; // long month
    private $suffix; // ordinal
    private $birthdayToday = false;
    private $numberBirthdays = 0;
    private $birthdayUpComing = false;
    private $numberUpComing = 0;

    /**
     * birthdayClass::__construct()
     *
     * @return
     */
    function __construct()
    {
        //  print "WWWWWWWWWWWWWWWWWWWWWWWWWWW";

    }

    /**
     * birthdayClass::processEmail()
     *
     * @return
     */
    function processEmail()
    {
        $this->initBirthday();
        if (1 == 1 || !$this->runToday) {
            $this->getBirthdays(); // generate the list of birthdays
            $this->stats(); //generate stats
            if ($this->prefs['birthdayIsToday']) {
                if ($this->prefs['birthday_sendEmail']) {
                    $emailOK = $this->sendEmail();
                }
                if ($this->prefs['birthday_sendpm']) {
                    $pmOK = $this->sendPM();
                }
                if (($this->prefs['birthday_sendEmail'] && $emailOK) && ($this->prefs['birthday_sendpm'] &&
                    $pmOK)) {
                    $this->prefs['birthday_dayRun'] = $this->thisDay;
                }
                $this->savePrefs();
            }
        }
    }

    /**
     * birthdayClass::stats()
     *
     * @return
     */
    function stats()
    {

    }

    /**
     * birthdayClass::sendEmail()
     *
     * @return
     */
    private function sendEmail()
    {


        $birthdayList = json_decode($this->prefs['birthdaysToday']);
        print "<div style='color:#ffff00'>QQQQ";
        foreach ($birthdayList as $value => $key) {
            print "<div style='color:#ffff00'>{$value} {$key}<br /></div>";

        }
        print "</div>";


        $retval = true;
        return $retval;
    }

    /**
     * birthdayClass::sendPM()
     *
     * @return
     */
    private function sendPM()
    {

    }

    /**
     * birthdayClass::savePrefs()
     *
     * @return
     */
    function savePrefs()
    {
        $this->prefs = e107::getConfig('birthday')->setPref($this->prefs)->save(false);
    }

    /**
     * birthdayClass::menuContent()
     *
     * @return
     */
    function menuContent()
    {
        if ($this->content = $this->cache->retrieve("nq_bdaymenu")) {
        } else {
        }
    }

    /**
     * birthdayClass::generate()
     *
     * @return
     */
    function getBirthdays()
    {

        // only show birthdays in the specified user class
        $birthdayFind = '';
        $birthdayFind .= "AND find_in_set('" . $this->prefs['birthday_includeclass'] .
            "',user_class) ";
        $birthdayFind .= "AND !find_in_set('" . $this->prefs['birthday_excludeclass'] .
            "',user_class) ";

        $birthday_arg = "
SELECT user_name,user_email,user_id,user_birthday,user_image,user_sess,user_class,user_admin,
TIMESTAMPDIFF(YEAR, user_birthday, CURDATE()) AS age
FROM #user
LEFT JOIN #user_extended ON user_extended_id = user_id
WHERE (date_format(user_birthday,'%m-%d') = date_format(now(),'%m-%d')) " . $birthdayFind;
        $result = $this->sql->gen($birthday_arg, true);
        $this->numberBirthdays = $result;
        $this->prefs['birthdayIsToday'] = false;
        $this->prefs['birthdaysToday'] = '';
        if ($result) {
            $this->prefs['birthdaysToday'] = json_encode($this->sql->rows());
            $this->prefs['birthdayIsToday'] = true;
        }

        $birthday_datestring = date("Y-m-d", $birthdayNow);
        $birthday_arg = "
SELECT user_name,user_email,user_id,user_birthday,user_image,user_sess,user_class,user_admin,
DATE_FORMAT(curdate(), '%Y') - DATE_FORMAT(user_birthday, '%Y') + IF(
DATE_FORMAT(user_birthday, '%m%d') < DATE_FORMAT(curdate(), '%m%d'), 1, 0) AS new_age, DATEDIFF(user_birthday + INTERVAL YEAR(curdate()) - YEAR(user_birthday) + IF(DATE_FORMAT(curdate(), '%m%d') > DATE_FORMAT(user_birthday, '%m%d'), 1, 0) YEAR,
curdate()) AS days_to_birthday
FROM #user
LEFT JOIN #user_extended ON user_id=user_extended_id
HAVING days_to_birthday>0 && user_birthday IS NOT NULL " . $birthdayFind . "
ORDER BY days_to_birthday ASC
LIMIT 0, {$this->prefs['birthday_numdue']};";

        $result = $this->sql->gen($birthday_arg, true);
        $this->numberUpComing = $result;
        $this->prefs['birthdayComing'] = '';
        $this->prefs['birthdayUpComing'] = false;
        if ($this->numberUpComing) {
            $this->prefs['birthdayComing'] = json_encode($this->sql->rows());
            $this->prefs['birthdayUpComing'] = true;
        }
        //   var_dump($this->prefs);
        // save prefs
        // temp $this->prefs['birthday_dayRun'] = $this->thisDay;
        $this->savePrefs();

        return;
        // Have we already done an email to users

        // for testing
        $birthday_text = '';
        // #   $BIRTHDAY_today = date("Y-m-d", $birthdayNow);
        // $BIRTHDAY_month = date("m", $birthdayNow);
        // $BIRTHDAY_day = date("d", $birthdayNow);
        // $BIRTHDAY_year = date("Y", $birthdayNow);
        // get any birthdays today

        // * Select the appropriate comment depending on the number of birthdays today
        $birthday_datedisplay = date($this->prefs['birthday_dformat'], $birthdayNow);
        $birthday_text .= $this->tp->parsetemplate($this->template->heading(), true, $this->
            sc);
        $birthday_text .= $this->tp->parsetemplate($this->template->today(), true, $this->
            sc);

        if ($result) {
            // require_once(e_HANDLER . "mail.php");
            while ($row = $this->sql->fetch()) {
                // print_a($row);
                // extract($row);
                // get location of avatar
                // do gold if active and not already done and birthday gold active
                // if (is_object($gold_obj)){
                // $gold_obj->load_gold($user_id);
                // $birthday_thisyear = date('Y', $birthdayNow);
                // if ($gold_obj->gold_plugins['birthday'] && $this->prefs['birthday_gold'] >= 0 && $gold_obj->gold_additional[$user_id]['birthday_year'] != $birthday_thisyear){
                // // if the birthday menu is active in gold, we do allocate gold and they not had it this year already
                // // *	Parameters	: 	$gold_param['gold_user_id'] (default no user)
                // // *				: 	$gold_param['gold_who_id'] (default no user)
                // // *				:	$gold_param['gold_amount'] (default no amount)
                // // *				:	$gold_param['gold_type'] (default "adjustment")
                // // *				:	$gold_param['gold_action'] 	credit - add to account
                // // *												debit - subtract from account
                // // *				:	$gold_param['gold_plugin'] (default no plugin)
                // // *				:	$gold_param['gold_log'] (default "")
                // // *				:	$gold_param['gold_forum'] (default 0)
                // $gold_param = array("gold_user_id" => $user_id,
                // "gold_who_id" => 0,
                // "gold_amount" => $this->prefs['birthday_gold'] ,
                // "gold_plugin" => "birthday",
                // "gold_type" => BIRTHDAY_ADMIN_GOLD05,
                // "gold_action" => "credit",
                // "gold_log" => BIRTHDAY_ADMIN_GOLD04 ,
                // "gold_forum" => 0);
                // $gold_obj->gold_modify($gold_param);
                // $gold_obj->gold_additional[$user_id]['birthday_year'] = $birthday_thisyear;
                // $gold_obj->write_additional($user_id);
                // }
                // }
                // print $user_email;
                // if (($this->prefs['sendEmail'] == 1 && $birthday_doemail)){
                // sendEmail($user_email, $user_name);
                // }
                $this->sc->data['avatar'] = $this->avatar($row['user_image'], $row['user_id'], $row['user_sess']);
                $this->sc->data['age'] = $this->age($row['age']);
                $this->sc->data['user'] = $this->user($row['user_name'], $row['user_id']);
                $birthday_text .= $this->tp->parsetemplate($this->template->detail(), true, $this->
                    sc);
                // $birthday_text .= "<a href='" . e_BASE . "user.php?id." . $user_id . "'>" . $this->tp->toHTML($user_name, false) . " " . $birthday_show . "<br /></a>";
            }
            // $birthday_text .= "<br />";
        } else {
            // none today
            $birthday_text .= $this->tp->parsetemplate($this->template->none(), true, $this->
                sc);
        }

        $birthday_text .= $this->tp->parsetemplate($this->template->nextHeading(), true,
            $this->sc);
        // move this to cron
        // if we have done email and gold check today
        // if ($birthday_doemail){
        // $this->prefs['birthday_lastemail'] = $birthdayNow;
        // save_prefs();
        // }
        // Check for the upcoming birthdays
        // $birthday_arg = "select *,YEAR('" . $birthday_datestring . "') - YEAR(user_birthday) -( DATE_FORMAT('" . $birthday_datestring . "', '%m-%d') < DATE_FORMAT(user_birthday, '%m-%d')) AS age2,
        // TIMESTAMPDIFF(YEAR, user_birthday, CURDATE())+1 AS age
        // from #user left join #user_extended on user_extended_id = user_id
        // where(user_birthday != '0000/00/00' AND ((DAYOFYEAR(CONCAT(DATE_FORMAT('" . $birthday_datestring . "', '%Y-'), DATE_FORMAT(user_birthday,'%m-%d'))) < DAYOFYEAR('" . $birthday_datestring . "'))*366)+
        // DAYOFYEAR(CONCAT(DATE_FORMAT('" . $birthday_datestring . "', '%Y-'), DATE_FORMAT(user_birthday,'%m-%d')))>=DAYOFYEAR('" . $birthday_datestring . "'))
        // and not (DAYOFMONTH(user_birthday)=DAYOFMONTH('" . $birthday_datestring . "') and MONTH(user_birthday)=MONTH('" . $birthday_datestring . "') ) " . $birthdayFind . "
        // ORDER BY
        // ((DAYOFYEAR(CONCAT(DATE_FORMAT('" . $birthday_datestring . "', '%Y-'), DATE_FORMAT(user_birthday,'%m-%d'))) < DAYOFYEAR('" . $birthday_datestring . "')) * 366) + DAYOFYEAR(CONCAT(DATE_FORMAT('" . $birthday_datestring . "', '%Y-'), DATE_FORMAT(user_birthday,'%m-%d'))),date_format(user_birthday,'%m%d') asc
        // limit 0," . $this->prefs['birthday_numdue'] . "";
        $birthday_datestring = date("Y-m-d", $birthdayNow);

        if ($BIRTHDAY_due) {
            $this->monthlist = explode(",", BIRTHDAY_LAN_MONTHS);
            // $birthday_text .= "<br />" . BIRTHDAY_LAN_5 . "<br />";
            while ($row = $this->sql->fetch()) {
                $this->sc->data['avatar'] = $this->avatar($row['user_image'], $row['user_id'], $row['user_sess']);
                $this->sc->data['age'] = $this->age($row['new_age']);
                $this->sc->data['user'] = $this->user($row['user_name'], $row['user_id']);
                $this->sc->data['datebirth'] = $this->birthdater($row['user_birthday']);

                $birthday_text .= $this->tp->parsetemplate($this->template->future(), true, $this->
                    sc);
                // $birthday_text .= $birthday_out . " <a title='" . $user_birthday = "$BIRTHDAY_datepart[2].{$BIRTHDAY_datepart[1]}{$birthday_showyear}" . "' href='" . e_BASE . "user.php?id." . $user_id . "'>" . $this->tp->toHTML($user_name, false) . " " . $birthday_show . "</a><br />";
            }
        } else {
            $birthday_text .= $this->tp->parsetemplate($this->template->nofuture(), true, $this->
                sc);
        }
        $birthday_text .= $this->tp->parsetemplate($this->template->footer(), true, $this->
            sc);

        $this->ns->tablerender(BIRTHDAY_LAN_3, $birthday_text, 'birthday'); // Render the menu
        return;
        ob_start(); // Set up a new output buffer
        $ns->tablerender(BIRTHDAY_LAN_3, $birthday_text, 'birthday'); // Render the menu
        $cache_data = ob_get_flush(); // Get the menu content, and display it
        $e107cache->set("nq_bdaymenu", $cache_data); // Save to cache
    }

    /**
     * birthdayClass::avatar()
     *
     * @param string $image
     * @param integer $user_id
     * @param mixed $photo
     * @return
     */
    function avatar($image = '', $user_id = 0, $photo)
    {
        global $FILES_DIRECTORY, $pref;
        if (!$this->prefs['showAvatar']) {
            return '';
        }
        // see if photos enabled
        // if so see if there is a photo (or matching avatar)
        // if there is we'll use it
        // if not carry on with avatars
        $photoFile = str_replace("-upload-", '', $photo);
        if ($pref['photo_upload'] && !empty($photo) && is_readable(e_MEDIA .
            'avatars/upload/' . $photoFile)) {
            $source = e_MEDIA . 'avatars/upload/' . $photoFile;
        } elseif ($pref['avatar_upload']) {
            $birthday_checkavatar = str_replace('-upload-', '', $image);
            if (strpos($image, '-upload-') !== false && is_readable(e_MEDIA .
                'avatars/upload/' . $birthday_checkavatar)) {
                // uploaded avatar
                $source = e_MEDIA . 'avatars/upload/' . $birthday_checkavatar;
            } elseif (strpos($image, 'http:') !== false || strpos($image, 'https:') !== false) {
                // remote avatar
                $source = $image;
            } elseif (!empty($image) && is_readable(e_MEDIA . 'avatars/default/' . $image)) {
                // site avatar
                $source = e_MEDIA . 'avatars/default/' . $image;
            } else {
                // no avatar
                $source = e_PLUGIN . "birthday/images/default.png";
            }
        } else {
            return '';
        }
        return "<img src='{$source}' style='border:0px;width:" . BIRTHDAY_AVHEIGHT .
            "px;height:" . BIRTHDAY_AVHEIGHT . "px;' alt='' title='' />";
        ;
    }

    /**
     * birthdayClass::age()
     *
     * @param mixed $age
     * @return
     */
    function age($age)
    {
        if ($this->prefs['showAge'] && $this->prefs['linkUser']) {
            return "<a href='" . e_BASE . "user.php?id." . $userID . "'>({$age})</a>";
        } elseif ($this->prefs['showAge']) {
            return "({$age})";
        } else {
            return "";
        }
    }

    /**
     * birthdayClass::user()
     *
     * @param mixed $userName
     * @param mixed $userID
     * @return
     */
    function user($userName, $userID)
    {
        global $gold_obj, $gorb_obj, $gold_obj;
        // if (is_object($gold_obj) && $gold_obj->plugin_active('gold_orb')){
        // gold orb is active
        // if (!is_object($gorb_obj)){
        // require_once(e_PLUGIN . 'gold_orb/includes/gold_orb_class.php');
        // $gorb_obj = new gold_orb;
        // }
        // if ($parm == 'nolink'){
        // return $gorb_obj->show_orb($user_id, $user_name) ;
        // }else{
        // return "<a href='" . e_BASE . "user.php?id." . $user_id . "'>" . $gorb_obj->show_orb($user_id, $user_name) . "</a>";
        // }
        // }else{
        if ($this->prefs['linkUser']) {
            return "<a href='" . e_BASE . "user.php?id." . $userID . "'>" . $this->tp->
                toHTML($userName, false) . "</a>";
        } else {
            return $this->tp->toHTML($userName, false);
        }
        // }
    }

    /**
     * birthdayClass::birthdater()
     *
     * @param mixed $birthdate
     * @return
     */
    function birthdater($birthdate)
    {
        $BIRTHDAY_datepart = explode("-", $birthdate);
        $birthday_bdate = mktime(0, 0, 0, $BIRTHDAY_datepart[1], $BIRTHDAY_datepart[2],
            $BIRTHDAY_datepart[0]);
        $BIRTHDAY_age = $age + 1;

        $birthday_d = intval($BIRTHDAY_datepart[2]); // day
        $birthday_m = intval($BIRTHDAY_datepart[1]); // month
        $birthday_y = ' ' . $BIRTHDAY_datepart[0]; // year
        switch ($this->prefs['birthday_dformat']) {
            case 1:
                // d M Y
                $birthday_out = $birthday_d . " " . $birthday_m . $birthday_y;
                break;
            case 2:
                // M d
                $birthday_out = $birthday_m . " " . $birthday_d;
                break;
            case 3:
                // M d Y
                $birthday_out = $birthday_m . " " . $birthday_d . $birthday_y;
                break;
            case 4:
                // Y M d
                $birthday_out = $birthday_y . " " . $birthday_m . $birthday_d;
                break;
            case 5:
                // d mmm Y
                $birthday_out = $birthday_d . " " . $this->months[$birthday_m] . $birthday_y;
                break;
            case 6:
                // d MMM Y
                $birthday_out = $birthday_d . " " . $this->monthl[$birthday_m] . $birthday_y;
                break;
            case 7:
                // mmm d Y
                $birthday_out = $this->months[$birthday_m] . " " . $birthday_d . $birthday_y;
                break;
            case 8:
                // MMM d Y
                $birthday_out = $this->monthl[$birthday_m] . " " . $birthday_d . $birthday_y;
                break;

            case 9:
                // d mmm Y
                $birthday_out = $birthday_d . $this->suffix[$birthday_d] . " " . $this->months[$birthday_m] .
                    $birthday_y;
                break;
            case 10:
                // d MMM Y
                $birthday_out = $birthday_d . $this->suffix[$birthday_d] . " " . $this->monthl[$birthday_m] .
                    $birthday_y;
                break;
            case 11:
                // mmm d Y
                $birthday_out = $this->months[$birthday_m] . " " . $birthday_d . $this->suffix[$birthday_d] .
                    $birthday_y;
                break;
            case 12:
                // MMM d Y
                $birthday_out = $this->monthl[$birthday_m] . " " . $birthday_d . $this->suffix[$birthday_d] .
                    $birthday_y;
                break;

            case 13:
                // d mmm Y
                $birthday_out = $birthday_d . " " . $this->months[$birthday_m];
                break;
            case 14:
                // d MMM Y
                $birthday_out = $birthday_d . " " . $this->monthl[$birthday_m];
                break;
            case 15:
                // mmm d Y
                $birthday_out = $this->months[$birthday_m] . " " . $birthday_d;
                break;
            case 16:
                // MMM d Y
                $birthday_out = $this->monthl[$birthday_m] . " " . $birthday_d;
                break;

            case 17:
                // d mmm Y
                $birthday_out = $birthday_d . $this->suffix[$birthday_d] . " " . $this->months[$birthday_m];
                break;
            case 18:
                // d MMM Y
                $birthday_out = $birthday_d . $this->suffix[$birthday_d] . " " . $this->monthl[$birthday_m];
                break;
            case 19:
                // mmm d Y
                $birthday_out = $this->months[$birthday_m] . " " . $birthday_d . $this->suffix[$birthday_d];
                break;
            case 10:
                // MMM d Y
                $birthday_out = $this->monthl[$birthday_m] . " " . $birthday_d . $this->suffix[$birthday_d];
                break;

            default:
                // d M
                $birthday_out = $birthday_d . " " . $birthday_m;
        }
        return $birthday_out;
    }

    /**
     * birthdayClass::demography()
     *
     * @return
     */
    function demography()
    {
        $cacheTag = "nomd5_birthdaydemo";
        // check if cached
        $this->cache->UserCacheActive = e107::getPref('cachestatus');
        $this->cache->SystemCacheActive = e107::getPref('syscachestatus');
        if ($cacheData = $this->cache->retrieve($cacheTag, 1, false, false)) {
            echo $cacheData;
        } else {
            $cacheData = $this->generatePage();
            ob_start(); // Set up a new output buffer
            $this->ns->tablerender(BIRTHDAY_LAN_3, $cacheData, 'birthday'); // Render the page
            $cacheData = ob_get_flush(); // Get the page content, and display it
            $this->cache->set($cacheTag, $cacheData, false, false, false); // Save to cache
        }
    }

    /**
     * birthdayClass::generatePage()
     *
     * @return
     */
    function generatePage()
    {
        // break down by age groups
        $qry = "SELECT
  COUNT(*) as numberMembers,
  CASE
  	WHEN age <10  THEN '<10'
  	WHEN age >=10 AND age <=20 THEN '10-20'
  	WHEN age >=21 AND age <=30 THEN '21-30'
  	WHEN age >=31 AND age <=40 THEN '31-40'
  	WHEN age >=41 AND age <=50 THEN '41-50'
  	WHEN age >=51 AND age <=60 THEN '51-60'
  	WHEN age >=61 AND age <=70 THEN '61-70'
  	WHEN age >=71 AND age <=70 THEN '71-80'
  	WHEN age >=81 THEN '81+'
  	WHEN age is null THEN 'none'
  END AS ageband
FROM
  (
    select DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(user_birthday, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(user_birthday, '00-%m-%d')) AS age
    from e107_user left join e107_user_extended on user_id=user_extended_id

  ) as tbl
GROUP BY ageband;";
        $agebands = array(
            '<10' => 0,
            '10-20' => 0,
            '21-30' => 0,
            '31-40' => 0,
            '41-50' => 0,
            '51-60' => 0,
            '61-70' => 0,
            '71-80' => 0,
            '81+' => 0,
            'none' => 0);
        $this->sql->gen($qry, false);

        while ($row = $this->sql->fetch()) {
            $agebands[$row['ageband']] = intval($row['numberMembers']);
        }
        if (e107::isInstalled('pchart')) {
            $this->barGraph($agebands);
            $text .= $this->tp->parsetemplate($this->template->demoHeader(), true, $this->
                sc);
            $text .= $this->tp->parsetemplate($this->template->demoDetail($agebands), true,
                $this->sc);
            $text .= $this->tp->parsetemplate($this->template->demoFooter(), true, $this->
                sc);
        } else {
            $text .= $this->tp->parsetemplate($this->template->demoHeaderNG(), true, $this->
                sc);
            $text .= $this->tp->parsetemplate($this->template->demoDetailNG($agebands), true,
                $this->sc);
            $text .= $this->tp->parsetemplate($this->template->demoFooterNG(), true, $this->
                sc);
        }
        return $text;
    }

    /**
     * birthdayClass::barGraph()
     *
     * @param mixed $dataArray
     * @return
     */
    function barGraph($dataArray)
    {
        include (e_PLUGIN . "pchart/class/pData.class.php");
        include (e_PLUGIN . "pchart/class/pDraw.class.php");
        include (e_PLUGIN . "pchart/class/pImage.class.php");
        $dataArray['51-60'] = 161;
        $myData = new pData();
        $myData->addPoints($dataArray, "Series1");
        $myData->setSerieDescription("Series1", "Age Range");
        $myData->setSerieOnAxis("Series1", 0);

        $myData->addPoints(array(
            "<10",
            "10-20",
            "21-30",
            "31-40",
            "41-50",
            "51-60",
            "61-70",
            "71-80",
            ">81",
            "None"), "Absissa");
        $myData->setAbscissa("Absissa");

        $myData->setAxisPosition(10, AXIS_POSITION_LEFT);
        $myData->setAxisName(0, "Members");
        $myData->setAxisUnit(0, "");

        $myPicture = new pImage(550, 350, $myData);
        $myPicture->drawRectangle(0, 0, 500, 349, array(
            "R" => 0,
            "G" => 0,
            "B" => 0));

        $myPicture->setShadow(true, array(
            "X" => 1,
            "Y" => 1,
            "R" => 50,
            "G" => 50,
            "B" => 50,
            "Alpha" => 20));

        $myPicture->setFontProperties(array("FontName" => e_PLUGIN .
                "pchart/fonts/GeosansLight.ttf", "FontSize" => 19));
        $TextSettings = array(
            "Align" => TEXT_ALIGN_MIDDLEMIDDLE,
            "R" => 19,
            "G" => 143,
            "B" => 10,
            "DrawBox" => 1,
            "BoxAlpha" => 30);
        $myPicture->drawText(245, 25, "Membership Age Distribution", $TextSettings);

        $myPicture->setShadow(false);
        $myPicture->setGraphArea(50, 50, 475, 310);
        $myPicture->setFontProperties(array(
            "R" => 0,
            "G" => 0,
            "B" => 0,
            "FontName" => e_PLUGIN . "pchart/fonts/GeosansLight.ttf",
            "FontSize" => 10));
        $max = max($dataArray);
        $max = intval(ceil($max / 10) * 10);
        $maxValue = ($max < 11 ? 10 : $max);
        $AxisBoundaries = array(0 => array("Min" => 0, "Max" => $max));
        $Settings = array(
            "Pos" => SCALE_POS_LEFTRIGHT,
            "Mode" => SCALE_MODE_START0,
            "Mode" => SCALE_MODE_MANUAL,
            "ManualScale" => $AxisBoundaries,
            "LabelingMethod" => LABELING_ALL,
            "GridR" => 255,
            "GridG" => 255,
            "GridB" => 255,
            "GridAlpha" => 50,
            "TickR" => 0,
            "TickG" => 0,
            "TickB" => 0,
            "TickAlpha" => 50,
            "LabelRotation" => 0,
            "CycleBackground" => 1,
            "DrawXLines" => 1,
            "DrawSubTicks" => 1,
            "SubTickR" => 255,
            "SubTickG" => 0,
            "SubTickB" => 0,
            "SubTickAlpha" => 50,
            "DrawYLines" => ALL);
        $myPicture->drawScale($Settings);

        $myPicture->setShadow(true, array(
            "X" => 1,
            "Y" => 1,
            "R" => 50,
            "G" => 50,
            "B" => 50,
            "Alpha" => 10));

        $Config = "";
        $myPicture->drawBarChart($Config);
        $myPicture->render(e_PLUGIN . "pchart/cache/birthday.png");
        // $myPicture->stroke();
    }

    /**
     * birthdayClass::initBirthday()
     *
     * @return
     */
    protected function initBirthday()
    {
        $this->sql = e107::getDb();

        $this->tp = e107::getParser();
        $this->frm = e107::getForm();
        $this->ns = e107::getRender();
        $this->sc = new birthday_shortcodes;
        $this->template = new bdayTemplate();
        $this->prefs = e107::getPlugPref('birthday', '', true);
        $this->thisDay = date('z');
        $this->doneToday = false;
        var_dump($this->prefs);
        if ($this->thisDay != $this->prefs['birthday_dayRun']) {

            $this->cache = new ecache;
            define(BIRTHDAY_AVATAR, $this->prefs['birthday_showAvatar']);
            $this->months = explode(',', BIRTHDAY_LAN_MONTHS);
            $this->monthl = explode(',', BIRTHDAY_LAN_MONTHL);
            $this->suffix = explode(',', BIRTHDAY_LAN_MONTHSUFFIX);
            if ($this->prefs['showAvatar']) {
                define(BIRTHDAY_AVHEIGHT, $this->prefs['birthday_avwidth']);
                define(BIRTHDAY_LINEHEIGHT, $this->prefs['birthday_avwidth'] + 3);
                define(BIRTHDAY_ALIGN, 'left');
            } else {
                define(BIRTHDAY_LINEHEIGHT, 0);
            }
        }

    }
    protected function logIt()
    {
        $log = e107::getLog();

        $logText = "" . LAN_PLUGIN_ASSIGN_LOGNAME . " {$name} " .
            LAN_PLUGIN_ASSIGN_LOGLOG . " {$logname} :: " . LAN_PLUGIN_ASSIGN_LOGOLD . " {$string} => " .
            LAN_PLUGIN_ASSIGN_LOGNEW . " {$class_list}";
        $log->add('birthday', $logText, E_LOG_INFORMATIVE, '1');
    }
}

?>
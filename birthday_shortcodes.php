<?php
if (!defined('e107_INIT')){
    exit;
}

/**
*
* @package bday
* @subpackage bday
* @version 1.0.1
* @author baz
*
* 	bday shortcodes
*/

/**
* birthday_shortcodes
*
* @package
* @author barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class birthday_shortcodes extends e_shortcode{
    public $data;

    function __construct(){
        parent::__construct();
    }
    function sc_birthday_graph(){
        return $this->data['graph'];
    }
    function sc_birthday_title(){
        switch ($this->data['numrecs']){
            case 0: // None today
                return '' ;
                break;
            case 1: // one today
                return BIRTHDAY_LAN_1a . " " . BIRTHDAY_LAN_1b ;
                break;
            default: // many today
                return BIRTHDAY_LAN_0 ;
                break;
        }
    }
    function sc_birthday_logo(){
        global $BIRTHDAY_today;
        if ($BIRTHDAY_today && is_readable(e_PLUGIN . "birthday/images/bdayanimate.gif")){
            return "<img src='" . e_PLUGIN . "birthday/images/bdayanimate.gif' alt='" . BIRTHDAY_LAN_7 . "' title='" . BIRTHDAY_LAN_8 . "' /><br />";
        }else{
            return '';
        }
    }
    function sc_birthday_user(){
        return $this->data['user'];
    }
    function sc_birthday_age(){
        return $this->data['age'];
    }
    function sc_birthday_upcomming(){
        global $gold_obj, $user_id, $tp, $birthday_show, $BIRTHDAY_datepart, $birthday_showyear, $user_name, $gorb_obj;
        if (is_object($gold_obj) && $gold_obj->plugin_active('gold_orb')){
            // gold orb is active
            if (!is_object($gorb_obj)){
                require_once(e_PLUGIN . 'gold_orb/includes/gold_orb_class.php');
                $gorb_obj = new gold_orb;
            }
            if ($parm == 'nolink'){
                return $birthday_out . $gorb_obj->show_orb($user_id, $user_name) ;
            }else{
                return $birthday_out . " <a title='" . $user_birthday = "$BIRTHDAY_datepart[2].{$BIRTHDAY_datepart[1]}.{$birthday_showyear}" . "' href='" . e_BASE . "user.php?id." . $user_id . "'>" . $gorb_obj->show_orb($user_id, $user_name) . "</a>";
            }
        }else{
            if ($parm == 'nolink'){
                return $birthday_out . $tp->toHTML($user_name, false) ;
            }else{
                return $birthday_out . " <a title='" . $user_birthday = "$BIRTHDAY_datepart[2].{$BIRTHDAY_datepart[1]}.{$birthday_showyear}" . "' href='" . e_BASE . "user.php?id." . $user_id . "'>" . $tp->toHTML($user_name, false) . "</a>";
            }
        }
    }
    function sc_birthday_update(){
        return $this->data['datebirth'];
    }
    function sc_birthday_upage(){
        global $user_id, $tp, $birthday_show, $BIRTHDAY_datepart, $birthday_showyear, $user_name;
        if ($parm == 'nolink'){
            return $birthday_show ;
        }else{
            return $birthday_out . " <a href='" . e_BASE . "user.php?id." . $user_id . "'>" . $birthday_show . "</a>";
        }
    }
    function sc_birthday_demo(){
        global $pref;
        if (check_class($pref['birthday_demographic'])){
            return '<a href="' . e_PLUGIN . 'birthday/index.php" >' . BIRTHDAY_LAN_9 . '</a>';
        }else{
            return '';
        }
    }
    function sc_birthday_range(){
        global $birthday_range;
        return $birthday_range;
    }
    function sc_birthday_total_range(){
        global $birthday_total_range;
        return $birthday_total_range;
    }
    function sc_birthday_avatar(){
        return $this->data['avatar'];
    }
    function sc_birthday_total(){
        global $birthday_total;
        return $birthday_total;
    }
    function sc_birthday_bar(){
        global $birthday_bar;
        return $birthday_bar;
    }
    function sc_birthday_undefined(){
        global $birthday_noentry;
        return $birthday_noentry;
    }
    function sc_birthday_percent(){
        global $birthday_percent;
        return $birthday_percent;
    }
    function sc_birthday_rangecolour($param){
        return $this->data['rangecolour'][$param];
    }
    function sc_birthday_rangename($param){
        return $param;
    }
    function sc_birthday_rangevalue($param){
        return $param;
    }
	function sc_birthday_bargraph(){
		return "<img src='".e_PLUGIN."pchart/cache/birthday.png' />";
	}
    function sc_birthday_piechart(){
        return;
    }
}
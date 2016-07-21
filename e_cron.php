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

if (!defined('e107_INIT')){
    exit;
}
require_once(e_PLUGIN . "birthday/handlers/birthday_class.php");
// include_lan(e_PLUGIN.'/pm/languages/English_mailer.php');
//e107::lan('birthday', 'admin', true); // language file

class birthday_cron extends birthdayClass{ // include plugin-folder in the name.
 //   private $logRequirement = 0; // Flag to determine logging level
 //   private $debugLevel = 0; // Used for internal debugging
 //   private $logHandle = null;
    private $pmClass; // Calendar library routines
 //   private $e107;
    private $mailManager;
 //   private $ourDB; // Used for some things


    /**
    * Cron configuration
    *
    * Defines one or more cron tasks to be performed
    *
    * @return array of task arrays
    */
    public function config(){
        $cron = array();
        $cron[] = array(
            'name' => BIRTHDAY_CRON_ENAME,
            'category' => 'plugin',
            'function' => 'processEmail',
            'description' => BIRTHDAY_CRON_ENAMEDESC
            );
        return $cron;
    }



    /**
    * Logging routine - writes lines to a text file
    *
    * Auto-opens log file (if necessary) on first call
    *
    * @param string $logText - body of text to write
    * @param boolean $closeAfter - if TRUE, log file closed before exit; otherwise left open
    * @return none
    */
    function logLine($logText, $closeAfter = false, $addTimeDate = false){
        if ($this->logRequirement == 0) return;

        $logFilename = e_LOG . 'pm_bulk.txt';
        if ($this->logHandle == null){
            if (!($this->logHandle = fopen($logFilename, "a"))){ // Problem creating file?
                echo "File open failed!<br />";
                $this->logRequirement = 0;
                return;
            }
        }

        if (fwrite($this->logHandle, ($addTimeDate ? date('D j M Y G:i:s') . ': ' : '') . $logText . "\r\n") == false){
            $this->logRequirement = 0;
            echo 'File write failed!<br />';
        }

        if ($closeAfter){
            fclose($this->logHandle);
            $this->logHandle = null;
        }
    }

    /**
    * Called to process outstanding PMs (which are always bulk lists)
    *
    * Emails are added to the queue.
    * Various events are logged in a text file
    *
    * @return none
    */
    public function xxprocessPM(){
        global $pref;

        require_once(e_PLUGIN . 'pm/pm_class.php');

        $this->startTime = mktime(0, 0, 0, date('n'), date('d'), date('Y')); // Date for start processing

        $this->logRequirement = varset($pref['eventpost_emaillog'], 0);
        if ($this->debugLevel >= 2) $this->logRequirement = 2; // Force full logging if debug

        // Start of the 'real' code
        if ($this->ourDB->select('generic', '*', "`gen_type` = 'pm_bulk' LIMIT 1")){
            $pmRow = $this->ourDB->fetch(MYSQL_ASSOC);
            $this->logLine("\r\n\r\n" . str_replace('--NUM--', $pmRow['gen_intdata'], LAN_EC_PM_06) . date('D j M Y G:i:s'));

            $this->ourDB->delete('generic', "`gen_type` = 'pm_bulk' AND `gen_id` = " . $pmRow['gen_id']);

            $pmData = e107::unserialize($pmRow['gen_chardata']);
            unset($pmRow);
            $this->pmClass = new private_message;
            $this->pmClass->add($pmData);
            $this->logLine(' .. Run completed', true, true);
        }
        return true;
    }

    private function checkMailManager(){
        if ($this->mailManager == null){
            require_once(e_HANDLER . 'mail_manager_class.php');
            $this->mailManager = new e107MailManager();
        }
    }
}

?>
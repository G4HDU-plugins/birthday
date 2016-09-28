<?php

/**
 * G4HDU version checker
 *
 * Copyright (C) 2008-2016 Barry Keal G4HDU http://www.keal.me.uk
 * released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * @author Barry Keal e107@keal.me.uk>
 * @copyright Copyright (C) 2008-2016 Barry Keal G4HDU
 * @package     e107
 * @subpackage  e_version 
 * @license GPL
 * @version 2.1.0
 * @category e107 utility
 * 
 * @todo Documentation
 */

e107::css('inline', '
	/* e_version */
	#eversionMoreBox{
        width:100%;
        text-align:center;
    }
    #eversionMore{
        font-size:1.6em;
        color:#0000ff;
        cursor:pointer;
        }
    #eversionContent{
        text-align:left;
        display:none;
        }
	');
e107::js('inline', "
	/* e_version js */
    $(document).ready(function(){
        $('#eversionMore').click(function(){
            $('#eversionContent').slideToggle()}
        );
    });
");
/**
 * e_version
 * 
 * @package   
 * @author Auto Assign
 * @copyright Father Barry
 * @version 2016
 * @access public
 */
class e_version
{

    static private $prefs;
    static private $remoteVersion;
    static private $localVersion;

    /**
     * e_version::__construct()
     * 
     * @return
     */

    function __construct()
    {

    }

    /**
     * e_version::updateAvailable()
     * 
     * @param string $plugName
     * @return
     */
    static private function updateAvailable($plugName)
    {
		return;
        if (self::$prefs['e_update'] != date('z'))
        {
            $url = (string )self::$prefs['e_remote'];
            if (!empty($url))
            {

                $xml = new SimpleXMLElement($url, LIBXML_NOCDATA, true);
                self::$remoteVersion = (string )$xml->attributes()->version;

                self::$prefs['e_version'] = self::$remoteVersion;
                self::$prefs['e_update'] = date('z');
                self::$prefs = e107::getConfig($plugName)->setPref(self::$prefs)->save(false);
            }
        } else
        {

            self::$remoteVersion = self::$prefs['e_version'];

        }

        self::$localVersion = e107::getPref('plug_installed')[$plugName];

        $status = version_compare(self::$localVersion, self::$remoteVersion);

        $text = '';
        if ($status > 0)
        {
            $retval = true;

        } else
        {
            $retval = false;
        }

        return $retval;
    }

    /**
     * e_version::genUpdate()
     * 
     * @param string $plugName
     * @return
     */
    static function genUpdate($plugName)
    {
return;
        self::$prefs = e107::getPlugPref($plugName);

        $github = str_replace('/master/plugin.xml', '', self::$prefs['e_remote']);
        $github = str_replace('raw.githubusercontent.com', 'github.com', $github);

        if (self::updateAvailable($plugName))
        {
            $help_text = "There is an update available";

        } else
        {
            $help_text = "You have the latest version installed";
        }
        $help_text .= "
        <div id='eversionMoreBox'><i id='eversionMore' class='fa fa-info-circle' aria-hidden='true'></i>
            <div id='eversionContent'>
                <a href='{$github}' target='_blank' >Github</a><br />
             <a href='{$github}/issues' target='_blank' >Issues</a><br />
             <a href='{$github}/wiki' target='_blank' >Wiki</a><br />
                <a href='" . e_ADMIN . "plugin.php??srch={$plugName}&go=&mode=online' >Plugin Manager</a><br />
            </div>
        </div>";
        return array('caption' => "Update", 'text' => $help_text);
    }
}

?>
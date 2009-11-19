<?php
/*
 * @version $Id$
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copynetwork (C) 2003-2006 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org/
 ----------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: DURIEUX David
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}


class PluginTrackerConfig extends CommonDBTM {

	function __construct() {
		$this->table="glpi_plugin_tracker_config";
		$this->type=PLUGIN_TRACKER_SNMP_CONFIG;
	}

	function initConfig($version) {
		global $DB,$CFG_GLPI;
		$url = str_replace("http:","https:",$CFG_GLPI["url_base"]);
		$query = "INSERT INTO ".$this->table."(
                              `ID`, `version`, `ssl_only`, `authsnmp`, `inventory_frequence`
                              `criteria1_ip`, `criteria1_name`, `criteria1_serial`,
                              `criteria1_macaddr`, `criteria2_ip`, `criteria2_name`,
                              `criteria2_serial`, `criteria2_macaddr`)
                VALUES ('1', '".$version."', '1', 'DB', '24', '0', '0', '0', '0', '0', '0', '0', '0');";

		$DB->query($query);
	}
	
	/* Function to get the value of a field */
	function getValue($field) {
		global $DB;

		$query = "SELECT ".$field."
                FROM ".$this->table."
                WHERE `ID` = '1';";
		if ($result = $DB->query($query)) {
			if ($this->fields = $DB->fetch_row($result)) {
				return $this->fields['0'];
         }
		}
		return false;
	}

	// Confirm if the functionality is activated, or not
	function isActivated($functionality) {
		
		if (!($this->getValue($functionality))) {
			return false;
      } else {
			return true;
      }
	}


	function defineTabs($ID,$withtemplate) {
		global $LANG,$CFG_GLPI;

		$ong[1]=$LANG['plugin_tracker']["functionalities"][2];
      $ong[2]=$LANG['plugin_tracker']['config'][1];
		$ong[3]=$LANG['plugin_tracker']["functionalities"][3]." - ".$LANG['plugin_tracker']["functionalities"][5];
//		$ong[3]=$LANG['plugin_tracker']["functionalities"][3]." - ".$LANG['plugin_tracker']["discovery"][3];

      $ong[7]=$LANG['title'][38];
		return $ong;
	}
	

	
	function showForm($target,$ID) {
		global $LANG,$CFG_GLPI;

		echo "<form method='post' name='functionalities_form' id='functionalities_form'  action='".$target."'>";
		echo "<table class='tab_cadre_fixe' cellpadding='5'>";
		
		echo "<tr>";
		echo "<th colspan='4'>";
		echo $LANG['plugin_tracker']["functionalities"][2]."&nbsp;:";
		echo "</th>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][27]."&nbsp;:</td>";
		echo "<td width='20%'>";
		dropdownYesNo("ssl_only", $this->isActivated('ssl_only'));
		echo "</td>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][16]."&nbsp;:</td>";
		echo "<td width='20%'>";
		unset($ArrayValues);
		$ArrayValues['DB']= $LANG['plugin_tracker']["functionalities"][17];
		$ArrayValues['file']= $LANG['plugin_tracker']["functionalities"][18];
		dropdownArrayValues('authsnmp', $ArrayValues,$this->getValue('authsnmp'));
		echo "</td>";
      echo "</tr>";

 		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']['config'][0]."&nbsp;:</td>";
		echo "<td>";
      dropdownInteger("inventory_frequence",$this->getValue('inventory_frequence'),1,240);
		echo "</td>";
		echo "<td></td>";
		echo "<td>";
		echo "</td>";
      echo "</tr>";

		echo "<tr>";
		echo "<th colspan='2'>";
		echo $LANG['plugin_tracker']["discovery"][6]."&nbsp;:";
		echo "</th>";
		echo "<th colspan='2'>";
		echo $LANG['plugin_tracker']["discovery"][6]." 2&nbsp;:";
		echo "</th>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td width='500'>".$LANG["networking"][14]."&nbsp;:</td>";
		echo "<td>";
		dropdownYesNo("criteria1_ip", $this->isActivated('criteria1_ip'));
		echo "</td>";
		echo "<td width='500'>".$LANG["networking"][14]."&nbsp;:</td>";
		echo "<td>";
		dropdownYesNo("criteria2_ip", $this->isActivated('criteria2_ip'));
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG["common"][16]."&nbsp;:</td>";
		echo "<td>";
		dropdownYesNo("criteria1_name", $this->isActivated('criteria1_name'));
		echo "</td>";
		echo "<td>".$LANG["common"][16]."&nbsp;:</td>";
		echo "<td>";
		dropdownYesNo("criteria2_name", $this->isActivated('criteria2_name'));
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG["common"][19]."&nbsp;:</td>";
		echo "<td>";
		dropdownYesNo("criteria1_serial", $this->isActivated('criteria1_serial'));
		echo "</td>";
		echo "<td>".$LANG["common"][19]."&nbsp;:</td>";
		echo "<td>";
		dropdownYesNo("criteria2_serial", $this->isActivated('criteria2_serial'));
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['device_iface'][2]."&nbsp;:</td>";
		echo "<td>";
		dropdownYesNo("criteria1_macaddr", $this->isActivated('criteria1_macaddr'));
		echo "</td>";
		echo "<td>".$LANG['device_iface'][2]."&nbsp;:</td>";
		echo "<td>";
		dropdownYesNo("criteria2_macaddr", $this->isActivated('criteria2_macaddr'));
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'><td align='center' colspan='4'>";
		echo "<input type='hidden' name='tabs' value='config' />";
		echo "<input type='submit' name='update' value=\"".$LANG["buttons"][2]."\" class='submit' ></div></td></tr>";
		echo "</table></form>";

	}
}



class PluginTrackerConfigDiscovery extends CommonDBTM {

	function __construct() {
		$this->table="glpi_plugin_tracker_config_discovery";
		$this->type=-1;
	}

	function initConfig() {
		global $DB;

		$query = "INSERT INTO ".$this->table."(
                            `ID`, `link_ip`, `link_name`, `link_serial`, `link2_ip`, `link2_name`,
                           `link2_serial`)
                VALUES ('1', '0', '0', '0', '0', '0', '0');";

		$DB->query($query);
	}

	/* Function to get the value of a field */
	function getValue($field) {
		global $DB;

		$query = "SELECT ".$field."
                FROM ".$this->table."
                WHERE `ID` = '1';";
		if ($result = $DB->query($query)) {
			if ($this->fields = $DB->fetch_row($result)) {
				return $this->fields['0'];
         }
		}
		return false;
	}

	// Confirm if the functionality is activated, or not
	function isActivated($functionality) {

		if (!($this->getValue($functionality))) {
			return false;
      } else {
			return true;
      }
	}

   function showForm($target,$ID) {
		global $LANG;

		echo "<form method='post' name='functionalities_form' id='functionalities_form'  action='".$target."'>";
		echo "<table class='tab_cadre_fixe' cellpadding='5'>";



		echo "<tr class='tab_bg_1'><td align='center' colspan='3'>";
		echo "<input type='hidden' name='tabs' value='snmp_discovery' />";
		echo "<input type='submit' name='update' value=\"".$LANG["buttons"][2]."\" class='submit' ></div></td></tr>";
		echo "</table></form>";
	}
}



class PluginTrackerConfigSNMPScript extends CommonDBTM {

	function __construct() {
		$this->table="glpi_plugin_tracker_config_snmp_script";
		$this->type=-1;
	}


	function initConfig() {
		global $DB;
		
		$query = "INSERT INTO ".$this->table."(
                            `ID`, `nb_process`, `logs`,`lock`)
                VALUES ('1', '1', '0', '0');";
		
		$DB->query($query);
	}
	

	/* Function to get the value of a field */
	function getValue($field) {
		global $DB;

		$query = "SELECT ".$field."
                FROM ".$this->table."
                WHERE `ID` = '1';";
		$result = $DB->query($query);
		if ($this->fields = $DB->fetch_row($result)) {
			return $this->fields['0'];
      }
		return false;
	}


	function showForm($target,$ID) {
		global $LANG;
		
		echo "<form method='post' name='functionalities_form' id='functionalities_form'  action='".$target."'>";
		echo "<table class='tab_cadre_fixe' cellpadding='5'>";
		
		echo "<tr>";
		echo "<th colspan='2'>";
		echo $LANG['plugin_tracker']["functionalities"][3]." - ".$LANG['plugin_tracker']["functionalities"][5]." :";
		echo "</th>";
		echo "</tr>";
		
		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][53]."</td>";
		echo "<td>";
		dropdownInteger("nb_process", $this->getValue('nb_process'),1,100);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][52]."</td>";
		echo "<td>";
		unset($ArrayValues);
		$ArrayValues[]= $LANG["choice"][0];
		$ArrayValues[]= $LANG["choice"][1];
		$ArrayValues[]= $LANG["setup"][137];
		dropdownArrayValues('logs', $ArrayValues,$this->getValue('logs'));
		echo "</td>";
		echo "</tr>";
		
		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["agents"][6]."</td>";
		echo "<td>";
		dropdownYesNo("lock", $this->getValue('`lock`'));
		echo "</td>";
		echo "</tr>";
		
		echo "<tr class='tab_bg_1'><td align='center' colspan='3'>";
		echo "<input type='hidden' name='tabs' value='snmp_script' />";
		echo "<input type='submit' name='update' value=\"".$LANG["buttons"][2]."\" class='submit' ></div></td></tr>";
		echo "</table></form>";	
	}
}



class PluginTrackerConfigSNMPNetworking extends CommonDBTM {

	function __construct() {
		$this->table="glpi_plugin_tracker_config_snmp_networking";
		$this->type=-1;
	}	


	function initConfig() {
		global $DB;
		
		$query = "INSERT INTO ".$this->table."(
                            `ID`, `active_device_state`, `history_wire`, `history_ports_state`,
                            `history_unknown_mac`, `history_snmp_errors`, `history_process`)
                VALUES ('1', '0', '0', '0', '0', '0', '0');";
		
		$DB->query($query);
	}
	

	/* Function to get the value of a field */
	function getValue($field) {
		global $DB;

		$query = "SELECT ".$field."
                FROM ".$this->table."
                WHERE `ID` = '1';";
		if ($result = $DB->query($query)) {
			if ($this->fields = $DB->fetch_row($result)) {
				return $this->fields['0'];
         }
		}
		return false;
	}

	// Confirm if the functionality is activated, or not
	function isActivated($functionality) {
		
		if (!($this->getValue($functionality))) {
			return false;
      } else {
			return true;
      }
	}


	
	function showForm($target,$ID) {
		global $LANG;
		
		echo "<form method='post' name='functionalities_form' id='functionalities_form'  action='".$target."'>";
		echo "<table class='tab_cadre_fixe' cellpadding='5'>";
		
		echo "<tr>";
		echo "<th colspan='2'>";
		echo $LANG['plugin_tracker']["functionalities"][3]." - ".$LANG["Menu"][1]." :";
		echo "</th>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][20]."</td>";
		echo "<td>";
		dropdownValue("glpi_dropdown_state", "active_device_state", $this->getValue("active_device_state"));
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][21]."</td>";
		echo "<td>";
		dropdownInteger("history_wire", $this->getValue('history_wire'),0,100);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][22]."</td>";
		echo "<td>";
		dropdownInteger("history_ports_state", $this->getValue('history_ports_state'),0,100);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][23]."</td>";
		echo "<td>";
		dropdownInteger("history_unknown_mac", $this->getValue('history_unknown_mac'),0,100);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][24]."</td>";
		echo "<td>";
		dropdownInteger("history_snmp_errors", $this->getValue('history_snmp_errors'),0,100);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][25]."</td>";
		echo "<td>";
		dropdownInteger("history_process", $this->getValue('history_process'),0,100);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'><td align='center' colspan='3'>";
		echo "<input type='hidden' name='tabs' value='snmp_networking' />";
		echo "<input type='submit' name='update' value=\"".$LANG["buttons"][2]."\" class='submit' ></div></td></tr>";
		echo "</table></form>";	
	}

   function CleanHistory($option) {
      global $DB;

      switch ($option) {
         case "history_process":
            if ($this->getValue("history_process") != 0) {
               $query_delete = "DELETE FROM `glpi_plugin_tracker_processes`
                                WHERE `start_time` < date_add(now(),interval -".
                                       $this->getValue("history_process")." day);";
               $DB->query($query_delete);
            }
            break;
      }
   }
}



class PluginTrackerConfigSNMPPrinter extends CommonDBTM {

	function __construct() {
		$this->table="glpi_plugin_tracker_config_snmp_printer";
		$this->type=-1;
	}	


	function initConfig() {
		global $DB;
		
		$query = "INSERT INTO ".$this->table."(
                `ID`, `active_device_state`, `manage_cartridges`)
                VALUES ('1', '0', '0');";
		
		$DB->query($query);
	}
	

	/* Function to get the value of a field */
	function getValue($field) {
		global $DB;

		$query = "SELECT ".$field."
                FROM ".$this->table."
                WHERE `ID` = '1';";
		if ($result = $DB->query($query)) {
			if ($this->fields = $DB->fetch_row($result)) {
				return $this->fields['0'];
         }
		}
		return false;
	}

	// Confirm if the functionality is activated, or not
	function isActivated($functionality) {
		
		if (!($this->getValue($functionality))) {
			return false;
      } else {
			return true;
      }
	}


	
	function showForm($target,$ID) {
		global $LANG;
		
		echo "<form method='post' name='functionalities_form' id='functionalities_form'  action='".$target."'>";
		echo "<table class='tab_cadre_fixe' cellpadding='5'>";
		
		echo "<tr>";
		echo "<th colspan='2'>";
		echo $LANG['plugin_tracker']["functionalities"][3]." - ".$LANG["Menu"][2]." :";
		echo "</th>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td>".$LANG['plugin_tracker']["functionalities"][30]."</td>";
		echo "<td>";
		dropdownValue("glpi_dropdown_state", "active_device_state", $this->getValue("active_device_state"));
		echo "</td>";
		echo "</tr>";

//		echo "<tr class='tab_bg_1'>";
//		echo "<td>".$LANG['plugin_tracker']["functionalities"][31]."</td>";
//		echo "<td>";
//		dropdownYesNo("manage_cartridges", $this->isActivated('manage_cartridges'));
//		echo "</td>";
//		echo "</tr>";

		echo "<tr class='tab_bg_1'><td align='center' colspan='3'>";
		echo "<input type='hidden' name='tabs' value='snmp_printer' />";
		echo "<input type='submit' name='update' value=\"".$LANG["buttons"][2]."\" class='submit' ></div></td></tr>";
		echo "</table></form>";	
	}
}		

?>
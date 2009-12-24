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

class PluginTrackerRangeIP extends CommonDBTM {

	function __construct() {
		$this->table = "glpi_plugin_tracker_rangeip";
		$this->type = PLUGIN_TRACKER_SNMP_RANGEIP;
	}


	function showForm($target, $ID = '') {
		global $DB,$CFG_GLPI,$LANG;

		if ($ID!='') {
			$this->getFromDB($ID);
      } else {
			$this->getEmpty();
      }
		$this->showTabs($ID, "",$_SESSION['glpi_tab']);
		echo "<div align='center'><form method='post' name='' id=''  action=\"" . $target . "\">";

		echo "<table class='tab_cadre' cellpadding='5' width='950'><tr><th colspan='2'>";
		echo $LANG['plugin_tracker']["rangeip"][2];
		echo " :</th></tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>" . $LANG["common"][16] . "</td>";
		echo "<td align='center'>";
		echo "<input type='text' name='name' value='".$this->fields["name"]."'/>";
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>" . $LANG['plugin_tracker']["rangeip"][0] . "</td>";
		echo "<td align='center'>";
		echo "<input type='text' name='ifaddr_start' value='".$this->fields["ifaddr_start"]."'/>";
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>" . $LANG['plugin_tracker']["rangeip"][1] . "</td>";
		echo "<td align='center'>";
		echo "<input type='text' name='ifaddr_end' value='".$this->fields["ifaddr_end"]."'/>";
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>" . $LANG['plugin_tracker']["agents"][12] . "</td>";
		echo "<td align='center'>";
		dropdownValue("glpi_plugin_tracker_agents","FK_tracker_agents_discover",$this->fields["FK_tracker_agents_discover"],0);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>" . $LANG['plugin_tracker']["agents"][13] . "</td>";
		echo "<td align='center'>";
		dropdownValue("glpi_plugin_tracker_agents","FK_tracker_agents_query",$this->fields["FK_tracker_agents_query"],0);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>" . $LANG['plugin_tracker']["discovery"][3] . "</td>";
		echo "<td align='center'>";
		dropdownYesNo("discover",$this->fields["discover"]);
		echo "</td>";
		echo "</tr>";
		
		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>" . $LANG['plugin_tracker']["rangeip"][3] . "</td>";
		echo "<td align='center'>";
		dropdownYesNo("query",$this->fields["query"]);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>".$LANG['entity'][0]."</td>";
		echo "<td align='center'>";
		dropdownValue('glpi_entities', 'FK_entities',$this->fields["FK_entities"]);
		echo "</td>";
		echo "</tr>";


		echo "<tr class='tab_bg_1'><td align='center' colspan='3'>";
		if ($ID=='') {
			echo "<div align='center'><input type='submit' name='add' value=\"" . $LANG["buttons"][8] . "\" class='submit' >";
		} else {
			echo "<input type='hidden' name='ID' value='" . $ID . "'/>";
			echo "<div align='center'><input type='submit' name='update' value=\"" . $LANG["buttons"][7] . "\" class='submit' >";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='delete' value=\"" . $LANG["buttons"][6] . "\" class='submit'>";
		}
		echo "</td></tr>";
		echo "</table></form></div>";
	}



   function Counter($agent_id, $type) {
      global $DB;
      
      $count = 0;
      switch ($type) {

         case "discover":
            $query = "SELECT COUNT(*) as count FROM `glpi_plugin_tracker_rangeip`
               WHERE `FK_tracker_agents_discover`='".$agent_id."'
                  AND `discover`='1' ";
            break;

         case "query":
            $query = "SELECT COUNT(*) as count FROM `glpi_plugin_tracker_rangeip`
               WHERE `FK_tracker_agents_query`='".$agent_id."'
                  AND `query`='1' ";
            break;

      }

      if ($result = $DB->query($query)) {
         $res = $DB->fetch_assoc($result);
         $count = $res["count"];
      }
      return $count;
   }


   function ListRange($agent_id, $type) {
      global $DB;

      $ranges = array();
      switch ($type) {

         case "discover":
            $query = "SELECT * FROM `glpi_plugin_tracker_rangeip`
               WHERE `FK_tracker_agents_discover`='".$agent_id."'
                  AND `discover`='1' ";
            break;

         case "query":
            $query = "SELECT * FROM `glpi_plugin_tracker_rangeip`
               WHERE `FK_tracker_agents_query`='".$agent_id."'
                  AND `query`='1' ";
            break;

      }
      if ($result = $DB->query($query)) {
         if ($DB->numrows($result) != 0) {
            while ($data=$DB->fetch_array($result)) {
               $ranges[$data["ID"]] = $data;
            }
         }
      }
      return $ranges;
   }
   
}

?>
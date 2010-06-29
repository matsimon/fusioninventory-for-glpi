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

class PluginFusioninventoryAgent extends CommonDBTM {
   function __construct() {
		$this->table = "glpi_plugin_fusioninventory_agents";
		$this->type = 'PluginFusioninventoryAgent';
	}

   function defineTabs($options=array()){
		global $LANG,$CFG_GLPI;

      $ptc = new PluginFusioninventoryConfig;

      $ong = array();
		if ((isset($this->fields['id'])) AND ($this->fields['id'] > 0)){
         $ong[1]=$LANG['plugin_fusioninventory']["agents"][9];
         if (($ptc->isActivated('remotehttpagent')) 
              AND(PluginFusioninventoryProfile::haveRight("Fusioninventory","remotecontrol","w"))) {
            $ong[2]=$LANG['plugin_fusioninventory']["task"][2];
         }
      }
		return $ong;
	}

	function PushData($id, $key) {
		$this->getFromDB($id);
		// Name of server
		// $this->fields["name"];
		
		$xml = "<snmp>\n";
		// ** boucle sur les équipements réseau
		// ** détection des équipements avec le bon status et l'IP dans la plage de l'agent
		//  Ecriture du fichier xml pour l'envoi à l'agent
	
		$xml .= "</snmp>\n";
		// Affichage du fichier xml pour que l'agent récupère les paramètres
		echo $xml;
	}


	function showForm($id, $options=array()) {
		global $DB,$CFG_GLPI,$LANG;

      if ($id!='') {
			$this->getFromDB($id);
      } else {
			$this->getEmpty();
      }

      $ptc = new PluginFusioninventoryConfig;

		$this->showTabs($options);
      $this->showFormHeader($options);
		echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_fusioninventory']["agents"][23]." :</td>";
		echo "<td align='center'>";
      if (($this->fields["items_id"] != "0") AND ($this->fields["items_id"] != "")) {
         $oComputer = new Computer();
         $oComputer->getFromDB($this->fields["items_id"]);
         echo $oComputer->getLink(1);
         echo "<input type='hidden' name='items_id' value='".$this->fields["items_id"]."'/>";
      } else {
         Computer_Item::dropdownConnect(COMPUTER_TYPE,COMPUTER_TYPE,'items_id', $_SESSION['glpiactive_entity']);
      }
		echo "</td>";

      if ($ptc->getValue('wol') == "1") {
         echo "<td>".$LANG['plugin_fusioninventory']['config'][6]." :</td>";
         echo "<td align='center'>";
         Dropdown::showYesNo("module_wakeonlan",$this->fields["module_wakeonlan"]);
         echo "</td>";
		} else {
         echo "<td colspan='2'></td>";
      }

		echo "<tr class='tab_bg_1'>";
      echo "<td>Token :</td>";
		echo "<td align='center' colspan='3'>";
		echo $this->fields["token"];
		echo "</td>";
		echo "</tr>";

		$this->showFormButtons($options);

      echo "<div id='tabcontent'></div>";
      echo "<script type='text/javascript'>loadDefaultTab();</script>";

      return true;
	}



   function showFormAdvancedOptions($id, $options=array()) {
      global $DB,$CFG_GLPI,$LANG;
      
      $this->showTabs($options);
      $this->showFormHeader($options);

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>".$LANG['plugin_fusioninventory']["agents"][3]."</td>";
		echo "<td align='center'>";
		Dropdown::showInteger("threads_discovery", $this->fields["threads_discovery"],1,400);
		echo "</td>";
		echo "</tr>";

		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>" . $LANG['plugin_fusioninventory']["agents"][2] . "</td>";
		echo "<td align='center'>";
		Dropdown::showInteger("threads_query", $this->fields["threads_query"],1,200);
		echo "</td>";
		echo "</tr>";

      $this->showFormButtons($options);

      echo "<div id='tabcontent'></div>";
      echo "<script type='text/javascript'>loadDefaultTab();</script>";

      return true;
   }



   function InfosByKey($key) {
      global $DB;

      $query = "SELECT * FROM `glpi_plugin_fusioninventory_agents`
      WHERE `key`='".$key."' LIMIT 1";

      $agent = array();
      if ($result = $DB->query($query)) {
         if ($DB->numrows($result) != 0) {
            $agent = $DB->fetch_assoc($result);
         }
      }
      return $agent;
   }

}

?>

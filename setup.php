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
// Original Author of file: David DURIEUX
// Purpose of file:
// ----------------------------------------------------------------------

include_once ("includes.php");

// Init the hooks of fusioninventory
function plugin_init_fusioninventory() {
	global $PLUGIN_HOOKS,$CFG_GLPI,$LANG;

   Plugin::registerClass('PluginFusioninventoryAgent');
   Plugin::registerClass('PluginFusioninventoryAgentProcess');
   Plugin::registerClass('PluginFusioninventoryConfig');
   Plugin::registerClass('PluginFusioninventoryConstructDevice');
   Plugin::registerClass('PluginFusioninventoryModelInfos');
   Plugin::registerClass('PluginFusioninventoryNetworkEquipment');
   Plugin::registerClass('PluginFusioninventoryPrinter');
   Plugin::registerClass('PluginFusioninventoryIPRange');
   Plugin::registerClass('PluginFusioninventoryConfigSNMPSecurity');
   Plugin::registerClass('PluginFusioninventoryNetworkPortLog');
   Plugin::registerClass('PluginFusioninventoryTask');
   Plugin::registerClass('PluginFusioninventoryUnknownDevice');
   Plugin::registerClass('PluginFusioninventoryNetworkport2',
                         array('classname'=>'glpi_networkports'));

	//array_push($CFG_GLPI["specif_entities_tables"],"glpi_plugin_fusioninventory_errors");

	$PLUGIN_HOOKS['init_session']['fusioninventory'] = 'plugin_fusioninventory_initSession';
	$PLUGIN_HOOKS['change_profile']['fusioninventory'] = 'plugin_fusioninventory_changeprofile';

	$PLUGIN_HOOKS['cron']['fusioninventory'] = 20*MINUTE_TIMESTAMP; // All 20 minutes

   $PLUGIN_HOOKS['add_javascript']['fusioninventory']="script.js";

	if (isset($_SESSION["glpiID"])) {

		if (haveRight("configuration", "r") || haveRight("profile", "w")) {// Config page
			$PLUGIN_HOOKS['config_page']['fusioninventory'] = 'front/functionalities.form.php';
      }

		// Define SQL table restriction of entity
		$CFG_GLPI["specif_entities_tables"][] = 'glpi_plugin_fusioninventory_discovery';
		$CFG_GLPI["specif_entities_tables"][] = 'glpi_plugin_fusioninventory_ipranges';
      $CFG_GLPI["specif_entities_tables"][] = 'glpi_plugin_fusioninventory_unknowndevices';

		if(isset($_SESSION["glpi_plugin_fusioninventory_installed"]) && $_SESSION["glpi_plugin_fusioninventory_installed"]==1) {

			$PLUGIN_HOOKS['use_massive_action']['fusioninventory']=1;
         $PLUGIN_HOOKS['pre_item_delete']['fusioninventory'] = 'plugin_pre_item_delete_fusioninventory';
			$PLUGIN_HOOKS['pre_item_purge']['fusioninventory'] = 'plugin_pre_item_purge_fusioninventory';
			$PLUGIN_HOOKS['item_update']['fusioninventory'] = 'plugin_item_update_fusioninventory';
         $PLUGIN_HOOKS['item_add']['fusioninventory'] = 'plugin_item_add_fusioninventory';

			$report_list = array();
         $report_list["report/switch_ports.history.php"] = "Historique des ports de switchs";
         $report_list["report/ports_date_connections.php"] = "Ports de switchs non connectés depuis xx mois";
			$PLUGIN_HOOKS['reports']['fusioninventory'] = $report_list;

			if (haveRight("snmp_models", "r") || haveRight("snmp_authentication", "r")) {
				$PLUGIN_HOOKS['menu_entry']['fusioninventory'] = true;
         }

         // Tabs for each type
         $PLUGIN_HOOKS['headings']['fusioninventory'] = 'plugin_get_headings_fusioninventory';
         $PLUGIN_HOOKS['headings_action']['fusioninventory'] = 'plugin_headings_actions_fusioninventory';

         if (PluginFusioninventoryAuth::haveRight("snmp_models","r")
            OR PluginFusioninventoryAuth::haveRight("snmp_authentication","r")
            OR PluginFusioninventoryAuth::haveRight("iprange","r")
            OR PluginFusioninventoryAuth::haveRight("agents","r")
            OR PluginFusioninventoryAuth::haveRight("agentsprocesses","r")
            OR PluginFusioninventoryAuth::haveRight("unknowndevices","r")
            OR PluginFusioninventoryAuth::haveRight("reports","r")
            ) {

            $PLUGIN_HOOKS['menu_entry']['fusioninventory'] = true;
            if (PluginFusioninventoryAuth::haveRight("snmp_models","w")) {
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['add']['models'] = 'front/models.form.php?add=1';
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['search']['models'] = 'front/models.php';
            }
            if (PluginFusioninventoryAuth::haveRight("snmp_authentication","w")) {
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['add']['snmp_auth'] = 'front/configsnmpsecurity.form.php?add=1';
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['search']['snmp_auth'] = 'front/configsnmpsecurity.php';
            }
            if (PluginFusioninventoryAuth::haveRight("agents","w")) {
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['add']['agents'] = 'front/agent.form.php?add=1';
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['search']['agents'] = 'front/agent.php';
            }

            if (PluginFusioninventoryAuth::haveRight("iprange","w")) {
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['add']['iprange'] = 'front/iprange.form.php?add=1';
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['search']['iprange'] = 'front/iprange.php';
            }

            $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['add']['constructdevice'] = 'front/construct_device.form.php?add=1';
            $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['search']['constructdevice'] = 'front/construct_device.php';

            if (PluginFusioninventoryAuth::haveRight("configuration","r")) {
               $PLUGIN_HOOKS['submenu_entry']['fusioninventory']['config'] = 'front/functionalities.form.php';
            }
			}
         $PLUGIN_HOOKS['submenu_entry']['fusioninventory']["<img  src='".GLPI_ROOT."/plugins/fusioninventory/pics/books.png' title='".$LANG['plugin_fusioninventory']["setup"][16]."' alt='".$LANG['plugin_fusioninventory']["setup"][16]."'>"] = 'front/documentation.php';
		}
	}
}

// Name and Version of the plugin
function plugin_version_fusioninventory() {
	return array('name'    => 'FusionInventory',
                'version' => '2.3.0',
                'author'=>'<a href="mailto:d.durieux@siprossii.com">David DURIEUX</a>
                           & <a href="mailto:v.mazzoni@siprossii.com">Vincent MAZZONI</a>',
                'homepage'=>'http://forge.fusioninventory.org/projects/fusioninventory-for-glpi/',
                'minGlpiVersion' => '0.78'// For compatibility / no install in version < 0.78
   );
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_fusioninventory_check_prerequisites() {
   global $LANG;
	if (GLPI_VERSION >= '0.78') {
		return true;
   } else {
		echo $LANG['plugin_fusioninventory']["errors"][50];
   }
}



function plugin_fusioninventory_check_config() {
	return true;
}



function plugin_fusioninventory_haveTypeRight($type,$right) {
	switch ($type) {
		case 'PluginFusioninventoryError' :
			return PluginFusioninventoryAuth::haveRight("errors",$right);
			break;
	}
	return true;
}

?>
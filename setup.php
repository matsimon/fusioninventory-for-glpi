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

include_once ("plugin_tracker.includes.php");

// Init the hooks of tracker
function plugin_init_tracker() {
	global $PLUGIN_HOOKS,$CFG_GLPI,$LANG;

	// Params - Register type
	registerPluginType('tracker', "PLUGIN_TRACKER_ERROR_TYPE", 5150, array(
		'classname' => 'PluginTrackerErrors',
		'tablename' => 'glpi_plugin_tracker_errors',
		'formpage' => 'front/plugin_tracker.errors.form.php'
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_MODEL", 5151, array(
		'classname' => 'PluginTrackerModelInfos',
		'tablename' => 'glpi_plugin_tracker_model_infos',
		'formpage' => 'front/plugin_tracker.models.form.php',
		'searchpage' => 'front/plugin_tracker.models.php',
		'typename' => $LANG['plugin_tracker']["model_info"][4]
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_SNMP_AUTH", 5152, array(
		'classname' => 'PluginTrackerSNMPAuth',
		'tablename' => 'glpi_plugin_tracker_snmp_connection',
		'formpage' => 'front/plugin_tracker.snmp_auth.form.php',
		'searchpage' => 'front/plugin_tracker.snmp_auth.php',
		'typename' => $LANG['plugin_tracker']["model_info"][3]
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_MAC_UNKNOWN", 5153, array(
		'classname' => 'PluginTrackerUnknownDevice',
		'tablename' => 'glpi_plugin_tracker_unknown_device',
      'formpage' => 'front/plugin_tracker.unknown.form.php',
		'searchpage' => 'front/plugin_tracker.unknown.form.php',
		'typename' => $LANG['plugin_tracker']["processes"][13],
		'deleted_tables' => true,
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_PRINTERS_CARTRIDGES", 5156, array(
		'classname' => 'PluginTrackerPrinters',
		'tablename' => 'glpi_plugin_tracker_printers_cartridges',
		'formpage' => 'front/plugin_tracker.printer_info.form.php',
		'typename' => $LANG["cartridges"][0]
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_SNMP_NETWORKING_PORTS", 5157, array(
		'classname' => 'PluginTrackerNetworking',
		'tablename' => 'glpi_networking_ports'
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_SNMP_AGENTS", 5158, array(
		'classname' => 'PluginTrackerAgents',
		'tablename' => 'glpi_plugin_tracker_agents',
		'formpage' => 'front/plugin_tracker.agents.form.php',
		'searchpage' => 'front/plugin_tracker.agents.php'
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_SNMP_RANGEIP", 5159, array(
		'classname' => 'PluginTrackerRangeIP',
		'tablename' => 'glpi_plugin_tracker_rangeip',
		'formpage' => 'front/plugin_tracker.rangeip.form.php',
		'searchpage' => 'front/plugin_tracker.rangeip.php'
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_AGENTS_PROCESSES", 5161, array(
		'classname' => 'PluginTrackerAgentsProcesses',
		'tablename' => 'glpi_plugin_tracker_agents_processes',
		'formpage' => 'front/plugin_tracker.agents.processes.php',
		'massiveaction_noupdate' => true
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_SNMP_HISTORY", 5162, array(
		'classname' => 'PluginTrackerSNMPHistory',
		'tablename' => 'glpi_plugin_tracker_snmp_history'
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_SNMP_NETWORKING_PORTS2", 5163, array(
		'classname' => 'PluginTrackerNetworking',
		'tablename' => 'glpi_plugin_tracker_networking_ports'
		));

   registerPluginType('tracker', "PLUGIN_TRACKER_SNMP_TMP_NETPORTS", 5164, array(
		'classname' => 'PluginTrackerTmpConnections',
		'tablename' => 'glpi_plugin_tracker_tmp_netports'
		));

	registerPluginType('tracker', "PLUGIN_TRACKER_SNMP_CONFIG", 5165, array(
		'classname' => 'PluginTrackerConfig',
		'tablename' => 'glpi_plugin_tracker_config',
		'formpage' => 'front/plugin_tracker.functionalities.form.php'
		));

   	registerPluginType('tracker', "PLUGIN_TRACKER_TASK", 5166, array(
		'classname' => 'PluginTrackerTask',
		'tablename' => 'glpi_plugin_tracker_task',
      'searchpage' => 'front/plugin_tracker.task.php'
		));

	//array_push($CFG_GLPI["specif_entities_tables"],"glpi_plugin_tracker_errors");
	
	$PLUGIN_HOOKS['init_session']['tracker'] = 'plugin_tracker_initSession';
	$PLUGIN_HOOKS['change_profile']['tracker'] = 'plugin_tracker_changeprofile';

	$PLUGIN_HOOKS['cron']['tracker'] = 20*MINUTE_TIMESTAMP; // All 20 minutes

   $PLUGIN_HOOKS['add_javascript']['tracker']="script.js";

	if (isset($_SESSION["glpiID"])) {

		if (haveRight("config", "w") || haveRight("profile", "w")) {// Config page
			$PLUGIN_HOOKS['config_page']['tracker'] = 'front/plugin_tracker.functionalities.form.php';
      }

		// Define SQL table restriction of entity
		$CFG_GLPI["specif_entities_tables"][] = 'glpi_plugin_tracker_discovery';
		$CFG_GLPI["specif_entities_tables"][] = 'glpi_plugin_tracker_rangeip';

		if(isset($_SESSION["glpi_plugin_tracker_installed"]) && $_SESSION["glpi_plugin_tracker_installed"]==1) {

			$PLUGIN_HOOKS['use_massive_action']['tracker']=1;
			$PLUGIN_HOOKS['pre_item_purge']['tracker'] = 'plugin_pre_item_purge_tracker';
			$PLUGIN_HOOKS['item_update']['tracker'] = 'plugin_item_update_tracker';

			$report_list = array();
         $report_list["report/plugin_tracker.switch_ports.history.php"] = "Historique des ports de switchs";
         $report_list["report/plugin_tracker.ports_date_connections.php"] = "Ports de switchs non connectés depuis xx mois";
			$PLUGIN_HOOKS['reports']['tracker'] = $report_list;

			if (haveRight("snmp_models", "r") || haveRight("snmp_authentification", "r") || haveRight("snmp_scripts_infos", "r") || haveRight("snmp_discovery", "r")) {
				$PLUGIN_HOOKS['menu_entry']['tracker'] = true;
         }

         // Tabs for each type
         $PLUGIN_HOOKS['headings']['tracker'] = 'plugin_get_headings_tracker';
         $PLUGIN_HOOKS['headings_action']['tracker'] = 'plugin_headings_actions_tracker';

         if (plugin_tracker_HaveRight("snmp_models","r")
            OR plugin_tracker_HaveRight("snmp_authentification","r")
            OR plugin_tracker_HaveRight("snmp_iprange","r")
            OR plugin_tracker_HaveRight("snmp_agent","r")
            OR plugin_tracker_HaveRight("snmp_scripts_infos","r")
            OR plugin_tracker_HaveRight("snmp_agent_infos","r")
            OR plugin_tracker_HaveRight("snmp_discovery","r")
            OR plugin_tracker_HaveRight("snmp_report","r")
            ) {

            $PLUGIN_HOOKS['menu_entry']['tracker'] = true;
            if (plugin_tracker_haveRight("snmp_models","w")) {
               $PLUGIN_HOOKS['submenu_entry']['tracker']['add']['models'] = 'front/plugin_tracker.models.form.php?add=1';
               $PLUGIN_HOOKS['submenu_entry']['tracker']['search']['models'] = 'front/plugin_tracker.models.php';
            }
            if (plugin_tracker_haveRight("snmp_authentification","w")) {
               $PLUGIN_HOOKS['submenu_entry']['tracker']['add']['snmp_auth'] = 'front/plugin_tracker.snmp_auth.form.php?add=1';
               $PLUGIN_HOOKS['submenu_entry']['tracker']['search']['snmp_auth'] = 'front/plugin_tracker.snmp_auth.php';
            }
            if (plugin_tracker_haveRight("snmp_agent","w")) {
               $PLUGIN_HOOKS['submenu_entry']['tracker']['add']['agents'] = 'front/plugin_tracker.agents.form.php?add=1';
               $PLUGIN_HOOKS['submenu_entry']['tracker']['search']['agents'] = 'front/plugin_tracker.agents.php';
            }

            if (plugin_tracker_haveRight("snmp_iprange","w")) {
               $PLUGIN_HOOKS['submenu_entry']['tracker']['add']['rangeip'] = 'front/plugin_tracker.rangeip.form.php?add=1';
               $PLUGIN_HOOKS['submenu_entry']['tracker']['search']['rangeip'] = 'front/plugin_tracker.rangeip.php';
            }

            if (plugin_tracker_haveRight("general_config","w")) {
               $PLUGIN_HOOKS['submenu_entry']['tracker']['config'] = 'front/plugin_tracker.functionalities.form.php';
            }
			}
         $PLUGIN_HOOKS['submenu_entry']['tracker']["<img  src='".GLPI_ROOT."/plugins/tracker/pics/books.png' title='".$LANG['plugin_tracker']["setup"][16]."' alt='".$LANG['plugin_tracker']["setup"][16]."'>"] = 'front/plugin_tracker.documentation.php';
		}
	}
}

// Name and Version of the plugin
function plugin_version_tracker() {
	return array( 'name'    => 'Tracker',
		'version' => '2.1.3',
		'author'=>'<a href="mailto:d.durieux@siprossii.com">David DURIEUX</a>',
		'homepage'=>'https://forge.indepnet.net/projects/show/tracker',
      'minGlpiVersion' => '0.72.1'// For compatibility / no install in version < 0.72
   );
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_tracker_check_prerequisites() {
   global $LANG;
	if (GLPI_VERSION >= '0.72.1') {
		return true;
   } else {
		echo $LANG['plugin_tracker']["errors"][50];
   }
}



function plugin_tracker_check_config() {
	return true;
}



function plugin_tracker_haveTypeRight($type,$right) {
	switch ($type) {
		case PLUGIN_TRACKER_ERROR_TYPE :
			return plugin_tracker_haveRight("errors",$right);
			break;
	}
	return true;
}


?>

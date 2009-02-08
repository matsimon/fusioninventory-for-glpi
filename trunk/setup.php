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
// Original Author of file: Nicolas SMOLYNIEC
// Purpose of file:
// ----------------------------------------------------------------------

include_once ("plugin_tracker.includes.php");

// Init the hooks of tracker
function plugin_init_tracker() {
	
	global $PLUGIN_HOOKS,$CFG_GLPI,$LANG,$LANGTRACKER;


	pluginNewType('tracker', "PLUGIN_TRACKER_ERROR_TYPE", 5150, "plugin_tracker_errors", "glpi_plugin_tracker_errors", "front/plugin_tracker.errors.form.php");
	pluginNewType('tracker', "PLUGIN_TRACKER_MODEL", 5151, "plugin_tracker_model_infos", "glpi_plugin_tracker_model_infos", "front/plugin_tracker.models.form.php",$LANGTRACKER["model_info"][4]);
	pluginNewType('tracker', "PLUGIN_TRACKER_SNMP_AUTH", 5152, "plugin_tracker_snmp_auth", "glpi_plugin_tracker_snmp_connection", "front/plugin_tracker.snmp_auth.form.php",$LANGTRACKER["model_info"][3]);
	pluginNewType('tracker', "PLUGIN_TRACKER_MAC_UNKNOW", 5153, "Threads", "glpi_plugin_tracker_unknown_mac", "front/plugin_tracker.processes.unknow_mac.php", $LANGTRACKER["processes"][13]);

	pluginNewType('tracker', "PLUGIN_TRACKER_PRINTERS_CARTRIDGES", 5156, "plugin_tracker_printers", "glpi_plugin_tracker_printers_cartridges", "front/plugin_tracker.printer_info.form.php",$LANG["cartridges"][0]);
	pluginNewType('tracker', "PLUGIN_TRACKER_SNMP_NETWORKING_PORTS", 5157, "plugin_tracker_networking", "glpi_networking_ports");

	//array_push($CFG_GLPI["specif_entities_tables"],"glpi_plugin_tracker_errors");
	
	$PLUGIN_HOOKS['init_session']['tracker'] = 'plugin_tracker_initSession';
	$PLUGIN_HOOKS['change_profile']['tracker'] = 'plugin_tracker_changeprofile';

	if (isset($_SESSION["glpiID"])){

		if (haveRight("config", "w") || haveRight("profile", "w")) // Config page
			$PLUGIN_HOOKS['config_page']['tracker'] = 'front/plugin_tracker.config.php';

	
		if(isset($_SESSION["glpi_plugin_tracker_installed"]) && $_SESSION["glpi_plugin_tracker_installed"]==1) {

			$PLUGIN_HOOKS['use_massive_action']['tracker']=1;
			
			if (haveRight("snmp_models", "r") || haveRight("snmp_authentification", "r") || haveRight("snmp_scripts_infos", "r") || haveRight("snmp_discovery", "r"))
				$PLUGIN_HOOKS['menu_entry']['tracker'] = true;

				// Tabs for each type
				$PLUGIN_HOOKS['headings']['tracker'] = 'plugin_get_headings_tracker';
				$PLUGIN_HOOKS['headings_action']['tracker'] = 'plugin_headings_actions_tracker';

			if (isset($_SESSION["glpi_plugin_tracker_profile"])) {


					$PLUGIN_HOOKS['menu_entry']['tracker'] = true;
					if (plugin_tracker_haveRight("snmp_models","w")){
						$PLUGIN_HOOKS['submenu_entry']['tracker']['add']['models'] = 'front/plugin_tracker.models.form.php?add=1';
						$PLUGIN_HOOKS['submenu_entry']['tracker']['search']['models'] = 'front/plugin_tracker.models.php';
					}
					if (plugin_tracker_haveRight("snmp_authentification","w")){
						$PLUGIN_HOOKS['submenu_entry']['tracker']['add']['snmp_auth'] = 'front/plugin_tracker.snmp_auth.form.php?add=1';
						$PLUGIN_HOOKS['submenu_entry']['tracker']['search']['snmp_auth'] = 'front/plugin_tracker.snmp_auth.php';
					}
						
					$PLUGIN_HOOKS['submenu_entry']['tracker']['config'] = 'front/plugin_tracker.config.php';
			}
		}
	}
}

// Name and Version of the plugin
function plugin_version_tracker()
{
	return array( 'name'    => 'Tracker',
		'minGlpiVersion' => '0.71.3',
		'version' => '1.1.0',
		'author'=>'<a href="mailto:d.durieux@siprossii.com">David DURIEUX</a>',
		'homepage'=>'http://glpi-project.org/wiki/doku.php?id='.substr($_SESSION["glpilanguage"],0,2).':plugins:pluginslist',);
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_tracker_check_prerequisites()
{
	if (GLPI_VERSION>=0.72)
		return true;
	else
		echo "GLPI version not compatible need 0.72";
}

function plugin_tracker_install()
{
	global $DB, $LANG, $CFG_GLPI;
	
	include_once (GLPI_ROOT."/inc/profile.class.php");
	
	if(!TableExists("glpi_plugin_tracker_config"))
		plugin_tracker_installing("1.1.0");
	elseif(TableExists("glpi_plugin_tracker_config") && !FieldExists("glpi_plugin_tracker_config","logs"))
		plugin_tracker_update("1.1.0");

	return true;
}


// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function plugin_tracker_check_config()
{
	return true;
}



function plugin_tracker_haveTypeRight($type,$right)
{
	switch ($type){
		case PLUGIN_TRACKER_ERROR_TYPE :
			return plugin_tracker_haveRight("errors",$right);
			break;
	}
	return true;
}
?>
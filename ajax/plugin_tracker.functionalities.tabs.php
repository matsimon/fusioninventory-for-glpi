<?php
/*
 * @version $Id: computer.tabs.php 8003 2009-02-26 11:03:19Z moyo $
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2009 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

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
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: David DURIEUX
// Purpose of file:
// ----------------------------------------------------------------------

$NEEDED_ITEMS=array();

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT . "/inc/includes.php");
header("Content-Type: text/html; charset=UTF-8");
header_nocache();

if(!isset($_POST["ID"])) {
	exit();
}
if(!isset($_POST["sort"])) $_POST["sort"] = "";
if(!isset($_POST["order"])) $_POST["order"] = "";
if(!isset($_POST["withtemplate"])) $_POST["withtemplate"] = "";




	checkRight("config","w");


	//show computer form to add
	if (!empty($_POST["withtemplate"])) {

		if ($_POST["ID"]>0){
			switch($_POST['glpi_tab']){
				case 2 :
					showSoftwareInstalled($_POST["ID"],$_POST["withtemplate"]);
					break;
				case 3 :
					showConnections($_POST['target'],$_POST["ID"],$_POST["withtemplate"]);
					if ($_POST["withtemplate"]!=2)
						showPortsAdd($_POST["ID"],COMPUTER_TYPE);
					showPorts($_POST["ID"], COMPUTER_TYPE,$_POST["withtemplate"]);
					break;
				case 4 :
					showInfocomForm($CFG_GLPI["root_doc"]."/front/infocom.form.php",COMPUTER_TYPE,$_POST["ID"],1,$_POST["withtemplate"]);
					showContractAssociated(COMPUTER_TYPE,$_POST["ID"],$_POST["withtemplate"]);
					break;
				case 5 :
					showDocumentAssociated(COMPUTER_TYPE,$_POST["ID"],$_POST["withtemplate"]);
					break;
				case 20 :
					showComputerDisks($_POST["ID"],$_POST["withtemplate"]);
					break;
				default :
					if (!displayPluginAction(COMPUTER_TYPE,$_POST["ID"],$_POST['glpi_tab'], $_POST["withtemplate"]))
						showDeviceComputerForm($_POST['target'],$_POST["ID"], $_POST["withtemplate"]);
					break;
			}
		}
	} else {

		switch($_POST['glpi_tab']){
			case -1 :
				showDeviceComputerForm($_POST['target'],$_POST["ID"], $_POST["withtemplate"]);
				showComputerDisks($_POST["ID"],$_POST["withtemplate"]);
				showSoftwareInstalled($_POST["ID"]);
				showConnections($_POST['target'],$_POST["ID"]);
				showPortsAdd($_POST["ID"],COMPUTER_TYPE);
				showPorts($_POST["ID"], COMPUTER_TYPE);
				showInfocomForm($CFG_GLPI["root_doc"]."/front/infocom.form.php",COMPUTER_TYPE,$_POST["ID"]);
				showContractAssociated(COMPUTER_TYPE,$_POST["ID"]);
				showDocumentAssociated(COMPUTER_TYPE,$_POST["ID"]);
				showJobListForItem(COMPUTER_TYPE,$_POST["ID"]);
				showLinkOnDevice(COMPUTER_TYPE,$_POST["ID"]);
				showRegistry($_POST["ID"]);
				displayPluginAction(COMPUTER_TYPE,$_POST["ID"],$_POST['glpi_tab'],$_POST["withtemplate"]);
				break;
			case 2 :
				$config_snmp_script = new glpi_plugin_tracker_config_snmp_script();
				$config_snmp_script->showForm($_SERVER['PHP_SELF'],'1');
				break;
			case 3 :
				$config_discovery = new plugin_tracker_config_discovery;
				$config_discovery->showForm($_SERVER['PHP_SELF'],'1');
				break;
			case 4 :
				$config_snmp_networking = new plugin_tracker_config_snmp_networking();
				$config_snmp_networking->showForm($_SERVER['PHP_SELF'],'1');
				break;
			case 5 :
				
				break;
			case 6 :
				$config_snmp_printer = new plugin_tracker_config_snmp_printer();
				$config_snmp_printer->showForm($_SERVER['PHP_SELF'],'1');
				break;
			case 7 :
				showLinkOnDevice(COMPUTER_TYPE,$_POST["ID"]);
				break;
			case 10 :
				showNotesForm($_POST['target'],COMPUTER_TYPE,$_POST["ID"]);
				break;
			case 11 :
				showDeviceReservations($_POST['target'],COMPUTER_TYPE,$_POST["ID"]);
				break;
			case 12 :
				showHistory(COMPUTER_TYPE,$_POST["ID"]);
				break;
			case 13 :
				ocsEditLock($_POST['target'],$_POST["ID"]);
				break;
			case 14:
				showRegistry($_POST["ID"]);
				break;
			case 20 :
				showComputerDisks($_POST["ID"], $_POST["withtemplate"]);
				break;
			default :
				if (!displayPluginAction(COMPUTER_TYPE,$_POST["ID"],$_POST['glpi_tab'],$_POST["withtemplate"]))
				{
					$config = new plugin_tracker_config();
					$config->showForm($_SERVER['PHP_SELF'],'1');
				}
				break;
		}
	}


	ajaxFooter();


?>
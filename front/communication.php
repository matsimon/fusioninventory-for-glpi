<?php
/*
 * @version $Id$
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
// Original Author of file: MAZZONI Vincent
// Purpose of file: test of communication class
// ----------------------------------------------------------------------

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");

if (!defined('GLPI_ROOT')) {
	define('GLPI_ROOT', '../../..');
}
include (GLPI_ROOT."/inc/includes.php");

$_SESSION["glpi_use_mode"] = 2;

$ptc  = new PluginFusioninventoryCommunication;
$pta  = new PluginFusioninventoryAgent;

$res='';
$errors='';

// ***** For debug only ***** //
//$GLOBALS["HTTP_RAW_POST_DATA"] = gzcompress('');
// ********** End ********** //

if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
   // Get conf tu know if SSL is only
   $fusioninventory_config = new PluginFusionInventoryConfig;
   $ssl = $fusioninventory_config->getValue(19, 'ssl_only');
   if (((isset($_SERVER["HTTPS"])) AND ($_SERVER["HTTPS"] == "on") AND ($ssl == "1"))
       OR ($ssl == "0")) {
      // echo "On continue";
   } else {
      $ptc->setXML("<?xml version='1.0' encoding='ISO-8859-1'?>
<REPLY>
</REPLY>");
      $ptc->noSSL();
      exit();
   }
   $ocsinventory = '0';
   file_put_contents(GLPI_PLUGIN_DOC_DIR."/fusioninventory/dial.log".rand(), gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]));
   $state = $pta->importToken(gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]));
   if ($state == '2') { // agent created
      $ocsinventory = '1';
   }
   $top0 = gettimeofday();
   if (!$ptc->import(gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]))) {
      //if ($ac->connectionOK($errors)) {
      if (1) {
         $res .= "1'".$errors."'";

         $p_xml = gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]);
         $pxml = @simplexml_load_string($p_xml);

         if (isset($pxml->DEVICEID)) {

            $ptc->setXML("<?xml version='1.0' encoding='UTF-8'?>
<REPLY>
</REPLY>");


            $ptt  = new PluginFusionInventoryTask;
            $PluginFusionInventoryConfig        = new PluginFusionInventoryConfig;
            $PluginFusioninventoryTaskjobstatus = new PluginFusioninventoryTaskjobstatus;

            $a_agent = $pta->InfosByKey($pxml->DEVICEID);
            $a_tasks = $ptt->find("`agent_id`='".$a_agent['id']."'", "date");

            $single = 0;

            // Get taskjob in waiting
            $ptc->getTaskAgent($a_agent['id']);



//
//
//            foreach ($a_tasks as $task_id=>$datas) {
//               if (($a_tasks[$task_id]['action'] == 'INVENTORY')
//                       AND ($ptc->is_active("TODO", 'inventoryocs')) //TODO
//                       AND ($a_agent['module_inventory'] == '1')) {
//
//                  $ptc->addInventory();
//                  $input['id'] = $task_id;
//                  $ptt->delete($input);
//                  $ocsinventory = '0';
//                  $single = 1;
//               }
//               if (($a_tasks[$task_id]['action'] == 'NETDISCOVERY')
//                       AND ($ptc->is_active('TODO', 'netdiscovery')) //TODO
//                       AND ($a_agent['module_netdiscovery'] == '1')) {
//                  $single = 1;
//                  $ptc->addDiscovery($pxml, 0); // Want to discovery all range IP
//                  $input['id'] = $task_id;
//                  $ptt->delete($input);
//               }
//               if (($a_tasks[$task_id]['action'] == 'SNMPQUERY')
//                       AND ($ptc->is_active('TODO', 'snmp')) //TODO
//                       AND ($a_agent['module_snmpquery'] == '1')) {
//                  $single = 1;
//                  $ptc->addQuery($pxml, 1);
//                  $input['id'] = $task_id;
//                  $ptt->delete($input);
//               }
//               if (($a_tasks[$task_id]['action'] == 'WAKEONLAN')
//                       AND ($ptc->is_active('TODO', 'wol')) //TODO
//                       AND ($a_agent['module_wakeonlan'] == '1')) {
//                  $single = 1;
//                  $ptc->addWakeonlan($pxml);
//                  $input['id'] = $task_id;
//                  $ptt->delete($input);
//               }
//            }
//
//            if ($single == "0") {
//               if ($a_agent['module_netdiscovery'] == '1') {
//                  $ptc->addDiscovery($pxml);
//               }
//               if ($a_agent['module_snmpquery'] == '1') {
//                  $ptc->addQuery($pxml);
//               }
//            }
//            if ($ocsinventory == '1') {
//               $ptc->addInventory();
//            }
////          $ptc->addWakeonlan();
//
         // ******** Send XML
            $ptc->setXML($ptc->getXML());
            echo $ptc->getSend();
         }
      } else {
         $res .= "0'".$errors."'";
      }
   }
}

?>
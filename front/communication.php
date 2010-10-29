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

session_start();
include (GLPI_ROOT."/inc/includes.php");

if (!isset($_SESSION['glpilanguage'])) {
   $_SESSION['glpilanguage'] = 'fr_FR';
}
error_reporting(E_ALL);
$_SESSION["glpi_use_mode"] = 2;

//// Load all plugin files
//   call_user_func("plugin_init_fusinvinventory");
//   $a_modules = PluginFusioninventoryModule::getAll();
//   foreach ($a_modules as $id => $datas) {
//      call_user_func("plugin_init_".$datas['directory']);
//   }

   

$PluginFusioninventoryCommunication  = new PluginFusioninventoryCommunication;
$pta  = new PluginFusioninventoryAgent;

$res='';
$errors='';

// ***** For debug only ***** //
//$GLOBALS["HTTP_RAW_POST_DATA"] = gzcompress('');
// ********** End ********** //

if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
   // Get conf tu know if SSL is only

   $fusioninventory_config = new PluginFusioninventoryConfig;
   $ssl = $fusioninventory_config->getValue(19, 'ssl_only');
   if (((isset($_SERVER["HTTPS"])) AND ($_SERVER["HTTPS"] == "on") AND ($ssl == "1"))
       OR ($ssl == "0")) {
      // echo "On continue";
   } else {
      $PluginFusioninventoryCommunication->setXML("<?xml version='1.0' encoding='ISO-8859-1'?>
<REPLY>
</REPLY>");
      $PluginFusioninventoryCommunication->noSSL();
      exit();
   }
   $ocsinventory = '0';
   file_put_contents(GLPI_PLUGIN_DOC_DIR."/fusioninventory/dial.log".rand(), gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]));
   $state = $pta->importToken(gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]));
   if ($state == '2') { // agent created
      $ocsinventory = '1';
   }
   $top0 = gettimeofday();
   if (!$PluginFusioninventoryCommunication->import(gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]))) {
      //if ($ac->connectionOK($errors)) {
      if (1) {
         $res .= "1'".$errors."'";

         $p_xml = gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]);
         $pxml = @simplexml_load_string($p_xml);

         if (isset($pxml->DEVICEID)) {

            $PluginFusioninventoryCommunication->setXML("<?xml version='1.0' encoding='UTF-8'?>
<REPLY>
</REPLY>");


            $PluginFusionInventoryConfig        = new PluginFusionInventoryConfig;
            $PluginFusioninventoryTaskjobstatus = new PluginFusioninventoryTaskjobstatus;

            $a_agent = $pta->InfosByKey($pxml->DEVICEID);

            $single = 0;

            // Get taskjob in waiting
            $PluginFusioninventoryCommunication->getTaskAgent($a_agent['id']);

            // ******** Send XML
            
            $PluginFusioninventoryCommunication->addInventory();
            $PluginFusioninventoryCommunication->addProlog();
            $PluginFusioninventoryCommunication->setXML($PluginFusioninventoryCommunication->getXML());

            echo $PluginFusioninventoryCommunication->getSend();
         }
      } else {
         $res .= "0'".$errors."'";
      }
   }
}

?>

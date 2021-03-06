<?php

/*
   ----------------------------------------------------------------------
   FusionInventory
   Copyright (C) 2010-2011 by the FusionInventory Development Team.

   http://www.fusioninventory.org/   http://forge.fusioninventory.org/
   ----------------------------------------------------------------------

   LICENSE

   This file is part of FusionInventory.

   FusionInventory is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 2 of the License, or
   any later version.

   FusionInventory is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with FusionInventory.  If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------
   Original Author of file: David Durieux
   Co-authors of file:
   Purpose of file:
   ----------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

include_once(GLPI_ROOT."/plugins/fusioninventory/inc/rulecollection.class.php");

class PluginFusioninventoryRuleImportEquipmentCollection extends PluginFusioninventoryRuleCollection {

   // From RuleCollection
   public $stop_on_first_match = true;
   public $right               = 'rule_ocs';
   public $menu_option         = 'linkcomputer';


   function getTitle() {
      global $LANG;

      return $LANG['plugin_fusioninventory']['rules'][2];
   }


   function prepareInputDataForProcess($input,$params) {
      return array_merge($input,$params);
   }


   function getRuleClassName() {
      if (preg_match('/(.*)Collection/',get_class($this),$rule_class)) {
         return $rule_class[1];
      }
      else {
         return "";
      }
   }
   /**
    * Get a instance of the class to manipulate rule of this collection
    *
   **/
   function getRuleClass() {
      $name = $this->getRuleClassName();
      if ($name !=  '') {
         return new $name ();
      }
      else {
         return null;
      }
   }


   
   function preProcessPreviewResults($output) {
      global $LANG;

      //If ticket is assign to an object, display this information first
      if (isset($output["action"])) {
         echo "<tr class='tab_bg_2'>";
         echo "<td>".$LANG['rulesengine'][11]."</td>";
         echo "<td>";

         switch ($output["action"]) {
            case PluginFusioninventoryRuleImportEquipment::LINK_RESULT_LINK :
               echo $LANG['setup'][620];
               break;

            case PluginFusioninventoryRuleImportEquipment::LINK_RESULT_NO_IMPORT:
               echo $LANG['ocsconfig'][11];
               break;

            case PluginFusioninventoryRuleImportEquipment::LINK_RESULT_IMPORT:
               echo $LANG['buttons'][37];
               break;
         }

         echo "</td>";
         echo "</tr>";
         if ($output["action"] != PluginFusioninventoryRuleImportEquipment::LINK_RESULT_NO_IMPORT
             && isset($output["found_equipment"])) {
            echo "<tr class='tab_bg_2'>";
            $className = $output["found_equipment"][1];
            $class = new $className;
            if ($class->getFromDB($output["found_equipment"][0])) {
               echo "<td>".$LANG['setup'][620]."</td>";
               echo "<td>".$class->getLink(true)."</td>";
            }
            echo "</tr>";
         }
      }
      return $output;
   }


}

?>
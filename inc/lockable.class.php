<?php

/*
 * @version $Id$
 ----------------------------------------------------------------------
 FusionInventory
 Coded by the FusionInventory Development Team.

 http://www.fusioninventory.org/   http://forge.fusioninventory.org//
 ----------------------------------------------------------------------

 LICENSE

 This file is part of FusionInventory plugins.

 FusionInventory is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 FusionInventory is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with FusionInventory; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: MAZZONI Vincent
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/// Plugin FusionInventory lock class
class PluginFusioninventoryLockable extends CommonDBTM{

   /**
    * Constructor
   **/
   function __construct () {
      $this->table="glpi_plugin_fusioninventory_lockables";
   }


   /**
    * Show lockables form.
    *
    *@param $p_target Target file.
    *TODO:  check rights and entity
    *
    *@return nothing (print the form)
    **/
   function showForm($p_options=array()) {
      global $LANG, $DB;

      if (!isset($this->fields['id'])) $this->fields['id']=0;

      $tableSelect='';
      if (isset($_SESSION["glpi_plugin_fusioninventory_lockable_table"])) {
         $tableSelect=$_SESSION["glpi_plugin_fusioninventory_lockable_table"];
      }

      echo "<form name='form' method='post' action='".$p_options['target']."'>";
      echo "<div class='center' id='tabsbody'>";
      echo "<table class='tab_cadre_fixe'>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_fusioninventory']['functionalities'][72]." :</td>";
      echo "<td>".$LANG['plugin_fusioninventory']['functionalities'][71]." :</td>";
      echo "<td></td><td>".$LANG['plugin_fusioninventory']['functionalities'][7]." :</td>";

      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>";

      $query = "SHOW TABLES;";
      $elements=array(0 => '-----');
      if ($result=$DB->query($query)) {
         while ($data=$DB->fetch_array($result)) {
            $elements[$data[0]]=$data[0];
         }
      }
      $idSelect = 'dropdown_tableSelect'.
                  Dropdown::showFromArray('tableSelect', $elements,
                                          array('value'=>$tableSelect));
      $elements=array();
      echo "</td><td class='right'>";

      echo "<span id='columnsSelect'>&nbsp;";
      if ($tableSelect!='') {
         PluginFusioninventoryLockable::getColumnSelect($tableSelect);
      }
      echo "</span>\n";

      $params = array('tableSelect' => '__VALUE__');
      ajaxUpdateItemOnSelectEvent($idSelect, 'columnsSelect', GLPI_ROOT."/plugins/fusioninventory/ajax/lockable.columns.php", $params);
      ajaxUpdateItemOnSelectEvent($idSelect, 'columnsLockable', GLPI_ROOT."/plugins/fusioninventory/ajax/lockable.lockables.php", $params);
      
      echo "</td><td class='center'>";
      if (PluginFusioninventoryProfile::haveRight("fusioninventory", "configuration", "w")) {
         echo "<input type='submit'  class=\"submit\" name='plugin_fusioninventory_lockable_add' value='" . $LANG['buttons'][8] . " >>'>";
         echo "<br /><br />";
         echo "<input type='submit'  class=\"submit\" name='plugin_fusioninventory_lockable_delete' value='<< " . $LANG['buttons'][6] . "'>";
      }
      echo "</td><td class='left'>";
      echo "<span id='columnsLockable'>&nbsp;";
      if ($tableSelect!='') {
         PluginFusioninventoryLockable::getLockableSelect($tableSelect);
      }
      echo "</span>\n";
      echo "</tr></table></div></form>";
   }

   /**
    * Get all about lockables fields
    *
    *@param $p_entities_id='' Entity id.
    *@param $p_table='' Table name.
    *TODO:  check rights
    *
    *@return result of the query
    **/
   static function getLockable($p_entities_id='', $p_table='') {
      global $DB;

      $query = "SELECT `id`, `tablename`, `tablefields`, `entities_id`, `is_recursive`
                FROM `glpi_plugin_fusioninventory_lockables`";
      $where = '';
      if ($p_entities_id != '') {
         $where = "`entities_id`='".$p_entities_id."'";
      }
      if ($p_table != '') {
         if ($where != '') $where.=' AND ';
         $where .= "`tablename`='".$p_table."'";
      }
      if ($where != '') $query.=' WHERE '.$where.';';
      $result = $DB->query($query);

      return $result;
   }

   /**
    * Get lockables fields
    *
    *@param $p_entities_id='' Entity id.
    *@param $p_table='' Table name.
    *TODO:  check rights
    *
    *@return array of lockable fields
    **/
   static function getLockableFields($p_entities_id='', $p_table='') {
      global $DB;

      if (TableExists('glpi_plugin_fusioninventory_lockables')) {
         $db_lockable = $DB->fetch_assoc(PluginFusioninventoryLockable::getLockable($p_entities_id, $p_table));
         $lockable_fields = $db_lockable["tablefields"];
         $lockable = importArrayFromDB($lockable_fields);

         return $lockable;
      }
   }

   /**
    * Set lockables fields
    *
    *@param $p_id Lockable id. If 0 creates a new lockable record, else update.
    *@param $p_table Table name.
    *@param $p_fields Array of fields to set to lockable (ex : "0=>name 1=>comment 2=>contact").
    *@param $p_entities_id Entity id.
    *@param $p_recursive Recursive lock (0/1).
    *TODO:  check rights
    *
    *@return nothing
    **/
   static function setLockable($p_id, $p_table, $p_fields, $p_entities_id, $p_recursive) {
      global $DB;

      if (empty($p_entities_id)) {
         $p_entities_id = 0;
      }
      if (empty($p_recursive)) {
         $p_recursive = 1;
      }

      if (!$p_id) {
         $insert = "INSERT INTO `glpi_plugin_fusioninventory_lockables` (
                     `tablename`, `tablefields`, `entities_id`, `is_recursive` )
                    VALUES ('$p_table','$p_fields','$p_entities_id','$p_recursive');";
         $DB->query($insert);
      } else {
         $update = "UPDATE `glpi_plugin_fusioninventory_lockables`
                    SET `tablename`='$p_table',
                        `tablefields`='$p_fields',
                        `entities_id`='$p_entities_id'
                    WHERE `id`='$p_id';";
         $DB->query($update);
      }
   }

   /**
    * Set lockables fields
    *
    *@param $p_post Array.
    *TODO:  check rights
    *
    *@return nothing
    **/
   static function setLockableForm($p_post) {
      global $DB;

      $tableSelect = $p_post["tableSelect"];
      $_SESSION["glpi_plugin_fusioninventory_lockable_table"] = $tableSelect;

      if ( (isset($p_post['plugin_fusioninventory_lockable_add']) AND isset($p_post['columnSelect'])) // add AND columns to add
            OR (isset($_POST['plugin_fusioninventory_lockable_delete']) AND isset($p_post['columnLockable'])) ) {  // delete AND columns to delete
         $db_lockable = $DB->fetch_assoc(PluginFusioninventoryLockable::getLockable('', $tableSelect));
         $lockable_id = $db_lockable["id"];
         $lockable_fields = $db_lockable["tablefields"];
         $lockable = importArrayFromDB($lockable_fields);

         if (isset($p_post['plugin_fusioninventory_lockable_add']) AND isset($p_post['columnSelect'])) { // add
            foreach ($p_post['columnSelect'] as $id_value) {
               array_push($lockable, $id_value);
            }
         }

         if (isset($_POST['plugin_fusioninventory_lockable_delete']) AND isset($p_post['columnLockable'])) { // delete
            foreach ($p_post['columnLockable'] as $id_value) {
               $fieldToDel = array_search($id_value, $lockable);
               if (isset($lockable[$fieldToDel])){
                  $fieldName = $lockable[$fieldToDel];
                  // TODO add a confirmation request before lockable deletion if locks are defined on this field
                  unset($lockable[$fieldToDel]);
                  // field is not lockable any more --> delete all locks on this field
                  PluginFusioninventoryLock::deleteInAllLockArray($tableSelect, $fieldName);
               }
            }
         }

         $lockable = array_values($lockable);
         PluginFusioninventoryLockable::setLockable($lockable_id, $tableSelect, exportArrayToDB($lockable), '', '');
      }
   }

   /**
    * Get multiple column select
    *
    *@param $p_table Table name.
    *TODO:  check rights
    *
    *@return nothing
    **/
   static function getColumnSelect($p_table) {
      global $DB;

      $query = "SHOW COLUMNS FROM `".$p_table."`";
      if ($result=$DB->query($query)) {
         if ($p_table != "") {
            $lockable_fields=PluginFusioninventoryLockable::getLockableFields('', $p_table);
            echo '<SELECT NAME="columnSelect[]" MULTIPLE SIZE="15">'."\n";
            while ($data=$DB->fetch_array($result)) {
               $column=$data[0];
                  if (!in_array($column,$lockable_fields)) { // do not display if the column name is already lockable
                     echo "<OPTION value='$column'>$column</OPTION>\n";
                  }
            }
            echo '</SELECT>';
         }
      }
   }

   /**
    * Get multiple lockable select
    *
    *@param $p_table Table name.
    *TODO:  check rights
    *
    *@return nothing
    **/
   static function getLockableSelect($p_table) {
      global $DB;

      if ($p_table != "") {
         $lockable_fields=PluginFusioninventoryLockable::getLockableFields('', $p_table);
         echo '<SELECT NAME="columnLockable[]" MULTIPLE SIZE="15">';
         if (count($lockable_fields)){
            foreach ($lockable_fields as $val){
               echo "<OPTION value='$val'>$val</OPTION>\n";
            }
         }
         echo '</SELECT>';
      }
   }
}

?>
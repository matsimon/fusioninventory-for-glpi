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

class PluginFusioninventoryErrors extends CommonDBTM {

	function __construct() {
		$this->table="glpi_plugin_fusioninventory_errors";
		$this->type='PluginFusioninventoryError';
		$this->entity_assign = true;
	}
	
	/* Useful function for : getIDandNewDescrFromDevice */
	function getIDandDescrFromDevice($itemtype, $value) {
		global $DB;
		
		if ($itemtype == COMPUTER_TYPE) {
			$field = 'ip';
      } else { // networking or printer
			$field = 'device_id';
      }
		$query = "SELECT `id`, `description` ".
               "FROM ".$this->table." ".
               "WHERE ".$field." = '".$value."'
                       AND `itemtype` = '".$itemtype."';";
		
		if (($result = $DB->query($query))) {
			if (($this->fields = $DB->fetch_assoc($result))) {
				return true;
         }
		}
		return false;
	}
	
	/**
	 * Function that finds if there is already an entry for a device in errors table.
	 * Returns new description error and id if an entry already exists, else : false.
	 * 
	 * $identifiant : ip for a computer, device id for the others
	 * $itemtype : type of the device
	 * $error_type : type of error : snmp, entries in GLPI DB, etc...
	 * $new_error : description of the new error
	 * 
	 * => Puts id and description into $this->fields
	 */
	function getIDandNewDescrFromDevice($itemtype, $identifiant, $error_type, $new_error) {
		global $LANG;

		if (!($this->getIDandDescrFromDevice($itemtype, $identifiant))) {
				return false;
      }

		// string to be checked if already exists into description
		if ($error_type == 'db') {
			$string = $LANG['plugin_fusioninventory']["errors"][10];
      }
		if ($error_type == 'snmp') {
			$string = $LANG['plugin_fusioninventory']["errors"][20];
      }
		if ($error_type == 'wire') {
			$string = $LANG['plugin_fusioninventory']["errors"][30];
      }
		$description = explode('. ', $this->fields['description']);
		$num = count($description);
		$i = 0;
		$find = false;
		
		while (($i<$num) && ($find == false)) {
			if (strstr($description["$i"], $string)) {
				$description["$i"] = " ".$new_error;
				$find = true;
			}
		$i++;
		}
		if ($find == false) {
			$description["$num"] = " ".$new_error;
      }
		$this->fields['description'] = implode('. ', $description);
		
		return true;
	}
	
	/* returns false if can't find computer (by IP, name or otherserial),
    * else returns the id of the computer */
	function writeComputerDbError($itemtype, $input) {
		global $LANG;
		global $DB;
		
		if (!($input['ip'] && $input['name'])) {
			return false;
      }
			
		// Trying to find id by IP
		$query = "SELECT `pc`.`id` AS `id`, `pc`.`name` AS `name`,
                       `pc`.`otherserial` AS `otherserial`, `pc`.`entities_id` AS `entities_id`
                FROM `glpi_computers` AS `pc`, `glpi_networkports` AS `port`
	   			 WHERE `port`.`itemtype` = ".$itemtype." ".
                      "AND `port`.`ip` = '".$input['ip']."' ".
                      "AND `port`.`items_id` = `pc`.`id`;";
		
		// else, find id by name
		$query2 = "SELECT `id`, `otherserial`, `entities_id` ".
				    "FROM `glpi_computers` AS `pc` ".
				    "WHERE `pc`.`name` = '".$input['name']."';";
		
		// else, find id by otherserial
		$query3 = "SELECT `id`, `entities_id` ".
				    "FROM `glpi_computers` AS `pc` ".
	   			 "WHERE `pc`.`otherserial` = '".$input['otherserial']."';";
	
		$fields = array();
		$input['description'] = $LANG['plugin_fusioninventory']["errors"][10]." : ";
		
		// if error = 0, no error
		$error = 2;
		
		/// Query 1 : if can find ip
		if (!($result = $DB->query($query))) {
			return false;
      } else if ($fields=$DB->fetch_assoc($result)) {
			if ($fields['name'] == $input['name']) {
				// we only keep what is false
				$input['name'] = 'ok';
				$error--;
			}
			if ($fields['otherserial'] == $input['otherserial']) {
				$input['otherserial'] = 'ok';
				$error--;
			}
			// if no error => end and returns the id of the device
			if ($error == 0) {
				return $fields['id'];
         } else {
				$input['description'] .= "IP : ok, ";
         }
      /// Query 2
		} else if (!($result = $DB->query($query2))) {
			return false;
      } else if ($fields=$DB->fetch_assoc($result)) {
			$input['name'] = 'ok';
			if ($fields['otherserial'] == $input['otherserial']) {
				$input['otherserial'] = 'ok';
         }
       /// Query 3
      } else if (!($result = $DB->query($query3))) {
			return false;
      } else if ($fields = $DB->fetch_assoc($result)) {
			$input['otherserial'] = 'ok';
      // can't find computer id
      } else {
			$input['description'] .= $LANG['plugin_fusioninventory']["errors"][11]." ,";
      }
		
		/// Get all inputs for DB
		$input['itemtype'] = $itemtype;

		if (isset($fields['id'])) {
			$input['device_id'] = $fields['id'];
      } else {
			$input['device_id'] = NULL;
      }
		if (isset($fields['entities_id'])) {
			$input['entities_id'] = $fields['entities_id'];
      } else {
			$input['entities_id'] = 0;
      }
		// if no description => unknown IP
		if (!isset($input['description'])) {
			$input['description'] = $LANG['plugin_fusioninventory']["errors"][12]." ,";
      }
		// add the other elements of the error messages
		$input['description'] .= "NetBIOS : ".$input['name'].", Admisys : ".$input['otherserial'];


		/// Check if this IP has already an entry in errors DB
		if ($this->getIDandNewDescrFromDevice($itemtype, $input['ip'], 'db',
                                            $input['description'])) {
			$input['id'] = $this->fields['id'];
			$input['description'] = $this->fields['description'];
			$this->update($input);
		} else {
			$input['first_pb_date'] = $input['last_pb_date'];
			$this->add($input);
		}
		
		if (isset($fields['id'])) {
			return $fields['id'];
      }
   	return false;
	}
	
	/* needs : ip, device_id */
	function writeSnmpError($itemtype, $input) {
		global $LANG;
		
		$input['itemtype'] = $itemtype;
		$input['entities_id'] = PluginFusioninventory::getDeviceFieldFromId($itemtype, $input['device_id'],
                                                                  "entities_id", false);

		$input['description'] = $LANG['plugin_fusioninventory']["errors"][20]." : ";
		$input['description'].= $LANG['plugin_fusioninventory']["errors"][21];
		
		// if there is already an error entry for the device
		if ($this->getIDandNewDescrFromDevice($itemtype, $input['device_id'], 'snmp',
                                            $input['description'])) {
			$input['id'] = $this->fields['id'];
			$input['description'] = $this->fields['description'];
			$this->update($input);	
		} else {
			$input['first_pb_date'] = $input['last_pb_date'];
			$this->add($input);
		}
	}
	
	/* needs : ip, device_id */
	function writeWireError($itemtype, $input) {
		global $LANG;
		
		$input['itemtype'] = $itemtype;
		$input['entities_id'] = PluginFusioninventory::getDeviceFieldFromId($itemtype, $input['device_id'],
                                                                  "entities_id", false);
		
		$input['description'] = $LANG['plugin_fusioninventory']["errors"][30];
		
		// if there is already an error entry for the device
		if ($this->getIDandNewDescrFromDevice($itemtype, $input['device_id'], 'wire',
                                            $input['description'])) {
			$input['id'] = $this->fields['id'];
			$input['description'] = $this->fields['description'];
			$this->update($input);	
		} else {
			$input['first_pb_date'] = $input['last_pb_date'];
			$this->add($input);
		}
	}
	
	/**
	 * Function which writes errors in DB
	 * 
	 * $input is an array
	 * $input has to contain :
	 * - ip for a computer for a wire control
	 * - ip, name and otherserial in case of db control for a computer 
	 * - device_id and ip for another device
	 */
	function writeError($itemtype, $error_type, $input, $date) {
		$input['last_pb_date'] = $date;

		if ($error_type == 'db') {
			return $this->writeComputerDbError($itemtype, $input);
      } else if ($error_type == 'snmp') {
			$this->writeSnmpError($itemtype, $input);
      } else if ( $error_type == 'wire' ) {
			$this->writeWireError($itemtype, $input);
      }
	}

	function countEntries($type, $id) {
		global $DB;
		
		$num = 0;
		$query = "SELECT count(DISTINCT `id`) ".
               "FROM ".$this->table." ";
		
		if ($type == COMPUTER_TYPE) {
			$query .="WHERE `itemtype` = '".COMPUTER_TYPE."' ";
      } else if ($type == NETWORKING_TYPE) {
			$query .="WHERE `itemtype` = '".NETWORKING_TYPE."' ";
      } else { // $type == PRINTER_TYPE
			$query .="WHERE `itemtype` = '".PRINTER_TYPE."' ";
      }
		$query .= "AND `device_id` = '".$id."';";
		
		if ($result_num=$DB->query($query)) {
			if ($field = $DB->result($result_num,0,0)) {
				$num += $field;
         }
		}
		return $num;
	}

	function getEntries($type, $id, $begin, $limit) {
		global $DB;
		
		$datas=array();
		$query = "SELECT *
                FROM ".$this->table." ";
		
		if ($type == COMPUTER_TYPE) {
			$query .= "WHERE `itemtype` = '".COMPUTER_TYPE."' ";
      } else if ($type == NETWORKING_TYPE) {
			$query .= "WHERE `itemtype` = '".NETWORKING_TYPE."' ";
      } else { // $type == PRINTER_TYPE
			$query .= "WHERE `itemtype` = '".PRINTER_TYPE."' ";
      }
		$query .= "AND `device_id` = '".$id."' ".
                "LIMIT ".$begin.", ".$limit.";";
		
		if ($result=$DB->query($query)) {
			$i = 0;
			while ($data=$DB->fetch_assoc($result)) {
				$data['first_pb_date'] = convDateTime($data['first_pb_date']);
				$data['last_pb_date'] = convDateTime($data['last_pb_date']);
				$datas["$i"] = $data;
				$i++;
			}
			return $datas;
		}
		return false;
	}
	
	function showForm($type, $target, $id) {
		global $LANG;
		
		if (!PluginFusioninventory::haveRight("errors","r")) {
			return false;
      }
		
		// preparing to display history
		if (!isset($_GET['start'])) {
			$_GET['start'] = 0;
      }
		
		$numrows = $this->countEntries($type, $id);
		$parameters = "id=".$_GET["id"]."&onglet=".$_SESSION["glpi_onglet"];	
		
		echo "<br>";
		printPager($_GET['start'], $numrows, $_SERVER['PHP_SELF'], $parameters);

		if ($_SESSION["glpilist_limit"] < $numrows) {
			$limit = $_SESSION["glpilist_limit"];
      } else {
			$limit = $numrows;
      }
		// Get history
		if (!($data = $this->getEntries($type, $id, $_GET['start'], $limit))) {
			return false;
      }

		// for $_GET['type'] (useful to check rights)
		if ($type == COMPUTER_TYPE) {
			echo "<div align='center'><form method='post' name='errors_form' id='errors_form'
                    action=\"".$target."?type=".COMPUTER_TYPE."\">";
      } else if ($type == NETWORKING_TYPE) {
			echo "<div align='center'><form method='post' name='errors_form' id='errors_form'
                    action=\"".$target."?type=".NETWORKING_TYPE."\">";
      } else { // $type == PRINTER_TYPE
			echo "<div align='center'><form method='post' name='errors_form' id='errors_form'
                    action=\"".$target."?type=".PRINTER_TYPE."\">";
      }
		echo "<table class='tab_cadre' cellpadding='5'><tr><th colspan='5'>";
		echo $LANG['plugin_fusioninventory']["errors"][0]." :</th></tr>";
		
		echo "<tr class='tab_bg_1'>";
		echo "<th></th>";
		echo "<th>".$LANG['plugin_fusioninventory']["errors"][1]." :</th>";
		echo "<th>".$LANG['plugin_fusioninventory']["errors"][2]." :</th>";
		echo "<th>".$LANG['plugin_fusioninventory']["errors"][3]." :</th>";
		echo "<th>".$LANG['plugin_fusioninventory']["errors"][4]." :</th></tr>";

		for ($i=0 ; $i<$limit ; $i++) {
			echo "<tr class='tab_bg_1'>";
			echo "<td align='center'>";
			echo "<input type='checkbox' name='checked_$i' value='1'>";
			echo "</td>";
			echo "<td align='center'>".$data["$i"]['ip']."</td>";
			echo "<td align='center'>".$data["$i"]['description']."</td>";
			echo "<td align='center'>".$data["$i"]['first_pb_date']."</td>";
			echo "<td align='center'>".$data["$i"]['last_pb_date']."</td>";
			echo "</td></tr>";
			echo "<input type='hidden' name='ID_$i' value='".$data["$i"]['id']."'>";
		}
		
		if (!PluginFusioninventory::haveRight("errors","w")) {
			return false;
      }
		echo "<input type='hidden' name='limit' value='".$limit."'>";
		echo "<tr class='tab_bg_1'><td colspan='5'>";
		echo "<div align='center'><a onclick= \"if ( markAllRows('errors_form') ) return false;\"
                 href='".$_SERVER['PHP_SELF']."?select=all'>".$LANG["buttons"][18]."</a>";
		echo " - <a onclick= \"if ( unMarkAllRows('errors_form') ) return false;\"
                  href='".$_SERVER['PHP_SELF']."?select=none'>".$LANG["buttons"][19]."</a> ";
		echo "<input type='submit' name='delete' value=\"".$LANG["buttons"][6]."\" class='submit' >
            </div></td></tr>";
		echo "</table></form></div>";
	}
}

?>
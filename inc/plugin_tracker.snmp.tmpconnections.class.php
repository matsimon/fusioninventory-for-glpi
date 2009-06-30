<?php
/*
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2008 by the INDEPNET Development Team.

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

// Original Author of file: David DURIEUX
// Purpose of file:
// ----------------------------------------------------------------------

class plugin_tracker_tmpconnections extends CommonDBTM
{

	function __construct()
	{
		$this->table = "glpi_plugin_tracker_tmp_netports";
		$this->type = PLUGIN_TRACKER_SNMP_TMP_NETPORTS;
	}



	function UpdatePort($FK_networking,$FK_networking_port,$cdp)
	{
		global $DB;

		$query = "SELECT * FROM glpi_plugin_tracker_tmp_netports ".
			" WHERE FK_networking='".$FK_networking."' ".
				" AND FK_networking_port=".$FK_networking_port." ";
		$result = $DB->query($query);
		if ( $DB->numrows($result) == 0 )
		{
			$datas["FK_networking"] = $FK_networking;
			$datas["FK_networking_port"] = $FK_networking_port;
			$datas["cdp"] = $cdp;
			$TMP_ID = $this->add($datas);
			return $TMP_ID;
		}
		return '';
	}



	function AddConnections($FK_tmp_netports,$ArrayMacAddress)
	{
		global $DB;

		foreach($ArrayMacAddress as $num=>$MacAddress)
		{
			$query_insert = "INSERT INTO glpi_plugin_tracker_tmp_connections ".
				" (FK_tmp_netports, macaddress) ".
				" VALKUES ('".$FK_tmp_netports."', '".$MacAddress."') ";
			$DB->query(query_insert);
		}

	}



	function WireInterSwitchs()
	{
		global $DB;
		// ** port in glpi_plugin_tracker_tmp_netports is deleted = port connected ** //

		// Select all cdp = 1 & their mac adress
		$query = "SELECT ifmac, glpi_plugin_tracker_tmp_netports.ID as IDnetports FROM glpi_plugin_tracker_tmp_netports ".
			" LEFT JOIN glpi_networking_ports ON FK_networking=on_device AND FK_networking_port=device_type ".
			" WHERE cdp='1' ";
		$result=$DB->query($query);
		while ($data=$DB->fetch_array($result))
		{
			$query_sel2 = " SELECT * FROM glpi_plugin_tracker_tmp_connections ".
				" WHERE macaddress='".$data['ifmac']."' ";
			$result_sel2=$DB->query($query_sel2);
			while ($data_sel2=$DB->fetch_array($result_sel2))
			{
				$query_delete = "DELETE FROM glpi_plugin_tracker_tmp_connections ".
					" WHERE ID='".$data_sel2["ID"]."' ";
				$DB->query($query_delete);
			}
			//delete after port with cdp = 1
			$query_delete = "DELETE FROM glpi_plugin_tracker_tmp_netports ".
				" WHERE ID='".$data_["IDnetports"]."' ";
			$DB->query($query_delete);
		}
		// Get ports which have only one connection and connect between ports(swicths)
		$i = 1;
		while ($i != 0)
		{
			$i = 0;
			$query = "SELECT ifmac, glpi_plugin_tracker_tmp_netports.ID as IDnetports, FK_networking, FK_networking_port ".
					" FROM glpi_plugin_tracker_tmp_netports ".
				" LEFT JOIN glpi_networking_ports ON FK_networking=on_device AND FK_networking_port=device_type ".
				" WHERE cdp='1' ".
					" AND COUNT(ifmac) = 1";
			$result=$DB->query($query);
			while ($data=$DB->fetch_array($result))
			{
				$i++;
				

			}
		}


	}

}


?>

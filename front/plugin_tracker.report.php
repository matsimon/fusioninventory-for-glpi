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

if (!defined('GLPI_ROOT')) {
	define('GLPI_ROOT', '../../..');
}

$NEEDED_ITEMS=array("tracker","search");
include (GLPI_ROOT."/inc/includes.php");

commonHeader($LANGTRACKER["title"][0],$_SERVER["PHP_SELF"],"plugins","tracker");

plugin_tracker_checkRight("snmp_report","r");

plugin_tracker_mini_menu();

echo "<table class='tab_cadre'>";

echo "<th align='center'>".$LANG["Menu"][6]."</th>";

echo "<tr class='tab_bg_1'>";
echo "<td align='center'>";
echo "<a href='".GLPI_ROOT."/plugins/tracker/report/plugin_tracker.unknown_mac.php'>Adresses MAC inconnues</a>";
echo "</td>";
echo "</tr>";

echo "<tr class='tab_bg_1'>";
echo "<td align='center'>";
echo "<a href='".GLPI_ROOT."/plugins/tracker/report/plugin_tracker.switch_ports.history.php'>Historique des ports de switchs</a>";
echo "</td>";
echo "</tr>";

echo "<tr class='tab_bg_1'>";
echo "<td align='center'>";
echo "<a href='".GLPI_ROOT."/plugins/tracker/report/plugin_tracker.ports_date_connections.php'>Ports de switchs non connectés depuis xx mois</a>";
echo "</td>";
echo "</tr>";
/*
echo "<tr class='tab_bg_1'>";
echo "<td align='center'>";
echo "Liste des équipements prêts à être interrogés mais non associés à un agent";
echo "</td>";
*/
echo "</table>";

commonFooter();

?>
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

class plugin_tracker_printers_cartridges extends CommonDBTM {
	function __construct() {
		$this->table = "glpi_plugin_tracker_printers_cartridges";
		//$this->type = PLUGIN_TRACKER_PRINTERS_CARTRIDGES;
	}
}

class plugin_tracker_printers extends CommonDBTM {
	function __construct() {
		$this->table = "glpi_plugin_tracker_printers";
		//$this->type = PLUGIN_TRACKER_PRINTERS_CARTRIDGES;
	}



	function showFormPrinter($target,$ID)
	{
		global $DB,$CFG_GLPI,$LANG,$LANGTRACKER,$TRACKER_MAPPING;	
	
		$this->ID = $ID;
		
		$query = "
		SELECT * 
		FROM glpi_plugin_tracker_printers
		WHERE FK_printers=".$ID." ";

		$result = $DB->query($query);		
		$data = $DB->fetch_assoc($result);
		
		// Add in database if not exist
		if ($DB->numrows($result) == "0")
		{
			$query_add = "INSERT INTO glpi_plugin_tracker_printers
			(FK_printers) VALUES('".$ID."') ";
			
			$DB->query($query_add);
		}
		
		// Form printer informations
		echo "<br>";
		echo "<div align='center'><form method='post' name='snmp_form' id='snmp_form'  action=\"".$target."\">";

		echo "<table class='tab_cadre' cellpadding='5' width='800'>";
		
		echo "<tr class='tab_bg_1'>";
		echo "<th colspan='3'>";
		echo $LANGTRACKER["snmp"][11];
		echo "</th>";
		echo "</tr>";
		
		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>".$LANGTRACKER["model_info"][4]."</td>";
		echo "<td align='center'>";
		dropdownValue("glpi_plugin_tracker_model_infos","FK_model_infos",$data["FK_model_infos"],0);
		echo "</td>";
		echo "</tr>";
	
		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>".$LANGTRACKER["functionalities"][43]."</td>";
		echo "<td align='center'>";
		plugin_tracker_snmp_auth_dropdown($data["FK_snmp_connection"]);
		echo "</td>";
		echo "</tr>";
		
		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>".$LANGTRACKER["functionalities"][24]."</td>";
		echo "<td align='center'>";
		dropdownInteger("frequence_days",$data["frequence_days"], 1,100);
		echo "&nbsp;&nbsp;".$LANG["stats"][31];
		echo "</td>";
		echo "</tr>";		
		
		echo "<tr class='tab_bg_1'>";
		echo "<td colspan='2'>";
		echo "<div align='center'>";
		echo "<input type='hidden' name='ID' value='".$ID."'>";
		echo "<input type='submit' name='update' value=\"".$LANG["buttons"][7]."\" class='submit' >";
		echo "</td>";
		echo "</tr>";

		echo "</table></form>";

		// ** FORM FOR CARTRIDGES

		echo "<br/><div align='center'><form method='post' name='snmp_form' id='snmp_form'  action=\"".$target."\">";

		echo "<table class='tab_cadre' cellpadding='5' width='800'>";		

		echo "<tr class='tab_bg_1'>";
		echo "<th align='center' colspan='2'>";
		echo $LANG["cartridges"][16];
		echo "</th>";
		echo "</tr>";

		$query_cartridges = "
		SELECT * 
		FROM glpi_plugin_tracker_printers_cartridges
		WHERE FK_printers=".$ID." ";
		if ( $result_cartridges=$DB->query($query_cartridges) )
		{
			while ( $data_cartridges=$DB->fetch_array($result_cartridges) )
			{
				echo "<tr class='tab_bg_1'>";
				echo "<td align='center'>";
				echo $TRACKER_MAPPING[PRINTER_TYPE][$data_cartridges['object_name']]['shortname'];
				echo " : ";
				dropdownValue("glpi_cartridges_type","FK_cartridges",$data_cartridges['FK_cartridges'],0);
				echo "</td>";
				echo "<td align='center'>";
				plugin_tracker_Bar($data_cartridges['state']); 
				echo "</td>";
				echo "</tr>";
			}
		}
				
		echo "<tr class='tab_bg_1'>";
		echo "<td align='center'>";
		echo "<select name='object_name'>";
		foreach ($TRACKER_MAPPING[PRINTER_TYPE] AS $cartridges=>$value)
		{
			if (ereg("cartridges", $cartridges))
			{
				echo "<option value='".$cartridges."'>".$TRACKER_MAPPING[PRINTER_TYPE][$cartridges]['name']."</option>";
			}
		}
		echo "</select>";
		echo "</td>";
		echo "<td align='center'>";
		dropdownCompatibleCartridges($ID);
		echo "</td>";
		echo "</tr>";	
			
		echo "<tr class='tab_bg_1'>";
		echo "<td colspan='2'>";
		echo "<div align='center'>";
		echo "<input type='hidden' name='ID' value='".$ID."'>";
		echo "<input type='hidden' name='state' value='100'>";
		echo "<input type='submit' name='add' value=\"".$LANG["buttons"][8]."\" class='submit' >";
		echo "</td>";
		echo "</tr>";

		echo "</table></form>";
		
	
	}



	function showFormPrinter_pagescounter($target,$ID)
	{
		global $DB,$CFG_GLPI,$LANG,$LANGTRACKER,$TRACKER_MAPPING;	
	
		$plugin_tracker_printers = new plugin_tracker_printers;
	
		$this->ID = $ID;
		
		// Form pages counter
		echo "<br>";
		echo "<div align='center'><form method='post' name='snmp_form' id='snmp_form'  action=\"".$target."\">";

		echo "<table class='tab_cadre' cellpadding='5' width='950'>";
		
		echo "<tr class='tab_bg_1'>";
		echo "<th colspan='3'>";
		echo $LANG["cartridges"][39];
		echo "</th>";
		echo "</tr>";

		// Total page counter
		echo "<tr class='tab_bg_1'>";
		echo "<th colspan='3'>";
		echo $LANGTRACKER["mapping"][128];
		echo "</th>";
		echo "</tr>";
		
		$query = "
		SELECT * 
		FROM glpi_plugin_tracker_printers_history
		WHERE FK_printers=".$ID." 
		ORDER BY date DESC
		LIMIT 0,7";
		$dates = array();
		$total_page_counter = array();
		if ( $result=$DB->query($query) )
		{
			while ( $data=$DB->fetch_array($result) )
			{
				$dates[] = $data['date'];
				$total_page_counter[] = $data['pages_total'];
			}
		}
		
		echo "<tr class='tab_bg_1'>";
		echo "<td colspan='3'>";
			echo "<table class='tab_cadre' cellpadding='5' width='900'>";

			$plugin_tracker_printers->counter_page_arrayLine_display($LANG["common"][27],$dates,1);
			
			$plugin_tracker_printers->counter_page_arrayLine_display($LANG["printers"][31],$total_page_counter);		
			
			echo "<tr class='tab_bg_1'>";
			echo "<th>Ecart</th>";
			$i = 1;
			$ecart = array();
			foreach ($total_page_counter AS $value)
			{
				if ($i >= count($total_page_counter))
				{
					echo "<td align='center'></td>";
				}
				else
				{
					echo "<td align='center'>".($value - $total_page_counter[$i])."</td>";
					$ecart[$dates[$i-1]] = ($value - $total_page_counter[$i]);
				}
				$i++;
			}
			echo "</tr>";
			
			echo "</table>";

		$plugin_tracker_printers->graphBy($ecart,$LANGTRACKER["mapping"][128],$LANGTRACKER["printer"][0],1,"day");
		
		echo "</td>";
		echo "</tr>";
		
		// Black & white page counter
		echo "<tr class='tab_bg_1'>";
		echo "<th colspan='3'>";
		echo $LANGTRACKER["mapping"][129];
		echo "</th>";
		echo "</tr>";

		// Color page counter
		echo "<tr class='tab_bg_1'>";
		echo "<th colspan='3'>";
		echo $LANGTRACKER["mapping"][130];
		echo "</th>";
		echo "</tr>";

		// Recto/Verso page counter
		echo "<tr class='tab_bg_1'>";
		echo "<th colspan='3'>";
		echo $LANGTRACKER["mapping"][154];
		echo "</th>";
		echo "</tr>";

		// Scanned page counter
		echo "<tr class='tab_bg_1'>";
		echo "<th colspan='3'>";
		echo $LANGTRACKER["mapping"][155];
		echo "</th>";
		echo "</tr>";
							
		echo "</table>";
		
	}
			
		
		
	function update_printers_infos($ID, $FK_model_infos, $FK_snmp_connection)
	{
		global $DB;
		
		$query = "UPDATE glpi_plugin_tracker_printers
		SET FK_model_infos='".$FK_model_infos."',FK_snmp_connection='".$FK_snmp_connection."'
		WHERE FK_printers='".$ID."' ";
	
		$DB->query($query);
	}	
	
	
	
	function counter_page_arrayLine_display($title,$array,$th=0)
	{
		$td = "td";
		if ($th == "1")
			$td = "th";
		echo "<tr class='tab_bg_1'>";
		echo "<th>".$title."</th>";
		foreach ($array AS $value)
		{
			$explode = explode(" ", $value);
			echo "<".$td." align='center'>".$explode[0]."</".$td.">";
		}
		echo "</tr>";
	
	}
	
	
	
	/** Get groups assigned to tickets between 2 dates
	* BASED ON SPIP DISPLAY GRAPH : www.spip.net
	* @param $type string : "month" or "year" or "day" or "week"
	* @param $entrees array : array containing data to displayed
	* @param $titre string : title 
	* @param $unit string : unit 
	* @param $showtotal boolean : also show total values ?
	* @return array contains the distinct groups assigned to a tickets
	*/
	function graphBy($entrees,$titre="",$unit="",$showtotal=1,$type="month"){
	
		global $DB,$CFG_GLPI,$LANG;
		ksort($entrees);
		$total="";
		if ($showtotal==1) $total=array_sum($entrees);
	
		echo "<p align='center'>";
		echo "<font face='verdana,arial,helvetica,sans-serif' size='2'><strong>$titre - $total $unit</strong></font>";
	
		echo "<div class='center'>";
	
		if (count($entrees)>0){
	
			$max = max($entrees);
			$maxgraph = substr(ceil(substr($max,0,2) / 10)."000000000000", 0, strlen($max));
	
			if ($maxgraph < 10) $maxgraph = 10;
			if (1.1 * $maxgraph < $max) $maxgraph.="0";	
			if (0.8*$maxgraph > $max) $maxgraph = 0.8 * $maxgraph;
			$rapport = 200 / $maxgraph;
	
			$largeur = floor(420 / (count($entrees)));
			if ($largeur < 1) $largeur = 1;
			if ($largeur > 50) $largeur = 50;
		}
	
		echo "<table cellpadding='0' cellspacing='0' border='0' ><tr><td style='background-image:url(".$CFG_GLPI["root_doc"]."/pics/fond-stats.gif)' >";
		echo "<table cellpadding='0' cellspacing='0' border='0'><tr>";
		echo "<td bgcolor='black'><img src='".$CFG_GLPI["root_doc"]."/pics/noir.png' width='1' height='200' alt=''></td>";
	
		// Presentation graphique
		$n = 0;
		$decal = 0;
		$tab_moyenne = "";
		$total_loc=0;
		while (list($key, $value) = each($entrees)) {
			$n++;
	
			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
	
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
	
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
			$moyenne = $moyenne / count($tab_moyenne);
	
			$hauteur_moyenne = round($moyenne * $rapport) ;
			$hauteur = round($value * $rapport)	;
			echo "<td valign='bottom' width=".$largeur.">";
	
			if ($hauteur >= 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/moyenne.png' width=".$largeur." height='1' >";
					echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/rien.gif' width=".$largeur." height=".$difference." >";
					echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/noir.png' width=".$largeur." height='1' >";
					if (ereg("-01",$key)){ // janvier en couleur foncee
						echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/fondgraph1.png' width=".$largeur." height=".$hauteur." >";
					} 
					else {
						echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/fondgraph2.png' width=".$largeur." height=".$hauteur." >";
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/noir.png' width=".$largeur." height='1'>";
					if (ereg("-01",$key)){ // janvier en couleur foncee
						$couleur =  "1";
						$couleur2 =  "2";
					} 
					else {
						$couleur = "2";
						$couleur2 = "1";
					}
					echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/fondgraph$couleur.png' width=".$largeur." height=".$difference.">";
					echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/moyenne.png' width=".$largeur." height='1'>";
					echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/fondgraph$couleur.png' width=".$largeur." height=".$hauteur_moyenne.">";
				}
				else {
					echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/noir.png' width=".$largeur." height='1'>";
					if (ereg("-01",$key)){ // janvier en couleur foncee
						echo "<img alt=\"$key: $val_tab\" title=\"$key: $value\" src='".$CFG_GLPI["root_doc"]."/pics/fondgraph1.png' width=".$largeur." height=".$hauteur.">";
					} 
					else {
						echo "<img alt=\"$key: $value\" title=\"$key: $value\"  src='".$CFG_GLPI["root_doc"]."/pics/fondgraph2.png' width=".$largeur." height=".$hauteur.">";
					}
				}
			}
	
			echo "<img alt=\"$value\" title=\"$value\"  src='".$CFG_GLPI["root_doc"]."/pics/rien.gif' width=".$largeur." height='1'>";
			echo "</td>\n";
	
		}
		echo "<td bgcolor='black'><img src='".$CFG_GLPI["root_doc"]."/pics/noir.png' width='1' height='1' alt=''></td>";
		echo "</tr>";
		if ($largeur>10){
			echo "<tr><td></td>";
			foreach ($entrees as $key => $val){
				if ($type=="month"){
					$splitter=split("-",$key);
					echo "<td class='center'>".utf8_substr($LANG["calendarM"][$splitter[1]-1],0,3)."</td>";
				} else if ($type=="year"){
					echo "<td class='center'>".substr($key,2,2)."</td>";
				}else if ($type=="day"){
					echo "<td class='center'>".substr($key,8,2)."</td>";
				}else if ($type=="week"){
				$val = explode(" ",$key);
       		$date = explode("-",$val[0]);
       		$time = explode(":",$val[1]);
					echo "<td class='center'>".date('W',mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]))."</td>";
				}
			}
			echo "</tr>";
		}
	
		if ($maxgraph<=10) $r=2;
		else if ($maxgraph<=100) $r=1;
		else $r=0;
		echo "</table>";
		echo "</td>";
		echo "<td style='background-image:url(".$CFG_GLPI["root_doc"]."/pics/fond-stats.gif)' valign='bottom'><img src='".$CFG_GLPI["root_doc"]."/pics/rien.gif' style='background-color:black;' width='3' height='1' alt=''></td>";
		echo "<td><img src='".$CFG_GLPI["root_doc"]."/pics/rien.gif' width='5' height='1' alt=''></td>";
		echo "<td valign='top'>";
		echo "<table cellpadding='0' cellspacing='0' border='0'>";
		echo "<tr><td height='15' valign='top'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1'><strong>".formatNumber($maxgraph,false,$r)."</strong></font>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1' color='#999999'>".formatNumber(7*($maxgraph/8),false,$r)."</font>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1'>".formatNumber(3*($maxgraph/4),false,$r)."</font>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1' color='#999999'>".formatNumber(5*($maxgraph/8),false,$r)."</font>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1'><strong>".formatNumber($maxgraph/2,false,$r)."</strong></font>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1' color='#999999'>".formatNumber(3*($maxgraph/8),false,$r)."</font>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1'>".formatNumber($maxgraph/4,false,$r)."</font>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1' color='#999999'>".formatNumber(1*($maxgraph/8),false,$r)."</font>";
		echo "</td></tr>";
		echo "<tr><td height='10' valign='bottom'>";		
		echo "<font face='arial,helvetica,sans-serif' size='1'><strong>0</strong></font>";
		echo "</td>";
	
		echo "</tr></table>";
		echo "</td></tr></table>";
		echo "</div>";
	}


}
?>
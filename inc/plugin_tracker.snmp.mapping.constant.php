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

if (!defined('GLPI_ROOT')){
	die("Sorry. You can't access directly to this file");
}

global $LANG,$LANGTRACKER,$TRACKER_MAPPING;

// ----------------------------------------------------------------------
//NETWORK MAPPING MAPPING
// ----------------------------------------------------------------------
$TRACKER_MAPPING[NETWORKING_TYPE]['location']['table'] = 'glpi_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['location']['field'] = 'location';
$TRACKER_MAPPING[NETWORKING_TYPE]['location']['name'] = $LANGTRACKER["mapping"][1];
$TRACKER_MAPPING[NETWORKING_TYPE]['location']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['location']['dropdown'] = 'glpi_dropdown_locations';

$TRACKER_MAPPING[NETWORKING_TYPE]['firmware']['table'] = 'glpi_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['firmware']['field'] = 'firmware';
$TRACKER_MAPPING[NETWORKING_TYPE]['firmware']['name'] = $LANGTRACKER["mapping"][2];
$TRACKER_MAPPING[NETWORKING_TYPE]['firmware']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['firmware']['dropdown'] = 'glpi_dropdown_firmware';

$TRACKER_MAPPING[NETWORKING_TYPE]['uptime']['table'] = 'glpi_plugin_tracker_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['uptime']['field'] = 'uptime';
$TRACKER_MAPPING[NETWORKING_TYPE]['uptime']['name'] = $LANGTRACKER["mapping"][3];
$TRACKER_MAPPING[NETWORKING_TYPE]['uptime']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['uptime']['dropdown'] = '';


$TRACKER_MAPPING[NETWORKING_TYPE]['cpu']['table'] = 'glpi_plugin_tracker_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['cpu']['field'] = 'cpu';
$TRACKER_MAPPING[NETWORKING_TYPE]['cpu']['name'] = $LANGTRACKER["mapping"][12];
$TRACKER_MAPPING[NETWORKING_TYPE]['cpu']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['cpu']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['serial']['table'] = 'glpi_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['serial']['field'] = 'serial';
$TRACKER_MAPPING[NETWORKING_TYPE]['serial']['name'] = $LANGTRACKER["mapping"][13];
$TRACKER_MAPPING[NETWORKING_TYPE]['serial']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['serial']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['name']['table'] = 'glpi_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['name']['field'] = 'name';
$TRACKER_MAPPING[NETWORKING_TYPE]['name']['name'] = $LANGTRACKER["mapping"][20];
$TRACKER_MAPPING[NETWORKING_TYPE]['name']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['name']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ram']['table'] = 'glpi_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['ram']['field'] = 'ram';
$TRACKER_MAPPING[NETWORKING_TYPE]['ram']['name'] = $LANGTRACKER["mapping"][21];
$TRACKER_MAPPING[NETWORKING_TYPE]['ram']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ram']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['memory']['table'] = 'glpi_plugin_tracker_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['memory']['field'] = 'memory';
$TRACKER_MAPPING[NETWORKING_TYPE]['memory']['name'] = $LANGTRACKER["mapping"][22];
$TRACKER_MAPPING[NETWORKING_TYPE]['memory']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['memory']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['vtpVlanName']['table'] = '';
$TRACKER_MAPPING[NETWORKING_TYPE]['vtpVlanName']['field'] = '';
$TRACKER_MAPPING[NETWORKING_TYPE]['vtpVlanName']['name'] = $LANGTRACKER["mapping"][19];
$TRACKER_MAPPING[NETWORKING_TYPE]['vtpVlanName']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['vtpVlanName']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['entPhysicalModelName']['table'] = 'glpi_networking';
$TRACKER_MAPPING[NETWORKING_TYPE]['entPhysicalModelName']['field'] = 'model';
$TRACKER_MAPPING[NETWORKING_TYPE]['entPhysicalModelName']['name'] = $LANGTRACKER["mapping"][17];
$TRACKER_MAPPING[NETWORKING_TYPE]['entPhysicalModelName']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['entPhysicalModelName']['dropdown'] = 'glpi_dropdown_model_networking';






// Networking Ports

$TRACKER_MAPPING[NETWORKING_TYPE]['ifmtu']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifmtu']['field'] = 'ifmtu';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifmtu']['name'] = $LANGTRACKER["mapping"][4];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifmtu']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifmtu']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifspeed']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifspeed']['field'] = 'ifspeed';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifspeed']['name'] = $LANGTRACKER["mapping"][5];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifspeed']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifspeed']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifinternalstatus']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinternalstatus']['field'] = 'ifinternalstatus';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinternalstatus']['name'] = $LANGTRACKER["mapping"][6];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinternalstatus']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinternalstatus']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['iflastchange']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['iflastchange']['field'] = 'iflastchange';
$TRACKER_MAPPING[NETWORKING_TYPE]['iflastchange']['name'] = $LANGTRACKER["mapping"][7];
$TRACKER_MAPPING[NETWORKING_TYPE]['iflastchange']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['iflastchange']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifinoctets']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinoctets']['field'] = 'ifinoctets';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinoctets']['name'] = $LANGTRACKER["mapping"][8];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinoctets']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinoctets']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifoutoctets']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifoutoctets']['field'] = 'ifoutoctets';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifoutoctets']['name'] = $LANGTRACKER["mapping"][9];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifoutoctets']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifoutoctets']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifinerrors']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinerrors']['field'] = 'ifinerrors';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinerrors']['name'] = $LANGTRACKER["mapping"][10];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinerrors']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifinerrors']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifouterrors']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifouterrors']['field'] = 'ifouterrors';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifouterrors']['name'] = $LANGTRACKER["mapping"][11];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifouterrors']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifouterrors']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifstatus']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifstatus']['field'] = 'ifstatus';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifstatus']['name'] = $LANGTRACKER["mapping"][14];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifstatus']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifstatus']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifPhysAddress']['table'] = 'glpi_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifPhysAddress']['field'] = 'ifmac';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifPhysAddress']['name'] = $LANGTRACKER["mapping"][15];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifPhysAddress']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifPhysAddress']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifName']['table'] = 'glpi_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifName']['field'] = 'name';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifName']['name'] = $LANGTRACKER["mapping"][16];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifName']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifName']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifType']['table'] = '';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifType']['field'] = '';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifType']['name'] = $LANGTRACKER["mapping"][18];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifType']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifType']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['ifdescr']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifdescr']['field'] = 'ifdescr';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifdescr']['name'] = $LANGTRACKER["mapping"][23];
$TRACKER_MAPPING[NETWORKING_TYPE]['ifdescr']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['ifdescr']['dropdown'] = '';

$TRACKER_MAPPING[NETWORKING_TYPE]['portDuplex']['table'] = 'glpi_plugin_tracker_networking_ports';
$TRACKER_MAPPING[NETWORKING_TYPE]['portDuplex']['field'] = 'portduplex';
$TRACKER_MAPPING[NETWORKING_TYPE]['portDuplex']['name'] = $LANGTRACKER["mapping"][33];
$TRACKER_MAPPING[NETWORKING_TYPE]['portDuplex']['type'] = 'text';
$TRACKER_MAPPING[NETWORKING_TYPE]['portDuplex']['dropdown'] = '';




$TRACKER_MAPPING[PRINTER_TYPE]['model']['table'] = 'glpi_printers';
$TRACKER_MAPPING[PRINTER_TYPE]['model']['field'] = 'model';
$TRACKER_MAPPING[PRINTER_TYPE]['model']['name'] = $LANGTRACKER["mapping"][25];
$TRACKER_MAPPING[PRINTER_TYPE]['model']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['model']['dropdown'] = 'glpi_dropdown_model_printers';

$TRACKER_MAPPING[PRINTER_TYPE]['serial']['table'] = 'glpi_printers';
$TRACKER_MAPPING[PRINTER_TYPE]['serial']['field'] = 'serial';
$TRACKER_MAPPING[PRINTER_TYPE]['serial']['name'] = $LANGTRACKER["mapping"][27];
$TRACKER_MAPPING[PRINTER_TYPE]['serial']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['serial']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['name']['table'] = 'glpi_printers';
$TRACKER_MAPPING[PRINTER_TYPE]['name']['field'] = 'name';
$TRACKER_MAPPING[PRINTER_TYPE]['name']['name'] = $LANGTRACKER["mapping"][24];
$TRACKER_MAPPING[PRINTER_TYPE]['name']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['name']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['memory']['table'] = 'glpi_printers';
$TRACKER_MAPPING[PRINTER_TYPE]['memory']['field'] = 'ramSize';
$TRACKER_MAPPING[PRINTER_TYPE]['memory']['name'] = $LANGTRACKER["mapping"][26];
$TRACKER_MAPPING[PRINTER_TYPE]['memory']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['memory']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblack']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblack']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblack']['name'] = $LANGTRACKER["mapping"][34];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblack']['shortname'] = $LANGTRACKER["mapping"][134];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblack']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblack']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblackphoto']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblackphoto']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblackphoto']['name'] = $LANGTRACKER["mapping"][35];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblackphoto']['shortname'] = $LANGTRACKER["mapping"][135];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblackphoto']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesblackphoto']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyan']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyan']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyan']['name'] = $LANGTRACKER["mapping"][36];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyan']['shortname'] = $LANGTRACKER["mapping"][136];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyan']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyan']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesyellow']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesyellow']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesyellow']['name'] = $LANGTRACKER["mapping"][37];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesyellow']['shortname'] = $LANGTRACKER["mapping"][137];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesyellow']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesyellow']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagenta']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagenta']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagenta']['name'] = $LANGTRACKER["mapping"][38];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagenta']['shortname'] = $LANGTRACKER["mapping"][138];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagenta']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagenta']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyanlight']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyanlight']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyanlight']['name'] = $LANGTRACKER["mapping"][39];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyanlight']['shortname'] = $LANGTRACKER["mapping"][139];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyanlight']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgescyanlight']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagentalight']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagentalight']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagentalight']['name'] = $LANGTRACKER["mapping"][40];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagentalight']['shortname'] = $LANGTRACKER["mapping"][140];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagentalight']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesmagentalight']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductor']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductor']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductor']['name'] = $LANGTRACKER["mapping"][41];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductor']['shortname'] = $LANGTRACKER["mapping"][141];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductor']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductor']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorblack']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorblack']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorblack']['name'] = $LANGTRACKER["mapping"][42];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorblack']['shortname'] = $LANGTRACKER["mapping"][142];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorblack']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorblack']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcolor']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcolor']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcolor']['name'] = $LANGTRACKER["mapping"][43];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcolor']['shortname'] = $LANGTRACKER["mapping"][143];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcolor']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcolor']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcyan']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcyan']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcyan']['name'] = $LANGTRACKER["mapping"][44];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcyan']['shortname'] = $LANGTRACKER["mapping"][144];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcyan']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductorcyan']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductoryellow']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductoryellow']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductoryellow']['name'] = $LANGTRACKER["mapping"][45];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductoryellow']['shortname'] = $LANGTRACKER["mapping"][145];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductoryellow']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductoryellow']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductormagenta']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductormagenta']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductormagenta']['name'] = $LANGTRACKER["mapping"][46];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductormagenta']['shortname'] = $LANGTRACKER["mapping"][146];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductormagenta']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesphotoconductormagenta']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertblack']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertblack']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertblack']['name'] = $LANGTRACKER["mapping"][47];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertblack']['shortname'] = $LANGTRACKER["mapping"][147];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertblack']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertblack']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertcyan']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertcyan']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertcyan']['name'] = $LANGTRACKER["mapping"][48];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertcyan']['shortname'] = $LANGTRACKER["mapping"][148];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertcyan']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertcyan']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertyellow']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertyellow']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertyellow']['name'] = $LANGTRACKER["mapping"][49];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertyellow']['shortname'] = $LANGTRACKER["mapping"][149];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertyellow']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertyellow']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertmagenta']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertmagenta']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertmagenta']['name'] = $LANGTRACKER["mapping"][50];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertmagenta']['shortname'] = $LANGTRACKER["mapping"][150];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertmagenta']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesunittransfertmagenta']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgeswaste']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgeswaste']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgeswaste']['name'] = $LANGTRACKER["mapping"][51];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgeswaste']['shortname'] = $LANGTRACKER["mapping"][151];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgeswaste']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgeswaste']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesfuser']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesfuser']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesfuser']['name'] = $LANGTRACKER["mapping"][52];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesfuser']['shortname'] = $LANGTRACKER["mapping"][152];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesfuser']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesfuser']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesbeltcleaner']['table'] = 'glpi_plugin_tracker_printers_cartridges';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesbeltcleaner']['field'] = 'state';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesbeltcleaner']['name'] = $LANGTRACKER["mapping"][53];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesbeltcleaner']['shortname'] = $LANGTRACKER["mapping"][153];
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesbeltcleaner']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['cartridgesbeltcleaner']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['pagecountertotalpages']['table'] = 'glpi_plugin_tracker_printers_history';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountertotalpages']['field'] = 'pages_total';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountertotalpages']['name'] = $LANGTRACKER["mapping"][28];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountertotalpages']['shortname'] = $LANGTRACKER["mapping"][128];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountertotalpages']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountertotalpages']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterblackpages']['table'] = 'glpi_plugin_tracker_printers_history';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterblackpages']['field'] = 'pages_n_b';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterblackpages']['name'] = $LANGTRACKER["mapping"][29];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterblackpages']['shortname'] = $LANGTRACKER["mapping"][129];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterblackpages']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterblackpages']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['pagecountercolorpages']['table'] = 'glpi_plugin_tracker_printers_history';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountercolorpages']['field'] = 'pages_color';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountercolorpages']['name'] = $LANGTRACKER["mapping"][30];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountercolorpages']['shortname'] = $LANGTRACKER["mapping"][130];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountercolorpages']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecountercolorpages']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterrectoversopages']['table'] = 'glpi_plugin_tracker_printers_history';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterrectoversopages']['field'] = 'pages_recto_verso';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterrectoversopages']['name'] = $LANGTRACKER["mapping"][54];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterrectoversopages']['shortname'] = $LANGTRACKER["mapping"][154];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterrectoversopages']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterrectoversopages']['dropdown'] = '';

$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterscannedpages']['table'] = 'glpi_plugin_tracker_printers_history';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterscannedpages']['field'] = 'scanned';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterscannedpages']['name'] = $LANGTRACKER["mapping"][55];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterscannedpages']['shortname'] = $LANGTRACKER["mapping"][155];
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterscannedpages']['type'] = 'text';
$TRACKER_MAPPING[PRINTER_TYPE]['pagecounterscannedpages']['dropdown'] = '';

?>
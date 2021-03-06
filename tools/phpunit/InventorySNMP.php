<?php

define('PHPUnit_MAIN_METHOD', 'Plugins_Fusioninventory_InventorySNMP::main');

if (!defined('GLPI_ROOT')) {
   define('GLPI_ROOT', '../../../..');

   require_once GLPI_ROOT."/inc/includes.php";
   $_SESSION['glpi_use_mode'] = 2;
   $_SESSION['glpiactiveprofile']['id'] = 4;

   ini_set('display_errors','On');
   error_reporting(E_ALL | E_STRICT);
   set_error_handler("userErrorHandler");

   // Backup present DB
   include_once("inc/backup.php");
   backupMySQL();

   $_SESSION["glpilanguage"] = 'fr_FR';

   // Install
   include_once("inc/installation.php");
   installGLPI();
   installFusionPlugins();

   loadLanguage();
   include_once(GLPI_ROOT."/locales/fr_FR.php");
   include_once(GLPI_ROOT."/plugins/fusinvsnmp/locales/fr_FR.php");
   $CFG_GLPI["root_doc"] = GLPI_ROOT;
}
include_once('emulatoragent.php');

/**
 * Test class for MyFile.
 * Generated by PHPUnit on 2010-08-06 at 12:05:09.
 */
class Plugins_Fusioninventory_InventorySNMP extends PHPUnit_Framework_TestCase {

    public static function main() {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Plugins_Fusioninventory_InventorySNMP');
        $result = PHPUnit_TextUI_TestRunner::run($suite);

    }

   public function testSetModuleInventoryOff() {
      global $DB;

     // set in config module inventory = yes by default
     $query = "UPDATE `glpi_plugin_fusioninventory_agentmodules`
        SET `is_active`='0'
        WHERE `modulename`='SNMPQUERY' ";
     $DB->query($query);

   }



   public function testSetModuleInventoryOn() {
      $DB = new DB();

      $query = "UPDATE `glpi_plugin_fusioninventory_agentmodules`
         SET `is_active`='1'
         WHERE `modulename`='SNMPQUERY' ";
      $DB->query($query);

   }



    public function testSendinventories() {

      $MyDirectory = opendir("xml/inventory_snmp");
      while(false !== ($Entry = readdir($MyDirectory))) {
         if(is_dir('xml/inventory_snmp/'.$Entry)&& $Entry != '.' && $Entry != '..') {
            $myVersion = opendir("xml/inventory_snmp/".$Entry);
            while(false !== ($xmlFilename = readdir($myVersion))) {
               if ($xmlFilename != '.' && $xmlFilename != '..') {

                  // We have the XML of each computer inventory
                  $xml = simplexml_load_file("xml/inventory_snmp/".$Entry."/".$xmlFilename,'SimpleXMLElement', LIBXML_NOCDATA);

                  // Send all of xml
                  $this->testSendinventory("xml/inventory_snmp/".$Entry."/".$xmlFilename);
                  foreach ($xml->CONTENT->DEVICE as $child) {
                     // Get device information in GLPI and items_id
                     $array = $this->testGetGLPIDevice("xml/inventory_snmp/".$Entry."/".$xmlFilename, $child);
                     $items_id = $array[0];
                     $itemtype = $array[1];
                     $unknown  = $array[2];
                     // test Infos
                     $this->testInfo($child, "xml/inventory_snmp/".$Entry."/".$xmlFilename, $items_id, $itemtype, $unknown);

                     $this->testIPs($child, "xml/inventory_snmp/".$Entry."/".$xmlFilename,$items_id,$itemtype);

                     $this->testPorts($child, "xml/inventory_snmp/".$Entry."/".$xmlFilename,$items_id,$itemtype);

                     $this->testPortsinfo($child, "xml/inventory_snmp/".$Entry."/".$xmlFilename,$items_id,$itemtype);

                     $this->testPortsVlan($child, "xml/inventory_snmp/".$Entry."/".$xmlFilename,$items_id,$itemtype);

                     $this->testPortsConnections($child, "xml/inventory_snmp/".$Entry."/".$xmlFilename,$items_id,$itemtype);

                     // Verify glpi_networkports_networkports have glpi_networkports existant
                     $this->testNetworkportsIntegrity($child, "xml/inventory_snmp/".$Entry."/".$xmlFilename,$items_id,$itemtype);

                  }
               }
            }
         }
      }
    }


   function testAddNetworkEquipmentHaveCDPConnection() {
      global $DB;
      // Add a networkquipment with a CDP in a connection yet created in unkniwn device.
      // Goal is to not have 2 times with device

      $PluginFusioninventoryUnknownDevice = new PluginFusioninventoryUnknownDevice();
      $NetworkPort = new NetworkPort();
      $a_networkport = $NetworkPort->find("`itemtype`='PluginFusioninventoryUnknownDevice'
         AND `name` like 'GigabitEthernet%'", 'id', '1');
      $datas = current($a_networkport);
      $PluginFusioninventoryUnknownDevice->getFromDB($datas['items_id']);
      
      $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><REQUEST></REQUEST>");
      $xml->addChild('DEVICEID', 'AnotherSwitch.toto.local');
      $xml->addChild('QUERY', 'SNMPQUERY');
      $xml_content = $xml->addChild('CONTENT');
      $xml_device = $xml_content->addChild('DEVICE');
      $xml_info = $xml_device->addChild('INFO');
      $xml_info->addChild('NAME', 'AnotherSwitch');
      $xml_info->addChild('SERIAL', 'NBGTVYU5893FGHJ');
      $xml_info->addChild('TYPE', 'NETWORKING');
      $xml_ips = $xml_info->addChild('IPS');
      $xml_ips->addChild('IP', '10.56.53.23');

      $xml_ports = $xml_device->addChild('PORTS');
      $xml_port = $xml_ports->addChild('PORT');
         $xml_connections = $xml_port->addChild('CONNECTIONS');
         $xml_connections->addChild('CDP', '1');

         $xml_connection = $xml_connections->addChild('CONNECTION');
         $xml_connection->addChild('IFDESCR', 'GigabitEthernet0/10');
         $xml_connection->addChild('IP', '192.168.200.124');

      $xml_port->addChild('IFDESCR', 'GigabitEthernet24/1');
      $xml_port->addChild('IFTYPE', '6');
      $xml_port->addChild('IFNAME', 'GigabitEthernet24/1');
      $xml_port->addChild('IFSTATUS', '1');
      $xml_port->addChild('IFNUMBER', '9');
      $xml_port->addChild('IFINTERNALSTATUS', '1');
      
      $this->testSendinventory('test', $xml);

      $array = $this->testGetGLPIDevice("networkequipment-anotherswitchcdp.xml", $xml_device);
      $items_id = $array[0];
      $itemtype = $array[1];
      $unknown  = $array[2];
      
      $a_unknown = $PluginFusioninventoryUnknownDevice->find("`ip` = '172.25.22.10'");

      $this->assertEquals(count($a_unknown), 1, 'Unknwon CDP has been added in GLPI not 1 times ('.count($a_unknown).')');

      $this->testNetworkportsIntegrity($xml_device, "networkequipment-anotherswitchcdp.xml",$items_id,$itemtype);

   }



   function testAddNetworkEquipmentCDP() {
      // Add a networkequipment which are already created but in unknwon device
      global $DB;

      $PluginFusioninventoryUnknownDevice = new PluginFusioninventoryUnknownDevice();
      $NetworkPort = new NetworkPort();
      $a_networkport = $NetworkPort->find("`itemtype`='PluginFusioninventoryUnknownDevice'
         AND `name` like 'GigabitEthernet%'", 'id', '1');
      $datas = current($a_networkport);
      $PluginFusioninventoryUnknownDevice->getFromDB($datas['items_id']);
      $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><REQUEST></REQUEST>");
      $xml->addChild('DEVICEID', 'testCDP.toto.local');
      $xml->addChild('QUERY', 'SNMPQUERY');
      $xml_content = $xml->addChild('CONTENT');
      $xml_device = $xml_content->addChild('DEVICE');
      $xml_info = $xml_device->addChild('INFO');
      $xml_info->addChild('NAME', 'testCDP');
      $xml_info->addChild('SERIAL', 'GTFD6IYJHGTFTY7');
      $xml_info->addChild('TYPE', 'NETWORKING');
      $xml_ips = $xml_info->addChild('IPS');
      $xml_ips->addChild('IP', $PluginFusioninventoryUnknownDevice->fields['ip']);

      $xml_ports = $xml_device->addChild('PORTS');

      $xml_port = $xml_ports->addChild('PORT');
      $xml_port->addChild('IFDESCR', 'GigabitEthernet45/1');
      $xml_port->addChild('IFTYPE', '6');
      $xml_port->addChild('IFNAME', 'GigabitEthernet45/1');
      $xml_port->addChild('IFSTATUS', '1');
      $xml_port->addChild('IFNUMBER', '9');
      $xml_port->addChild('IFINTERNALSTATUS', '1');

      $xml_port = $xml_ports->addChild('PORT');
      $xml_port->addChild('IFDESCR', $datas['name']);
      $xml_port->addChild('IFTYPE', '6');
      $xml_port->addChild('IFNAME', $datas['name']);
      $xml_port->addChild('IFSTATUS', '1');
      $xml_port->addChild('IFNUMBER', '10');
      $xml_port->addChild('IFINTERNALSTATUS', '1');

      $xml_port = $xml_ports->addChild('PORT');
      $xml_port->addChild('IFDESCR', 'GigabitEthernet10/54');
      $xml_port->addChild('IFTYPE', '6');
      $xml_port->addChild('IFNAME', 'GigabitEthernet10/54');
      $xml_port->addChild('IFSTATUS', '1');
      $xml_port->addChild('IFNUMBER', '11');
      $xml_port->addChild('IFINTERNALSTATUS', '1');


      $this->testSendinventory('test', $xml);

      $array = $this->testGetGLPIDevice("networkequipment-testcdp.xml", $xml_device);
      $items_id = $array[0];
      $itemtype = $array[1];
      $unknown  = $array[2];

      $a_unknown = $PluginFusioninventoryUnknownDevice->find("`id` = '".$datas['items_id']."'");

      $this->assertEquals(count($a_unknown), 0, 'Switch has been added in GLPI but unknown device with CDP yet added is not fusionned with switch (unknown id : '.$datas['items_id'].')');

      // Test if port is moved from unknown device to switch
      $NetworkPort->getFromDB($datas['id']);
      $this->assertEquals($NetworkPort->fields['itemtype'], 'NetworkEquipment', 'Port has not been transfered from unknown device to switch port');
      // Test if extension of port informations have been right created
      $query = "SELECT * FROM `glpi_plugin_fusinvsnmp_networkports`
         WHERE `networkports_id`='".$datas['id']."'";
      $result = $DB->query($query);
      $this->assertEquals($DB->numrows($result), 1, 'Port extension has not been created');

      // Test if port connected on unknown device is connected on switch port

   }


   
   /*
    * If a port is connected on a hub and we disconnect,
    * port in hub is empty and we must delete this port
    */
   function testDisconnectPortHub() {
      // Get a hub with 3 ports mini
      $PluginFusioninventoryUnknownDevice = new PluginFusioninventoryUnknownDevice();
      $NetworkPort_NetworkPort = new NetworkPort_NetworkPort();
      $NetworkPort = new NetworkPort();

      $a_hub = $PluginFusioninventoryUnknownDevice->find("`hub`='1'");
      $hub_found = 0;
      $port_id = 0;
      $port_count = 0;
      $hub_id = 0;
      foreach ($a_hub as $hub) {
         if ($hub_found == '0') {
            $a_ports = $NetworkPort->find("`itemtype`='PluginFusioninventoryUnknownDevice'
                  AND `items_id`='".$hub['id']."'
                  AND `name` is NULL");

            if (count($a_ports) > 1) {
               $port_count = count($a_ports);
               foreach ($a_ports as $port) {
                  // Get port connected
                  $port_id = $NetworkPort_NetworkPort->getOppositeContact($port['id']);
               }               
               $hub_found = 1;
               $hub_id = $hub['id'];
            }
         }
      }
      
      // Get mac address
      $NetworkPort->getFromDB($port_id);
      // Create a switch with connection with this mac address
      $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><REQUEST></REQUEST>");
      $xml->addChild('DEVICEID', 'testhub.toto.local');
      $xml->addChild('QUERY', 'SNMPQUERY');
      $xml_content = $xml->addChild('CONTENT');
      $xml_device = $xml_content->addChild('DEVICE');
      $xml_info = $xml_device->addChild('INFO');
      $xml_info->addChild('NAME', 'testhub');
      $xml_info->addChild('SERIAL', 'GTIYJHIOH6748HUY');
      $xml_info->addChild('TYPE', 'NETWORKING');
      $xml_ips = $xml_info->addChild('IPS');
      $xml_ips->addChild('IP', '192.168.56.34');

      $xml_ports = $xml_device->addChild('PORTS');

      $xml_port = $xml_ports->addChild('PORT');
      $xml_connections = $xml_port->addChild('CONNECTIONS');
      $xml_connection = $xml_connections->addChild('CONNECTION');
      $xml_connection->addChild('MAC', $NetworkPort->fields['mac']);
      $xml_port->addChild('IFDESCR', 'GigabitEthernet0/4');
      $xml_port->addChild('IFTYPE', '6');
      $xml_port->addChild('IFNAME', 'GigabitEthernet0/4');
      $xml_port->addChild('IFSTATUS', '1');
      $xml_port->addChild('IFNUMBER', '9');
      $xml_port->addChild('IFINTERNALSTATUS', '1');

      $this->testSendinventory('test', $xml);

      $array = $this->testGetGLPIDevice("networkequipment-testcdp.xml", $xml_device);
      $items_id = $array[0];
      $itemtype = $array[1];
      $unknown  = $array[2];

      // verify in the hub the port has been removed
      $a_ports_verif = $NetworkPort->find("`itemtype`='PluginFusioninventoryUnknownDevice'
                  AND `items_id`='".$hub_id."'
                  AND `name` is NULL");

      $this->assertEquals(count($a_ports_verif), ($port_count - 1), 'Port of hub disconnected not deleted (Hub id : '.$hub_id.')');

   }





      function testInfo($xml='', $xmlFile='', $items_id=0, $itemtype='', $unknown=0) {

         if (empty($xmlFile)) {
            echo "testInfo with no arguments...\n";
            return;
         }
         $class = new $itemtype;
         $class->getFromDB($items_id);

         foreach ($xml->INFO as $child2) {
            $this->assertEquals($class->fields['name'], (string)$child2->NAME , 'Difference of Hardware name, have '.$class->fields['name'].' instead '.$child2->NAME.' ['.$xmlFile.']');
            $this->assertEquals($class->fields['serial'], (string)$child2->SERIAL , 'Difference of Hardware serial, have '.$class->fields['serial'].' instead '.$child2->SERIAL.' ['.$xmlFile.']');

            if ($child2->TYPE == 'PRINTER') {
               if (isset($child2->MODEL)) {
                  $PrinterModel = new PrinterModel();
                  $this->assertEquals($class->fields['printermodels_id'], $PrinterModel->import(array('name'=>(string)$child2->MODEL)) , 'Difference of Hardware model, have '.$class->fields['printermodels_id'].' instead '.$PrinterModel->import(array('name'=>$child2->MODEL)).' ['.$xmlFile.']');
               }
               if (isset($child2->MANUFACTURER)) {
                  $Manufacturer = new Manufacturer();
                  $this->assertEquals($class->fields['manufacturers_id'], $Manufacturer->import(array('name'=>(string)$child2->MANUFACTURER)) , 'Difference of Hardware manufacturer, have '.$class->fields['manufacturers_id'].' instead '.$Manufacturer->import(array('name'=>$child2->MANUFACTURER)).' ['.$xmlFile.']');
               }
               $this->assertEquals($class->fields['memory_size'], (string)$child2->MEMORY , 'Difference of Hardware memory size, have '.$class->fields['memory_size'].' instead '.$child2->MEMORY.' ['.$xmlFile.']');
            } else if ($child2->TYPE == 'NETWORKING') {
               $this->assertEquals($class->fields['ram'], (string)$child2->RAM , 'Difference of Hardware ram size, have '.$class->fields['ram'].' instead '.$child2->RAM.' ['.$xmlFile.']');
            }
            if (isset($child2->LOCATION)) {
               $Location = new Location();
               $this->assertEquals($class->fields['locations_id'], $Location->import(array('name' => (string)$child2->LOCATION, 'entities_id' => '0')) , 'Difference of Hardware location, have '.$class->fields['locations_id'].' instead '.$Location->import(array('name' => (string)$child2->LOCATION, 'entities_id' => '0')).' ['.$xmlFile.']');
            }
            /*
 *         <COMMENTS>Xerox WorkCentre M20i ; OS 1.22   Engine 4.1.08 NIC V2.22(M20i) DADF 1.04</COMMENTS>
 */
         }
      }



   function testSendinventory($xmlFile='', $xml='') {

      if (empty($xmlFile)) {
         echo "testSendinventory with no arguments...\n";
         return;
      }

      $emulatorAgent = new emulatorAgent;
      $emulatorAgent->server_urlpath = "/glpi078/plugins/fusioninventory/front/communication.php";
      if (empty($xml)) {
         $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);
      }
      
      // Send prolog for creation of agent in GLPI
      $input_xml = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <DEVICEID>'.$xml->DEVICEID.'</DEVICEID>
  <QUERY>PROLOG</QUERY>
  <TOKEN>CBXTMXLU</TOKEN>
</REQUEST>';
      $emulatorAgent->sendProlog($input_xml);

      foreach ($xml->CONTENT->DEVICE as $child) {
         foreach ($child->INFO as $child2) {
            if ($child2->TYPE == 'PRINTER') {
               // Create switch in asset
               $Printer = new Printer();
               $input = array();
               $input['serial']=$child2->SERIAL;
               $Printer->add($input);
            } else if ($child2->TYPE == 'NETWORKING') {
               // Create switch in asset
               $NetworkEquipment = new NetworkEquipment();
               $input = array();
               if (isset($child2->SERIAL)) {
                  $input['serial']=$child2->SERIAL;
               } else {
                  $input['name']=$child2->NAME;
               }
               $NetworkEquipment->add($input);
            }
         }
      }
      $input_xml = $xml->asXML();
      $emulatorAgent->sendProlog($input_xml);
      
   }


   function testGetGLPIDevice($xmlFile='', $xml='') {
      
      if (empty($xmlFile)) {
         echo "testGetGLPIDevice with no arguments...\n";
         return;
      }

      $input = array();
      if ((string)$xml->INFO->TYPE == 'PRINTER') {
         $input['serial']=(string)$xml->INFO->SERIAL;
         $name = (string)$xml->INFO->NAME;
      } else if ((string)$xml->INFO->TYPE == 'NETWORKING') {
         $input['serial']=(string)$xml->INFO->SERIAL;
         $name = (string)$xml->INFO->NAME;
      }
 
      $serial = "`serial` IS NULL";

      if ((isset($input['serial'])) && (!empty($input["serial"]))) {
         $serial = "`serial`='".$input['serial']."'";
      }
      
      $itemtype = '';
      $a_devices = array();
      if (strstr($xmlFile, 'printer')) {
         $itemtype = 'printer';
         $Printer = new Printer();
         $a_devices = $Printer->find("`name`='".$name."' AND ".$serial);
      } else if (strstr($xmlFile, 'networkequipment')) {
         $itemtype = 'networkequipment';
         $NetworkEquipment = new NetworkEquipment();
         $a_devices = $NetworkEquipment->find("`name`='".$name."' AND ".$serial);
      }
      $unknown = 0;
      if (count($a_devices) == 0) {
         // Search in unknown device
         $PluginFusioninventoryUnknownDevice = new PluginFusioninventoryUnknownDevice();
         $a_devices = $PluginFusioninventoryUnknownDevice->find("`name`='".$name."'");
         $unknown = 1;
      }
      $this->assertEquals(count($a_devices), 1 , 'Problem on creation device, not created ('.$xmlFile.')');
      foreach($a_devices as $items_id => $data) {
         return array($items_id, $itemtype, $unknown);
      }
   }


   function testIPs($xml='', $xmlFile='',$items_id=0,$itemtype='') {

      if (empty($xmlFile)) {
         echo "testIPs with no arguments...\n";
         return;
      }

      if ($itemtype != 'networkequipment') {
         echo "testIPs with itemtype not networkequipment...\n";
         return;
      }

      $count_ips = 0;
      foreach ($xml->INFO->IPS->IP as $child) {
         if ($child != "127.0.0.1") {
            $count_ips++;
         }
      }

      $PluginFusinvsnmpNetworkEquipmentIP = new PluginFusinvsnmpNetworkEquipmentIP();
      $a_ips = $PluginFusinvsnmpNetworkEquipmentIP->find("`networkequipments_id`='".$items_id."'");
      $this->assertEquals(count($a_ips), $count_ips , 'Problem on manage IPs of the switch, '.count($a_ips).' instead '.$count_ips.' ['.$xmlFile.']');
   }


   public function testPorts($xml='', $xmlFile='',$items_id=0,$itemtype='') {

      if (empty($xmlFile)) {
         echo "testPorts with no arguments...\n";
         return;
      }

      $NetworkPort = new NetworkPort();
      $PluginFusinvsnmpNetworkPort = new PluginFusinvsnmpNetworkPort($itemtype);
      $count_ports = 0;
      foreach ($xml->PORTS->PORT as $child) {
         if ($PluginFusinvsnmpNetworkPort->isReal($child->IFTYPE)) {
            $count_ports++;
         }
      }
      $a_ports = $NetworkPort->find("`itemtype`='".$itemtype."' AND `items_id`='".$items_id."'");

      $this->assertEquals(count($a_ports), $count_ports , 'Problem on creation of ports, '.count($a_ports).' instead '.$count_ports.' ['.$xmlFile.']');
   }


   public function testPortsinfo($xml='', $xmlFile='',$items_id=0,$itemtype='') {

      if (empty($xmlFile)) {
         echo "testPorts with no arguments...\n";
         return;
      }

      $NetworkPort = new NetworkPort();
      $PluginFusinvsnmpNetworkPort = new PluginFusinvsnmpNetworkPort();

      if ((string)$xml->INFO->TYPE == 'NETWORKING') {
         foreach ($xml->PORTS->children() as $name=>$child) {
            if ((string)$child->IFTYPE == '6') {
               $a_ports = array();
               if (isset($child->IFNAME)) {
                  $a_ports = $NetworkPort->find("`itemtype`='".$itemtype."' AND `items_id`='".$items_id."'
                                             AND `name`='".(string)$child->IFNAME."'");
               } else {
                  $a_ports = $NetworkPort->find("`itemtype`='".$itemtype."' AND `items_id`='".$items_id."'
                                             AND `name`='".(string)$child->IFDESCR."'");
               }
               $data = array();
               foreach ($a_ports as $id => $data) {

               }
               $oFusioninventory_networkport = new PluginFusinvsnmpCommonDBTM("glpi_plugin_fusinvsnmp_networkports");
               $a_portsExt = $oFusioninventory_networkport->find("`networkports_id`='".$id."'");
               $dataExt = array();
               foreach ($a_portsExt as $idExt => $dataExt) {

               }
               if (isset($child->IFNAME)) {
                  $this->assertEquals($data['name'], (string)$child->IFNAME , 'Name of port not good ("'.$data['name'].'" instead of "'.(string)$child->IFNAME.'")['.$xmlFile.']');
               } else {
                  $this->assertEquals($data['name'], (string)$child->IFDESCR , 'Name of port not good ("'.$data['name'].'" instead of "'.(string)$child->IFDESCR.'")['.$xmlFile.']');
               }
               if (!strstr((string)$child->MAC, '00:00:00')) {
                  $this->assertEquals($data['mac'], (string)$child->MAC , 'Mac of port not good ("'.$data['mac'].'" instead of "'.(string)$child->MAC.'")['.$xmlFile.']');
               }
               $this->assertEquals($data['logical_number'], (string)$child->IFNUMBER , 'Number of port not good ("'.$data['logical_number'].'" instead of "'.(string)$child->IFNUMBER.'")['.$xmlFile.']');
               if (isset($child->IFDESCR)) {
                  $this->assertEquals($dataExt['ifdescr'], (string)$child->IFDESCR , 'Description of port not good ("'.$dataExt['ifdescr'].'" instead of "'.(string)$child->IFDESCR.'")['.$xmlFile.']');
               }
               if (isset($child->IFMTU)) {
                  $this->assertEquals($dataExt['ifmtu'], (string)$child->IFMTU , 'MTU of port not good ("'.$dataExt['ifmtu'].'" instead of "'.(string)$child->IFMTU.'")['.$xmlFile.']');
               }
               if (isset($child->IFSPEED)) {
                  $this->assertEquals($dataExt['ifspeed'], (string)$child->IFSPEED , 'Speed of port not good ("'.$dataExt['ifspeed'].'" instead of "'.(string)$child->IFSPEED.'")['.$xmlFile.']');
               }
               if (isset($child->IFINTERNALSTATUS)) {
                  $this->assertEquals($dataExt['ifinternalstatus'], (string)$child->IFINTERNALSTATUS , 'Internal status of port not good ("'.$dataExt['ifinternalstatus'].'" instead of "'.(string)$child->IFINTERNALSTATUS.'")['.$xmlFile.']');
               }
               if (isset($child->IFLASTCHANGE)) {
                  $this->assertEquals($dataExt['iflastchange'], (string)$child->IFLASTCHANGE , 'Last change of port not good ("'.$dataExt['iflastchange'].'" instead of "'.(string)$child->IFLASTCHANGE.'")['.$xmlFile.']');
               }
               if (isset($child->IFINOCTETS)) {
                  $this->assertEquals($dataExt['ifinoctets'], (string)$child->IFINOCTETS , 'In octets of port not good ("'.$dataExt['ifinoctets'].'" instead of "'.(string)$child->IFINOCTETS.'")['.$xmlFile.']');
               }
               if (isset($child->IFINERRORS)) {
                  $this->assertEquals($dataExt['ifinerrors'], (string)$child->IFINERRORS , 'In errors of port not good ("'.$dataExt['ifinerrors'].'" instead of "'.(string)$child->IFINERRORS.'")['.$xmlFile.']');
               }
               if (isset($child->IFOUTOCTETS)) {
                  $this->assertEquals($dataExt['ifoutoctets'], (string)$child->IFOUTOCTETS , 'Out octets of port not good ("'.$dataExt['ifoutoctets'].'" instead of "'.(string)$child->IFOUTOCTETS.'")['.$xmlFile.']');
               }
               if (isset($child->IFOUTERRORS)) {
                  $this->assertEquals($dataExt['ifouterrors'], (string)$child->IFOUTERRORS , 'out errors of port not good ("'.$dataExt['ifouterrors'].'" instead of "'.(string)$child->IFOUTERRORS.'")['.$xmlFile.']');
               }
               if (isset($child->IFSTATUS)) {
                  $this->assertEquals($dataExt['ifstatus'], (string)$child->IFSTATUS , 'Status of port not good ("'.$dataExt['ifstatus'].'" instead of "'.(string)$child->IFSTATUS.'")['.$xmlFile.']');
               }
               if (isset($child->TRUNK)) {
                  $this->assertEquals($dataExt['trunk'], (string)$child->TRUNK , 'trunk port not good ("'.$dataExt['trunk'].'" instead of "'.(string)$child->TRUNK.'")['.$xmlFile.']');
               } else {
                  $this->assertEquals($dataExt['trunk'], 0 , 'trunk port not good ("'.$dataExt['trunk'].'" instead of "0")['.$xmlFile.']');
               
               }
            }
         }
      }

   }

   function testPortsVlan($xml='', $xmlFile='',$items_id=0,$itemtype='') {

      if (empty($xmlFile)) {
         echo "testPortsVlan with no arguments...\n";
         return;
      }

      $NetworkPort = new NetworkPort();

      foreach ($xml->PORTS->children() as $child) {
         if ((string)$child->IFTYPE == '6') {

            $a_ports = array();
            $a_ports = $NetworkPort->find("`itemtype`='".$itemtype."' AND `items_id`='".$items_id."'
                                          AND `logical_number`='".(string)$child->IFNUMBER."'");
            $data = array();
            foreach ($a_ports as $id => $data) {
            }

            $vlanDB = NetworkPort_Vlan::getVlansForNetworkPort($id);
            $vlanDB_Name_Comment = array();
            foreach ($vlanDB as $vlan_id) {
               $temp = Dropdown::getDropdownName('glpi_vlans', $vlan_id, 1);
               $vlanDB_Name_Comment[$temp['name']."-".$temp['comment']] = 1;
            }
            $nb_errors = 0;
            $forgotvlan = '';
            if (isset($child->VLANS)) {
               foreach ($child->VLANS->children() as $childvlan) {
                  //if (!isset($vlanDB_Name_Comment[strval($childvlan->NUMBER)."-".strval($childvlan->NAME)])) {
                  if (!array_key_exists((string)$childvlan->NUMBER."-".(string)$childvlan->NAME, $vlanDB_Name_Comment)) {
                     $nb_errors++;
                     $forgotvlan .= (string)$childvlan->NUMBER."-".(string)$childvlan->NAME." | ";
                  } else {
                     unset($vlanDB_Name_Comment[(string)$childvlan->NUMBER."-".(string)$childvlan->NAME]);
                  }
               }
            }
            $this->assertEquals($forgotvlan, '' , 'Vlans not in DB ('.$forgotvlan.' for port '.$child->IFNAME.')['.$xmlFile.']');
            $this->assertEquals(count($vlanDB_Name_Comment), 0 , 'Vlans in DB but not in the XML ("'.print_r($vlanDB_Name_Comment, true).'"  for port '.(string)$child->IFNAME.')['.$xmlFile.']');
         }
      }
   }


   public function testPortsConnections($xml='', $xmlFile='',$items_id=0,$itemtype='') {

      if (empty($xmlFile)) {
         echo "testPortsConnections with no arguments...\n";
         return;
      }

      $NetworkPort = new NetworkPort();
      $PluginFusinvsnmpNetworkPort = new PluginFusinvsnmpNetworkPort();
      $NetworkPort_NetworkPort = new NetworkPort_NetworkPort();

      foreach ($xml->PORTS->children() as $name=>$child) {
         if ((string)$child->IFTYPE == '6') {

            $a_ports = $NetworkPort->find("`itemtype`='".$itemtype."' AND `items_id`='".$items_id."'
                                          AND `name`='".(string)$child->IFNAME."'");
            $data = array();
            foreach ($a_ports as $id => $data) {
            }

            if (isset($child->CONNECTIONS)) {
               foreach ($child->CONNECTIONS->children() as $nameconnect => $childconnect) {
                  if (isset($child->CONNECTIONS->CDP)) { // Manage CDP

                     


                  } else { // Manage tradictionnal connections
                     // Search in DB if MAC exist

                     $a_port = $NetworkPort->find("`mac`='".strval($childconnect->MAC)."'
                                                   AND `itemtype`='PluginFusioninventoryUnknownDevice' ");
                     $this->assertEquals(count($a_port), 1 , 'Port (connection) not good created, '.count($a_port).' instead of 1 port ('.strval($childconnect->MAC).' (test on mac : '.$childconnect->MAC.' on portname '.$child->IFNAME.')['.$xmlFile.']');
                     foreach($a_port as $ports_id => $datas) {
                     }
                     if (count($child->CONNECTIONS->CONNECTION->children()) > 1) {
                        // Hub management
                        $hubLink_id = $NetworkPort_NetworkPort->getOppositeContact($ports_id);
                        $NetworkPort->getFromDB($hubLink_id);
                        $a_portHub = $NetworkPort->find("`items_id`='".$NetworkPort->fields['items_id']."'
                                                   AND `itemtype`='PluginFusioninventoryUnknownDevice' ");
                        $this->assertEquals(count($a_portHub), count($child->CONNECTIONS->CONNECTION->children()) + 1 , 'Number of ports on hub not correct, '.count($a_portHub).' instead of '.(count($child->CONNECTIONS->CONNECTION->children()) + 1).' port (hub id : '.$NetworkPort->fields['items_id'].') ['.$xmlFile.']');

                     } else {
                        
                        $this->assertTrue($NetworkPort_NetworkPort->getFromDBForNetworkPort($ports_id) , 'Unknown port connection not connected with an other device['.$xmlFile.']');

                     }
                  }
               }
            }
         }
      }
   }


   // Test if network port connected on each port exist (verify integrity of datas)
   function testNetworkportsIntegrity($xml='', $xmlFile='',$items_id=0,$itemtype='') {

      if (empty($xmlFile)) {
         echo "testNetworkportsIntegrity with no arguments...\n";
         return;
      }

      $NetworkPort = new NetworkPort();
      $NetworkPort_NetworkPort = new NetworkPort_NetworkPort();

      foreach ($xml->PORTS->children() as $name=>$child) {
         if ((string)$child->IFTYPE == '6') {
            
            $a_ports = $NetworkPort->find("`itemtype`='".$itemtype."' AND `items_id`='".$items_id."'
                                          AND `name` IN ('".(string)$child->IFNAME."', '".(string)$child->IFDESCR."')");
            $this->assertEquals(count($a_ports), 1 , 'Found more than 1 port in DB ('.count($a_ports).' ports instead 1 for port '.(string)$child->IFDESCR.')['.$xmlFile.']');
           
            $data = array();
            $data = current($a_ports);

            if ($NetworkPort_NetworkPort->getOppositeContact($data['id'])) {
               $this->assertTrue($NetworkPort->getFromDB($NetworkPort_NetworkPort->getOppositeContact($data['id'])),
                  'Port integrity problem (id_source=>'.$data['id'].' - id_dest=>'.$NetworkPort_NetworkPort->getOppositeContact($data['id']).') ['.$xmlFile.']');

               
               
            }
         }
      }
      
   }

   

}

// Call Plugins_Fusioninventory_Discovery_Newdevices::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Plugins_Fusioninventory_InventorySNMP::main') {
    Plugins_Fusioninventory_InventorySNMP::main();

}

//restoreMySQL();
//unlink('backup/backup.sql');
?>
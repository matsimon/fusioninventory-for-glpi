<?php

define('PHPUnit_MAIN_METHOD', 'Plugins_Fusioninventory_InventoryLocal::main');
    
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

   $CFG_GLPI["root_doc"] = GLPI_ROOT;
}
require_once 'emulatoragent.php';

/**
 * Test class for MyFile.
 * Generated by PHPUnit on 2010-08-06 at 12:05:09.
 */
class Plugins_Fusioninventory_InventoryLocal extends PHPUnit_Framework_TestCase {

    public static function main() {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('Plugins_Fusioninventory_InventoryLocal');
        $result = PHPUnit_TextUI_TestRunner::run($suite);

    }


    public function testSetModuleInventoryOff() {
       global $DB,$LANG;
       loadLanguage();


         //deleteDir(GLPI_ROOT."/files/_plugins/fusioninventory/criterias");
         //deleteDir(GLPI_ROOT."/files/_plugins/fusioninventory/machines");
         system("rm -fr ".GLPI_ROOT."/files/_plugins/fusioninventory/criterias");
         system("rm -fr ".GLPI_ROOT."/files/_plugins/fusioninventory/machines");

        // set in config module inventory = yes by default
        $query = "UPDATE `glpi_plugin_fusioninventory_agentmodules`
           SET `is_active`='0'
           WHERE `modulename`='INVENTORY' ";
        $result = $DB->query($query);
       
    }


//    public function testSendinventoryOff() {
//       $this->testSendinventory();
//    }
//
//
//   public function testMachinesCriteriasFoldersOff() {
//      $exist = 0;
//      if (file_exists(GLPI_ROOT."/files/_plugins/fusioninventory/machines")) {
//         $exist = 1;
//      }
//      $this->assertEquals($exist, 0 , 'Problem on inventory, machines & criterias folder must not create because inventory not allowed on this agent');
//   }



    public function testSetModuleInventoryOn() {
       global $DB;
       
        // set in config module inventory = yes by default
        $query = "UPDATE `glpi_plugin_fusioninventory_agentmodules`
           SET `is_active` = '1'
           WHERE `modulename` = 'INVENTORY'";
        $DB->query($query);
     }

     

    public function testSendinventories() {
      
      $MyDirectory = opendir("xml/inventory_local");
      while(false !== ($Entry = readdir($MyDirectory))) {
         if(is_dir('xml/inventory_local/'.$Entry)&& $Entry != '.' && $Entry != '..') {
            $myVersion = opendir("xml/inventory_local/".$Entry);
            while(false !== ($xmlFilename = readdir($myVersion))) {
               if ($xmlFilename != '.' && $xmlFilename != '..') {

                  // We have the XML of each computer inventory
                  $xml = simplexml_load_file("xml/inventory_local/".$Entry."/".$xmlFilename,'SimpleXMLElement', LIBXML_NOCDATA);

                  $deviceid_ok = 0;
                  if (!empty($xml->DEVICEID)) {
                     $deviceid_ok = 1;
                  }
                  $this->assertEquals($deviceid_ok, 1 , 'Problem on XML, DEVICEID of file xml/inventory_local/'.$Entry.'/'.$xmlFilename.' not good!');

                  $inputProlog = '<?xml version="1.0" encoding="UTF-8"?>
<REQUEST>
  <DEVICEID>'.$xml->DEVICEID.'</DEVICEID>
  <QUERY>PROLOG</QUERY>
  <TOKEN>NTMXKUBJ</TOKEN>
</REQUEST>';

                  $this->testProlog($inputProlog, $xml->DEVICEID);

                  $array = $this->testSendinventory("xml/inventory_local/".$Entry."/".$xmlFilename);
                  $items_id = $array[0];
                  $unknown  = $array[1];

                  $this->testPrinter("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testMonitor("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testCPU("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testDrive("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testController("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testSound("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testVideo("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testMemory("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testNetwork("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testSoftware("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);

                  $this->testHardware("xml/inventory_local/".$Entry."/".$xmlFilename, $items_id, $unknown);
               }
            }
         }
      }
   }

   

   public function testMachinesCriteriasFolders() {
      $exist = 0;
      if (file_exists(GLPI_ROOT."/files/_plugins/fusioninventory/machines")) {
         $exist = 1;
      }
      $this->assertEquals($exist, 1 , 'Problem on inventory, machines & criterias folder not create successfully!');
   }


   function testProlog($inputXML='', $deviceID='') {
      global $DB;

      if (empty($inputXML)) {
         echo "testProlog with no arguments...\n";
         return;
      }
      $emulatorAgent = new emulatorAgent;
      $emulatorAgent->server_urlpath = "/glpi078/plugins/fusioninventory/front/communication.php";
      $prologXML = $emulatorAgent->sendProlog($inputXML);
      $PluginFusioninventoryAgent = new PluginFusioninventoryAgent();
      $a_agent = $PluginFusioninventoryAgent->find("`device_id`='".$deviceID."'");
      $this->assertEquals(count($a_agent), 1 , 'Problem on prolog, agent ('.$deviceID.') not right created!');

      $this->assertEquals(preg_match("/<RESPONSE>SEND<\/RESPONSE>/",$prologXML), 1, 'Prolog not send to agent!');
   }

   function testSendinventory($xmlFile='') {
      
      if (empty($xmlFile)) {
         echo "testSendinventory with no arguments...\n";
         return;
      }

      $emulatorAgent = new emulatorAgent;
      $emulatorAgent->server_urlpath = "/glpi078/plugins/fusioninventory/front/communication.php";
      $input_xml = file_get_contents($xmlFile);
      $emulatorAgent->sendProlog($input_xml);

      $Computer = new Computer();
      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);
      if (isset($xml->CONTENT->BIOS->SSN)) {
         $xml->CONTENT->BIOS->SSN = trim($xml->CONTENT->BIOS->SSN);
      }
      $serial = "`serial` IS NULL";
      if ((isset($xml->CONTENT->BIOS->SSN)) AND (!empty($xml->CONTENT->BIOS->SSN))) {
         $serial = "`serial`='".$xml->CONTENT->BIOS->SSN."'";
      }
      $a_computers = $Computer->find("`name`='".$xml->CONTENT->HARDWARE->NAME."' AND ".$serial);
      $unknown = 0;
      if (count($a_computers) == 0) {
         // Search in unknown device
         $PluginFusioninventoryUnknownDevice = new PluginFusioninventoryUnknownDevice();
         $a_computers = $PluginFusioninventoryUnknownDevice->find("`name`='".$xml->CONTENT->HARDWARE->NAME."'");
         $unknown = 1;
      }
      $this->assertEquals(count($a_computers), 1 , 'Problem on creation computer, not created ('.$xmlFile.')');
      foreach($a_computers as $items_id => $data) {
         return array($items_id, $unknown);
      }
   }


   function testPrinter($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testPrinter with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $Computer = new Computer();
      $Printer  = new Printer();

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->PRINTERS)) {
         return;
      }
      // Verify not have 2 printer in DB with same printer serial
      foreach ($xml->CONTENT->PRINTERS as $child) {
         if (isset($child->SERIAL)) {
            $a_printer = $Printer->find("`serial`='".$child->SERIAL."'");
            $this->assertEquals(count($a_printer), 1 , 'Problem on printers, printer created "'.count($a_printer).'" instead 1 times');
         }         
      }
      // Verify all printers are connected to the computer
         // Get all printers connected to computer in DB
         $query = "SELECT * FROM `glpi_computers_items`
                  INNER JOIN `glpi_printers` on `glpi_printers`.`id`=`items_id`
                      WHERE `computers_id` = '".$items_id."'
                            AND `itemtype` = 'Printer'";
         $result=$DB->query($query);
         $a_printerDB = array();
         while ($data=$DB->fetch_array($result)) {
            $a_printerDB["'".$data['name']."'"] = 1;
         }
         // Verify printers in XML
         $a_printerXML = array();
         foreach ($xml->CONTENT->PRINTERS as $child) {
            $a_printerXML["'".$child->NAME."'"] = 1;
         }
         // Display (test) differences
         $a_printerDiff = array();
         $a_printerDiff = array_diff_key($a_printerDB, $a_printerXML);
         if (count($a_printerDiff) < count(array_diff_key($a_printerXML, $a_printerDB))) {
            $a_printerDiff = array_diff_key($a_printerXML, $a_printerDB);
         }
         $this->assertEquals(count($a_printerDiff), 0 , 'Difference of printers "'.print_r($a_printerDiff, true).'" ['.$xmlFile.']');


         // Verify fields in GLPI
         foreach($xml->CONTENT->PRINTERS as $child) {
            if (isset($child->SERIAL)) {
               $a_printer = $Printer->find("`serial`='".$child->SERIAL."' ");
               foreach ($a_printer as $printer_id => $datas) {
                  if (isset($child->NAME)) {
                     $this->assertEquals(trim($child->NAME), $datas['name'] , 'Difference of printers fields ['.$xmlFile.']');
                  } else if (isset($child->DRIVER)) {
                     $this->assertEquals($child->DRIVER, $datas['name'] , 'Difference of printers fields ['.$xmlFile.']');
                  }
                  if (strstr($child->PORT, "USB")) {
                     $this->assertEquals("1", $datas['have_usb'] , 'Difference of printers fields ['.$xmlFile.']');
                  }
                  // Find in USBDEVICES to find manufacturer
                  foreach($xml->CONTENT->USBDEVICES as $childusb) {
                     if (isset($childusb->SERIAL)) {
                        if (file_exists(GLPI_ROOT."/files/_plugins/fusioninventory/DataFilter/usbids/".strtolower($childusb->VENDORID)."/".strtolower($childusb->PRODUCTID)."info")) {
                           $info = file_get_contents(GLPI_ROOT."/files/_plugins/fusioninventory/DataFilter/usbids/".strtolower($childusb->VENDORID)."/".strtolower($childusb->PRODUCTID)."info");
                           $array = explode("\n", $info);
                           $manufacturer_id = Dropdown::importExternal('Manufacturer', $array[0]);
                           $this->assertEquals($manufacturer_id, $datas['manufacturers_id'] , 'Difference of printers fields ['.$xmlFile.']');
                        }
                     }
                  }
               }
            } else {
               $query = "SELECT * FROM `glpi_computers_items`
                        INNER JOIN `glpi_printers` on `glpi_printers`.`id`=`items_id`
                        WHERE `computers_id` = '".$items_id."'
                            AND `itemtype` = 'Printer'";
               $result=$DB->query($query);
               $printer_select = array();
               while ($data=$DB->fetch_array($result)) {
                  if (count($printer_select) < 1) {
                     if ((isset($child->NAME)) AND ($data['name'] == $child->NAME)) {
                        $printer_select = $data;
                     } else if ((isset($child->DRIVER)) AND ($data['name'] == $child->DRIVER)) {
                        $printer_select = $data;
                     }
                  }
               }
               $this->assertEquals(count($printer_select['id']), "1" , 'Problem to find printer for fields verification ['.$xmlFile.']');
               if (strstr($child->PORT, "USB")) {
                  $this->assertEquals("1", $datas['have_usb'] , 'Difference of printers fields ['.$xmlFile.']');
               }
            }

         }
   }


   function testMonitor($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testMonitor with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $Computer = new Computer();
      $Monitor  = new Monitor();

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->MONITORS)) {
         return;
      }

      // Verify not have 2 monitor in DB with same printer serial
      foreach ($xml->CONTENT->MONITORS as $child) {
         if (isset($child->SERIAL)) {
            $a_monitor = $Monitor->find("`serial`='".$child->SERIAL."'");
            $this->assertEquals(count($a_monitor), 1 , 'Problem on monitors, monitor created "'.count($a_monitor).'" instead 1 times');
         }
      }

      // Verify all monitors are connected to the computer
         // Get all monitors connected to computer in DB
         $query = "SELECT * FROM `glpi_computers_items`
                  INNER JOIN `glpi_monitors` on `glpi_monitors`.`id`=`items_id`
                      WHERE `computers_id` = '".$items_id."'
                            AND `itemtype` = 'Monitor'";
         $result=$DB->query($query);
         $a_monitorDB = array();
         while ($data=$DB->fetch_array($result)) {
            $a_monitorDB["'".$data['name']."'"] = 1;
         }
         // Verifiy monitors in XML
         $a_monitorXML = array();
         foreach ($xml->CONTENT->MONITORS as $child) {
            $a_monitorXML["'".$child->CAPTION."'"] = 1;
         }
         // Display (test) differences
         $a_monitorDiff = array();
         $a_monitorDiff = array_diff_key($a_monitorDB, $a_monitorXML);
         if (count($a_monitorDiff) < count(array_diff_key($a_monitorXML, $a_monitorDB))) {
            $a_monitorDiff = array_diff_key($a_monitorXML, $a_monitorDB);
         }
         $this->assertEquals(count($a_monitorDiff), 0 , 'Difference of monitors "'.print_r($a_monitorDiff, true).'"');

   }


   function testCPU($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testCPU with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->CPUS)) {
         return;
      }

      $a_cpuXML = array();
      $i = 0;
      foreach ($xml->CONTENT->CPUS as $child) {
         if (isset($child->NAME)) {
            $a_cpuXML["'".$i."-".$child->NAME."'"] = 1;
            $i++;
         }
      }

      $Computer = new Computer();
      $query = "SELECT * FROM `glpi_computers_deviceprocessors`
         WHERE `computers_id`='".$items_id."' ";
      $result=$DB->query($query);

      $this->assertEquals($DB->numrows($result), count($a_cpuXML) , 'Difference of CPUs, created '.$DB->numrows($result).' times instead '.count($a_cpuXML).' ['.$xmlFile.']');
   }



   function testDrive($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testDrive with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->DRIVE)) {
         return;
      }

      $a_driveXML = array();
      $i = 0;
      foreach ($xml->CONTENT->DRIVES as $child) {
         if (isset($child->CAPTION)) {
            $a_driveXML["'".$i."-".$child->CAPTION."'"] = 1;
            $i++;
         }
      }

      $Computer = new Computer();
      $query = "SELECT * FROM `glpi_computerdisks`
         WHERE `computers_id`='".$items_id."' ";
      $result=$DB->query($query);

      $this->assertEquals($DB->numrows($result), count($a_driveXML) , 'Difference of Drives, created '.$DB->numrows($result).' times instead '.count($a_driveXML).' ['.$xmlFile.']');
   }


   function testController($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testController with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->CONTROLLERS)) {
         return;
      }

      // Controller to ignore
      $ignore_controllers = array();
      foreach ($xml->CONTENT->VIDEOS as $child) {
         $ignore_controllers["'".$child->NAME."'"] = 1;
      }
      foreach ($xml->CONTENT->SOUNDS as $child) {
         $ignore_controllers["'".$child->NAME."'"] = 1;
      }

      $a_controllerXML = array();
      $i = 0;
      foreach ($xml->CONTENT->CONTROLLERS as $child) {
         if ((isset($child->NAME)) AND (!isset($ignore_controllers["'".$child->NAME."'"]))) {
            $a_controllerXML["'".$i."-".$child->NAME."'"] = 1;
            $i++;
         }
      }

      $Computer = new Computer();
      $query = "SELECT * FROM `glpi_computers_devicecontrols`
         WHERE `computers_id`='".$items_id."' ";
      $result=$DB->query($query);

      $this->assertEquals($DB->numrows($result), count($a_controllerXML) , 'Difference of Controllers, created '.$DB->numrows($result).' times instead '.count($a_controllerXML).' ['.$xmlFile.']');
   }


   function testSound($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testSound with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->SOUNDS)) {
         return;
      }

      $a_soundXML = array();
      $i = 0;
      foreach ($xml->CONTENT->SOUNDS as $child) {
         if (isset($child->NAME)) {
            $a_soundXML["'".$i."-".$child->NAME."'"] = 1;
            $i++;
         }
      }

      $Computer = new Computer();
      $query = "SELECT * FROM `glpi_computers_devicesoundcards`
         WHERE `computers_id`='".$items_id."' ";
      $result=$DB->query($query);

      $this->assertEquals($DB->numrows($result), count($a_soundXML) , 'Difference of Sounds, created '.$DB->numrows($result).' times instead '.count($a_soundXML).' ['.$xmlFile.']');
   }


  function testVideo($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testVideo with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->VIDEOS)) {
         return;
      }

      $a_videoXML = array();
      $i = 0;
      foreach ($xml->CONTENT->VIDEOS as $child) {
         if (isset($child->NAME)) {
            $a_videoXML["'".$i."-".$child->NAME."'"] = 1;
            $i++;
         }
      }

      $Computer = new Computer();
      $query = "SELECT * FROM `glpi_computers_devicegraphiccards`
         WHERE `computers_id`='".$items_id."' ";
      $result=$DB->query($query);

      $this->assertEquals($DB->numrows($result), count($a_videoXML) , 'Difference of Videos, created '.$DB->numrows($result).' times instead '.count($a_videoXML).' ['.$xmlFile.']');
   }


  function testMemory($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testMemory with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->MEMORIES)) {
         return;
      }

      $a_memoryXML = array();
      $i = 0;
      foreach ($xml->CONTENT->MEMORIES as $child) {
         if (isset($child->CAPTION)) {
            $a_memoryXML["'".$i."-".$child->CAPTION."'"] = 1;
            $i++;
         }
      }

      $Computer = new Computer();
      $query = "SELECT * FROM `glpi_computers_devicememories`
         WHERE `computers_id`='".$items_id."' ";
      $result=$DB->query($query);

      $this->assertEquals($DB->numrows($result), count($a_memoryXML) , 'Difference of Memories, created '.$DB->numrows($result).' times instead '.count($a_memoryXML).' ['.$xmlFile.']');
   }



  function testNetwork($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testNetwork with no arguments...\n";
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->NETWORKS)) {
         return;
      }

      $a_networkXML = array();
      $i = 0;
      foreach ($xml->CONTENT->NETWORKS as $child) {
         if (isset($child->DESCRIPTION)) {
            $a_networkXML["'".$i."-".$child->DESCRIPTION."'"] = 1;
            $i++;
         }
      }

      $Computer = new Computer();
      $query = "SELECT * FROM `glpi_networkports`
         WHERE `items_id`='".$items_id."'
            AND `itemtype`='Computer'";
      if ($unknown == '1') {
         $query = "SELECT * FROM `glpi_networkports`
            WHERE `items_id`='".$items_id."'
               AND `itemtype`='PluginFusioninventoryUnknownDevice'";      }
      $result=$DB->query($query);

      $this->assertEquals($DB->numrows($result), count($a_networkXML) , 'Difference of Networks, created '.$DB->numrows($result).' times instead '.count($a_networkXML).' ['.$xmlFile.']');
   }



   function testSoftware($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testSoftware with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      if (!isset($xml->CONTENT->SOFTWARES)) {
         return;
      }

      $a_softwareXML = array();
      $i = 0;
      foreach ($xml->CONTENT->SOFTWARES as $child) {
         if (isset($child->NAME)) {
            $a_softwareXML["'".$i."-".$child->NAME."'"] = 1;
            $i++;
         }
      }

      $Computer = new Computer();
      $query = "SELECT * FROM `glpi_computers_softwareversions`
         WHERE `computers_id`='".$items_id."' ";
      $result=$DB->query($query);

      $this->assertEquals($DB->numrows($result), count($a_softwareXML) , 'Difference of Softwares, created '.$DB->numrows($result).' times instead '.count($a_softwareXML).' ['.$xmlFile.']');

      // Verify fields in GLPI
      foreach($xml->CONTENT->SOFTWARES as $child) {
         if (!isset($child->VERSION)) {
            $child->VERSION = '0';
         }
         // Search in GLPI if it's ok
         $query = "SELECT * FROM `glpi_computers_softwareversions`
            LEFT JOIN `glpi_softwareversions` ON `softwareversions_id`=`glpi_softwareversions`.`id`
            LEFT JOIN `glpi_softwares` ON `glpi_softwareversions`.`softwares_id` = `glpi_softwares`.`id`
            WHERE `computers_id`='".$items_id."'
               AND `glpi_softwareversions`.`name` = '".$child->VERSION."'
               AND `glpi_softwares`.`name` = '".$child->NAME."'
                  LIMIT 1";
         $result=$DB->query($query);

         $this->assertEquals($DB->numrows($result), 1 , 'Software not find in GLPI '.$DB->numrows($result).' times instead 1 ['.$xmlFile.']');

      }


   }


   function testHardware($xmlFile='', $items_id=0, $unknown=0) {
      global $DB;

      if (empty($xmlFile)) {
         echo "testHardware with no arguments...\n";
         return;
      }
      if ($unknown == '1') {
         // MANAGE SOME OF DATAS !!!!
         return;
      }

      $xml = simplexml_load_file($xmlFile,'SimpleXMLElement', LIBXML_NOCDATA);

      $Computer = new Computer();
      $Computer->getFromDB($items_id);

      foreach ($xml->CONTENT->BIOS as $child) {
         if ((isset($child->SMANUFACTURER))
               AND (!empty($child->SMANUFACTURER))) {

            $this->assertEquals($Computer->fields['manufacturers_id'], Dropdown::importExternal('Manufacturer', $child->SMANUFACTURER) , 'Difference of Hardware manufacturer, have '.$Computer->fields['manufacturers_id'].' instead '.Dropdown::importExternal('Manufacturer', $child->SMANUFACTURER).' ['.$xmlFile.']');
         } else if ((isset($child->BMANUFACTURER))
                      AND (!empty($dataSection['BMANUFACTURER']))) {

            $this->assertEquals($Computer->fields['manufacturers_id'], Dropdown::importExternal('Manufacturer', $child->BMANUFACTURER) , 'Difference of Hardware manufacturer, have '.$Computer->fields['manufacturers_id'].' instead '.Dropdown::importExternal('Manufacturer', $child->BMANUFACTURER).' ['.$xmlFile.']');
         }
         if (isset($child->SMODEL)) {
            $ComputerModel = new ComputerModel;

            $this->assertEquals($Computer->fields['computermodels_id'], $ComputerModel->import(array('name'=>$child->SMODEL)) , 'Difference of Hardware model, have '.$Computer->fields['computermodels_id'].' instead '.$ComputerModel->import(array('name'=>$child->SMODEL)).' ['.$xmlFile.']');
         }
         if (isset($child->SSN)) {
            if (!empty($child->SSN)) {
               $this->assertEquals($Computer->fields['serial'], trim($child->SSN) , 'Difference of Hardware serial number, have '.$Computer->fields['serial'].' instead '.$child->SSN.' ['.$xmlFile.']');
            }
         }
      }
  }



   


   

//
//
//   public function testComputerCreation() {
//
//   }
//
//

//   public function testComputerVolumes() {
//      global $DB;
//
//      $emulatorAgent = new emulatorAgent;
//      $emulatorAgent->server_urlpath = "/glpi078/plugins/fusioninventory/front/communication.php";
//
//      $input_xml = file_get_contents("xml/inventory_local/2.1.6/port003-2010-06-08-08-13-45.xml");
//      $input_xml = str_replace("<DEVICEID></DEVICEID>", "<DEVICEID>agenttest-2010-03-09-09-41-28</DEVICEID>", $input_xml);
//
//      // modify space of a volume
//      $input_xml = str_replace("<FREE>12779</FREE>", "<FREE>10000</FREE>", $input_xml);
//
//      // Return Inventory you want
//      $return_xml = $emulatorAgent->sendProlog($input_xml);
//      echo "========== Send local inventory ==========\n";
//      print_r($return_xml);
//
//      $ComputerDisk = new ComputerDisk();
//      $a_disk = $ComputerDisk->find();
//      $this->assertEquals(count($a_disk), 5 , 'Problem on inventory, we have not good number of disks ('.count($a_disk).' instead of 5)!');
//
//      $size = 0;
//      foreach ($a_disk as $id => $datas) {
//         if ($datas['device'] == "/dev/ad4s1g") {
//            $size = $datas['freesize'];
//         }
//      }
//      $this->assertEquals($size, 10000 , 'Problem on inventory, freesize of a disk is not good ('.$size.' instead of 10000)!');
//   }


//   public function testSendinventoryByWebservice() {
//
//
//      system('php ../../../webservices/scripts/testxmlrpc.php --method=glpi.doLogin --login_name=glpi login_password=glpi',$data);
//
//
//      $a_data = explode("\n", $output);
//      foreach($a_data as $num=>$value) {
//         if (strstr($value, "[session] => ")) {
//            $session_num = str_replace("[session] => ", $value);
//         }
//      }
//
//      system('php ../../../webservices/scripts/testxmlrpc.php method=fusioninventory.test --base64=plugins/fusioninventory/tools/phpunit/xml/inventory_local/2.1.6/port003-2010-06-08-08-13-45.xml --session='.$session_num, $data);
//      print_r($data);
//   }


}

// Call Plugins_Fusioninventory_Discovery_Newdevices::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Plugins_Fusioninventory_InventoryLocal::main') {
    Plugins_Fusioninventory_InventoryLocal::main();
}
?>
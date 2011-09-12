-- MySQL dump 10.13  Distrib 5.1.37, for apple-darwin8.11.1 (i386)
--
-- Host: localhost    Database: ccctest
-- ------------------------------------------------------
-- Server version	5.1.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actions`
--

DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ACTION` enum('PREP_FOR_PICKUP','SHIP_TO_WINERY','SHIP') DEFAULT NULL,
  `STATUS` enum('IN_PROGRESS','COMPLETED') DEFAULT NULL,
  `ORDERNUMBER` int(11) DEFAULT NULL,
  `LOCATION` enum('FULFILLMENT_CENTER','WINERY','CUSTOMER') DEFAULT NULL,
  `REQUESTDATE` datetime DEFAULT NULL,
  `SHIPDATE` datetime DEFAULT NULL,
  `INPROGRESS` enum('YES','NO') DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=338 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `additiondata`
--

DROP TABLE IF EXISTS `additiondata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `additiondata` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `additions`
--

DROP TABLE IF EXISTS `additions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `additions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SUPERFOODAMT` int(11) DEFAULT NULL,
  `DAPAMOUNT` int(11) DEFAULT NULL,
  `HTAAMOUNT` int(11) DEFAULT NULL,
  `GOAMOUNT` int(11) DEFAULT NULL,
  `WATERAMOUNT` int(11) DEFAULT NULL,
  `INNOCULATIONBRAND` tinytext,
  `INNOCULATIONAMOUNT` int(11) DEFAULT NULL,
  `BRIX` tinyint(4) DEFAULT NULL,
  `LABTEST` text,
  `BLEEDAMOUNT` int(11) DEFAULT NULL,
  `DAYCOUNT` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=671 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `appellations`
--

DROP TABLE IF EXISTS `appellations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appellations` (
  `NAME` tinytext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `TYPEID` int(11) DEFAULT NULL,
  `NAME` tinytext,
  `DESCRIPTION` mediumtext,
  `HIDDEN` enum('YES','NO') DEFAULT NULL,
  `CAPACITY` int(11) DEFAULT NULL,
  `OWNER` tinytext,
  `LOCATION` tinytext,
  `CYLINDERDIAMETER` float(5,2) NOT NULL DEFAULT '0.00',
  `CYLINDERHEIGHT` float(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=530 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assettypes`
--

DROP TABLE IF EXISTS `assettypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assettypes` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `NAME` tinytext,
  `CAPACITY` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `barrelhistory`
--

DROP TABLE IF EXISTS `barrelhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barrelhistory` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WOID` int(11) DEFAULT NULL,
  `PRESSFRACTION` float(9,3) DEFAULT NULL,
  `BARRELNUMBER` int(11) DEFAULT NULL,
  `DIRECTION` enum('IN','OUT') DEFAULT 'IN',
  `STATUS` enum('GOOD','BAD') DEFAULT 'GOOD',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=615 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `barrels`
--

DROP TABLE IF EXISTS `barrels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barrels` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NUMBER` tinytext,
  `DESCRIPTION` text,
  `CAPACITY` int(11) DEFAULT NULL,
  `CLIENTCODE` tinytext,
  `FOREST` tinytext,
  `YEARNEW` int(11) DEFAULT NULL,
  `INITIALVARIETAL` tinytext,
  `INITIALUSEDCOUNT` tinyint(4) DEFAULT NULL,
  `VINEYARD` tinytext,
  `TOAST` tinytext,
  `OTHER` tinytext,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NUMBER` (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=477 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bindetail`
--

DROP TABLE IF EXISTS `bindetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bindetail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `BINCOUNT` tinyint(4) DEFAULT NULL,
  `WEIGHT` int(11) DEFAULT NULL,
  `TARE` int(11) DEFAULT NULL,
  `MISC` tinytext,
  `WEIGHTAG` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=8961 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blend`
--

DROP TABLE IF EXISTS `blend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blend` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WOID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1090 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blenditems`
--

DROP TABLE IF EXISTS `blenditems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blenditems` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `SOURCELOT` tinytext,
  `GALLONS` float(9,3) DEFAULT NULL,
  `DIRECTION` enum('IN FROM','OUT TO') DEFAULT NULL,
  `BLENDID` int(11) DEFAULT NULL,
  `COMMENT` text,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1902 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bol`
--

DROP TABLE IF EXISTS `bol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bol` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `DIRECTION` enum('IN','OUT') DEFAULT NULL,
  `BONDED` enum('BONDTOBOND','TAXPAID') DEFAULT NULL,
  `BOND` tinytext,
  `NAME` text,
  `ADDRESS1` text,
  `ADDRESS2` text,
  `CITY` text,
  `STATE` tinytext,
  `ZIP` tinytext,
  `PHONE` tinytext,
  `DATE` date DEFAULT NULL,
  `CARRIER` text,
  `CLIENTCODE` tinytext,
  `FACILITYID` int(11) NOT NULL DEFAULT '0',
  `COST` float(9,2) NOT NULL DEFAULT '0.00',
  `CREATIONDATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=822 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bolitembreakout`
--

DROP TABLE IF EXISTS `bolitembreakout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolitembreakout` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `BOLITEMSID` int(11) DEFAULT NULL,
  `VARIETAL` tinytext,
  `APPELLATION` tinytext,
  `VINEYARD` tinytext,
  `PERCENTAGE` float(4,0) DEFAULT NULL,
  `VINTAGE` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=421 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bolitems`
--

DROP TABLE IF EXISTS `bolitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolitems` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TYPE` enum('WINE','JUICE','GRAPES','BOTTLED') DEFAULT 'WINE',
  `ALC` enum('<14%','>=14%') DEFAULT NULL,
  `GALLONS` float(9,3) DEFAULT NULL,
  `BOLID` int(11) DEFAULT NULL,
  `LOT` tinytext,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1391 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bottling`
--

DROP TABLE IF EXISTS `bottling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bottling` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `WOID` int(11) unsigned zerofill DEFAULT NULL,
  `LABELAPPROVAL` tinytext,
  `ESTCASECOUNT` int(11) DEFAULT NULL,
  `FINALCASECOUNT` int(11) DEFAULT NULL,
  `GALLONSPERCASE` float(11,4) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=296 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brixtemp`
--

DROP TABLE IF EXISTS `brixtemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brixtemp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lot` varchar(20) DEFAULT NULL,
  `vessel` varchar(20) DEFAULT NULL,
  `vesseltype` enum('TANK','TBIN','BBL','PORTA') DEFAULT NULL,
  `BRIX` float(11,1) DEFAULT NULL,
  `temp` int(11) DEFAULT NULL,
  `DATE` date DEFAULT NULL,
  `woid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `newindex` (`lot`,`vessel`,`vesseltype`,`BRIX`,`temp`,`DATE`)
) ENGINE=MyISAM AUTO_INCREMENT=11273 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `buffertitration`
--

DROP TABLE IF EXISTS `buffertitration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buffertitration` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `INITIALPH` float(9,3) DEFAULT NULL,
  `PH1` float(9,3) DEFAULT NULL,
  `PH2` float(9,3) DEFAULT NULL,
  `PH3` float(9,3) DEFAULT NULL,
  `PH4` float(9,3) DEFAULT NULL,
  `PH5` float(9,3) DEFAULT NULL,
  `PH6` float(9,3) DEFAULT NULL,
  `PH7` float(9,3) DEFAULT NULL,
  `NAOH1` float(9,3) DEFAULT NULL,
  `NAOH2` float(9,3) DEFAULT NULL,
  `NAOH3` float(9,3) DEFAULT NULL,
  `NAOH4` float(9,3) DEFAULT NULL,
  `NAOH5` float(9,3) DEFAULT NULL,
  `NAOH6` float(9,3) DEFAULT NULL,
  `NAOH7` float(9,3) DEFAULT NULL,
  `LABRESULTSID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `id` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `clientid` int(11) NOT NULL AUTO_INCREMENT,
  `CLIENTNAME` tinytext,
  `CODE` char(3) DEFAULT NULL,
  `GROUP` tinytext,
  `ADDRESS1` tinytext,
  `ADDRESS2` tinytext,
  `CITY` tinytext,
  `STATE` tinytext,
  `ZIP` tinytext,
  `PHONE` tinytext,
  `BOND` tinytext,
  `AP` enum('YES','NO') DEFAULT 'NO',
  `ACTIVE` enum('YES','NO') DEFAULT 'YES',
  PRIMARY KEY (`clientid`),
  UNIQUE KEY `clientid` (`clientid`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `companyInfo`
--

DROP TABLE IF EXISTS `companyInfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companyInfo` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` tinytext NOT NULL,
  `address1` tinytext NOT NULL,
  `address2` tinytext NOT NULL,
  `city` tinytext NOT NULL,
  `state` tinytext NOT NULL,
  `zip` tinytext NOT NULL,
  `lat` tinytext NOT NULL,
  `long` tinytext NOT NULL,
  `logoURL` tinytext NOT NULL,
  `favicon` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devtokens`
--

DROP TABLE IF EXISTS `devtokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devtokens` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DEVTOKEN` tinytext NOT NULL,
  `UDID` tinytext NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fermprot`
--

DROP TABLE IF EXISTS `fermprot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fermprot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `STIR` enum('YES','NO','ACTIVE') DEFAULT 'NO',
  `LOT` tinytext,
  `DATE` date DEFAULT NULL,
  `PO` enum('Yes','No') DEFAULT 'No',
  `PD` enum('YES','NO') DEFAULT 'NO',
  `PODURATION` tinyint(4) DEFAULT NULL,
  `POSTARTBRIX` tinyint(4) DEFAULT NULL,
  `POENDBRIX` tinyint(4) DEFAULT NULL,
  `PDFREQ` tinyint(4) DEFAULT NULL,
  `PDSTARTBRIX` tinyint(4) DEFAULT NULL,
  `PDSTRENGTH` enum('LIGHT','MEDIUM','HEAVY') DEFAULT NULL,
  `STATUS` enum('ACTIVE','CLOSED') DEFAULT NULL,
  `PDENDBRIX` tinyint(4) DEFAULT NULL,
  `PDSTARTBRIX2` tinyint(4) DEFAULT NULL,
  `PDENDBRIX2` tinyint(4) DEFAULT NULL,
  `PDFREQ2` tinyint(4) DEFAULT NULL,
  `PDSTRENGTH2` enum('LIGHT','MEDIUM','HEAVY') DEFAULT NULL,
  `CLIENTCODE` char(3) DEFAULT NULL,
  `POFREQ` tinyint(4) DEFAULT NULL,
  `PD2` enum('Yes','No') DEFAULT NULL,
  `VESSELTYPE` enum('TANK','TBIN','BBL','PORTA') DEFAULT NULL,
  `VESSELID` tinytext,
  `ADDITIONID` int(11) DEFAULT NULL,
  `ADDITIONDATE` date DEFAULT NULL,
  `TIMESLOT1` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `TIMESLOT2` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `COMMENT` longtext,
  `PO2` enum('YES','NO') DEFAULT NULL,
  `PODURATION2` tinyint(4) DEFAULT NULL,
  `POSTARTBRIX2` tinyint(4) DEFAULT NULL,
  `POENDBRIX2` tinyint(4) DEFAULT NULL,
  `POFREQ2` tinyint(4) DEFAULT NULL,
  `POTIMESLOT2` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `TIMESLOT3` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `TIMESLOT4` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `PDAM` enum('NONE','LIGHT','MEDIUM','HEAVY') DEFAULT NULL,
  `PDNOON` enum('NONE','LIGHT','MEDIUM','HEAVY') DEFAULT NULL,
  `PDPM` enum('NONE','LIGHT','MEDIUM','HEAVY') DEFAULT NULL,
  `POAM` tinyint(4) DEFAULT NULL,
  `PONOON` tinyint(4) DEFAULT NULL,
  `POPM` tinyint(4) DEFAULT NULL,
  `DRYICE` enum('YES','NO') NOT NULL DEFAULT 'YES',
  `STATTEMP` float(4,1) NOT NULL DEFAULT '0.0',
  `BRIXTEMP` enum('YES','NO') NOT NULL DEFAULT 'YES',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1571 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TYPEID` enum('WO','WT','BOL') DEFAULT NULL,
  `THEID` int(11) DEFAULT NULL,
  `LOCATION` text,
  `NAME` text,
  `SIZE` int(11) DEFAULT NULL,
  `FILETYPE` text,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `filllevels`
--

DROP TABLE IF EXISTS `filllevels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filllevels` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `BOTTLINGID` int(11) DEFAULT NULL,
  `TIME` datetime DEFAULT NULL,
  `AMOUNT` int(11) DEFAULT NULL,
  `CORRECTION` enum('YES','NO') DEFAULT NULL,
  `CORRECTIONTIME` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flags`
--

DROP TABLE IF EXISTS `flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flags` (
  `ID` tinyint(4) NOT NULL AUTO_INCREMENT,
  `RULE` enum('GLUFRU LIMIT','MALIC ACID LIMIT','SULPHUR TEST FREQUENCY','GLUFRU TEST FREQUENCY','TOPPING FREQUENCY','NEVER TOPPED') DEFAULT NULL,
  `VALUE` float(9,3) DEFAULT NULL,
  `CLIENTID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fpaddmap`
--

DROP TABLE IF EXISTS `fpaddmap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fpaddmap` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DATE` date DEFAULT NULL,
  `FERMPROTID` int(11) DEFAULT NULL,
  `ADDITIONID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=665 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `CLIENTID` int(4) DEFAULT NULL,
  `GROUPID` tinyint(4) DEFAULT NULL,
  `ID` tinyint(4) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=117 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labresults`
--

DROP TABLE IF EXISTS `labresults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labresults` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `LABTEST` enum('GLUFRU','PILLTEST','ALCOHOL','pH','TA','Brix','GLU','FRU','Ammonia','AMINO_NITROGEN','Potassium','MALIC_ACID','FSO2','TSO2','VA','BC','JUICE PANEL - BASIC','JUICE PANEL - FULL','BUFFER_TITRATION','BUFFER_CAPACITY','tartaric','ALPHA_AMINO_COMPOUNDS','Glu/Fru','L-Malic','Potassium','Buffer Capacity','YAN','Ethanol','LACTO','PEDIO','ACETO','BRETT','ZYGO','JPBRIX','JPGLUFRU','JPPH','JPTA','JPTARTARIC','JPMALIC','JPPOT','JPALPHA','JPAMMONIA','JPYEAST','JPBUFFER','4EP','4EG') DEFAULT NULL,
  `VALUE1` float(9,3) DEFAULT NULL,
  `VALUE2` float(9,3) DEFAULT NULL,
  `LABTESTID` int(11) DEFAULT NULL,
  `COMMENT` text,
  `UNITS1` tinytext,
  `UNITS2` tinytext,
  `WOID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=4372 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labtest`
--

DROP TABLE IF EXISTS `labtest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labtest` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `WOID` int(11) DEFAULT NULL,
  `LAB` enum('CCC','VINQUIRY','ETS') DEFAULT NULL,
  `LABTESTNUMBER` tinytext NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2034 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labtestcosts`
--

DROP TABLE IF EXISTS `labtestcosts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labtestcosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientid` int(11) NOT NULL DEFAULT '0',
  `labtest` tinytext NOT NULL,
  `cost` float(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` tinytext NOT NULL,
  `BONDNUMBER` tinytext NOT NULL,
  `ADDRESS1` tinytext NOT NULL,
  `ADDRESS2` tinytext NOT NULL,
  `CITY` tinytext NOT NULL,
  `STATE` tinytext NOT NULL,
  `ZIP` tinytext NOT NULL,
  `LOCATIONTYPE` enum('FACILITY','VINEYARD') NOT NULL DEFAULT 'FACILITY',
  `LAT` double NOT NULL DEFAULT '0',
  `LONG` double NOT NULL DEFAULT '0',
  `CLIENTID` int(11) NOT NULL DEFAULT '0',
  `ORGANIC` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `BIODYNAMIC` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `GATECODE` tinytext NOT NULL,
  `APPELLATION` tinytext NOT NULL,
  `REGION` tinytext NOT NULL,
  `APPLEMOTH` tinytext NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=912 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lotfavorites`
--

DROP TABLE IF EXISTS `lotfavorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lotfavorites` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `LOTNUMBER` tinytext NOT NULL,
  `CLIENTID` int(11) NOT NULL DEFAULT '0',
  `FAVORITE` enum('YES','NO') NOT NULL DEFAULT 'YES',
  KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=318 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lotlist`
--

DROP TABLE IF EXISTS `lotlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lotlist` (
  `woid` int(11) NOT NULL DEFAULT '0',
  `lotid` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lots`
--

DROP TABLE IF EXISTS `lots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lots` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `LOTNUMBER` tinytext,
  `DESCRIPTION` tinytext,
  `YEAR` int(4) DEFAULT NULL,
  `CLIENTCODE` int(11) DEFAULT NULL,
  `THEORDER` int(11) NOT NULL DEFAULT '0',
  `FAVORITE` enum('YES','NO') NOT NULL DEFAULT 'YES',
  `ORGANIC` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `ACTIVELOT` enum('YES','NO') NOT NULL DEFAULT 'YES',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`),
  KEY `LOTNUMBER` (`LOTNUMBER`(20))
) ENGINE=MyISAM AUTO_INCREMENT=3159 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `MESSAGE` text,
  `ENABLED` enum('TRUE','FALSE') DEFAULT 'FALSE',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newwos`
--

DROP TABLE IF EXISTS `newwos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newwos` (
  `DEVTOKEN` tinytext NOT NULL,
  `WOID` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ORDERNUMBER` int(11) DEFAULT NULL,
  `DATEENTERED` datetime DEFAULT NULL,
  `COMMENT` longtext,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FIRSTNAME` tinytext,
  `ORDERNUMBER` int(11) DEFAULT NULL,
  `ORDERDATE` datetime DEFAULT NULL,
  `CUSTOMERID` int(11) DEFAULT NULL,
  `LASTNAME` tinytext,
  `EMAIL` tinytext,
  `COMPANY` tinytext,
  `ADDRESS1` tinytext,
  `ADDRESS2` tinytext,
  `CITY` tinytext,
  `STATE` tinytext,
  `ZIP` tinytext,
  `SHIP_FIRSTNAME` tinytext,
  `SHIP_LASTNAME` tinytext,
  `SHIP_ADDRESS1` tinytext,
  `SHIP_ADDRESS2` tinytext,
  `SHIP_CITY` tinytext,
  `SHIP_STATE` tinytext,
  `SHIP_ZIP` tinytext,
  `SHIP_PHONE` tinytext,
  `SKU` tinytext,
  `SKUQTY` int(11) DEFAULT NULL,
  `SKUPRICE` float(9,3) DEFAULT NULL,
  `SHIPMETHOD` tinytext,
  `SHIPCLASS` tinytext,
  `SHIPDATE` datetime DEFAULT NULL,
  `ORDERCOMMENT` longtext,
  `USERNAME` tinytext,
  `PASSWORD` tinytext,
  `SHIP_COMPANY` tinytext,
  `SHIPPING_COST` float(9,3) DEFAULT NULL,
  `TOTAL_ORDER_AMOUNT` float(9,3) DEFAULT NULL,
  `ORDERSTATUS` tinytext,
  `CUSTID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=15260 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ACTIONID` int(11) DEFAULT NULL,
  `CARRIER` enum('FEDEX','UPS','GSO','DHL') DEFAULT NULL,
  `METHOD` enum('GROUND','3DAY','2DAY','OVERNIGHT','PRIORITYOVERNIGHT') DEFAULT NULL,
  `TRACKINGNUMBER` tinytext,
  `COMMENT` longtext,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pressprogram`
--

DROP TABLE IF EXISTS `pressprogram`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pressprogram` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PROGRAM` tinytext,
  `WOID` int(11) DEFAULT NULL,
  `PRESSTYPE` enum('PRESS_TO_BBL','PRESS_TO_TANK') DEFAULT 'PRESS_TO_BBL',
  `FILLLEVEL` int(11) DEFAULT NULL,
  `SETTLINGTIME` int(11) DEFAULT NULL,
  `PRESSDURATION` int(11) DEFAULT NULL,
  `PRESSCUT` float(9,3) DEFAULT NULL,
  `DESCRIPTION` text,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2936 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program`
--

DROP TABLE IF EXISTS `program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DAY` int(4) DEFAULT NULL,
  `LOWBRIX` int(11) DEFAULT NULL,
  `HIGHBRIX` int(11) DEFAULT NULL,
  `WOTEMPLATEID` int(11) DEFAULT NULL,
  `REPEATING` enum('YES','NO') DEFAULT 'NO',
  `FERMPROTID` int(11) DEFAULT NULL,
  `FREQUENCY` enum('1','2','3') DEFAULT NULL,
  `DAYCOUNT` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ASSETID` int(11) DEFAULT NULL,
  `DATEALLOCATED` date DEFAULT NULL,
  `TIMESLOT` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `CUSTID` int(11) DEFAULT NULL,
  `FORLOT` tinytext,
  `STATUS` enum('GRANTED','REQUESTED') DEFAULT NULL,
  `WOID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=8303 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scp`
--

DROP TABLE IF EXISTS `scp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scp` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `HANDSORTING` enum('YES','NO','DIRECTTOPRESS') DEFAULT NULL,
  `SPECIALINSTRUCTIONS` text,
  `WHOLECLUSTER` int(11) DEFAULT NULL,
  `TANKPOSITION` enum('TOP','BOTTOM') DEFAULT 'TOP',
  `CRUSHING` enum('NOCRUSHING','PARTIAL','COMPLETE') DEFAULT NULL,
  `ESTTONS` float(11,3) DEFAULT NULL,
  `WOID` int(11) DEFAULT NULL,
  `ACTUALTONS` float(9,3) DEFAULT NULL,
  `ZONE` enum('1 - MENDOCINO','2 - LAKE','3 - SONOMA','4 - NAPA','7 - MONTEREY','8 - SANTA BARBARA','10 - EL DORADO','0 - OUT OF STATE') DEFAULT NULL,
  `VARIETAL` tinytext,
  `DELIVERYDATE` date DEFAULT NULL,
  `APC` tinytext,
  `NOTES` longtext,
  `VINEYARD` tinytext,
  `APPELLATION` tinytext,
  `CLONE` tinytext,
  `PROPOSEDLOT` tinytext,
  `ESTDAYSINTANK` tinyint(4) DEFAULT '12',
  `VINEYARDID` int(11) NOT NULL DEFAULT '0',
  `COLORCODE` tinytext NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2589 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skudata`
--

DROP TABLE IF EXISTS `skudata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skudata` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SKU` tinytext,
  `DESCRIPTION` tinytext,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveyanswers`
--

DROP TABLE IF EXISTS `surveyanswers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveyanswers` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `USERID` tinytext,
  `QUESTIONID` int(11) DEFAULT NULL,
  `YESNOANSWER` enum('YES','NO') DEFAULT NULL,
  `SCALEANSWER` tinyint(4) DEFAULT NULL,
  `COMMENT` text,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveyquestions`
--

DROP TABLE IF EXISTS `surveyquestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveyquestions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `QUESTION` text,
  `QUESTIONTYPE` enum('YESNO','SCALE1TO5','COMMENTONLY') DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinytext NOT NULL,
  `startdate` date NOT NULL DEFAULT '0000-00-00',
  `enddate` date NOT NULL DEFAULT '0000-00-00',
  `description` longtext NOT NULL,
  `workperformedby` enum('CCC','CLIENT') NOT NULL DEFAULT 'CCC',
  `clientid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usermessagemap`
--

DROP TABLE IF EXISTS `usermessagemap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usermessagemap` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `USERID` int(11) DEFAULT NULL,
  `MESSAGEID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `clientid` int(11) DEFAULT NULL,
  `username` tinytext,
  `staff` enum('YES','NO') DEFAULT NULL,
  `group` tinyint(4) DEFAULT NULL,
  `deviceid` tinytext NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `validlabtests`
--

DROP TABLE IF EXISTS `validlabtests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `validlabtests` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `LABTEST` tinytext NOT NULL,
  `UNITS` tinytext NOT NULL,
  `DECIMALPLACES` tinyint(4) NOT NULL DEFAULT '0',
  `MIN` float NOT NULL DEFAULT '0',
  `MAX` float NOT NULL DEFAULT '0',
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `varietals`
--

DROP TABLE IF EXISTS `varietals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `varietals` (
  `NAME` tinytext,
  `ABBREVIATION` tinytext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vessels`
--

DROP TABLE IF EXISTS `vessels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vessels` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `vesseltype` enum('TANK','TBIN','BARREL-60') DEFAULT NULL,
  `vesselid` int(10) unsigned DEFAULT NULL,
  `description` text,
  `volume` float DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wo`
--

DROP TABLE IF EXISTS `wo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wo` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TYPE` enum('OTHER','PUMP OVER','PUNCH DOWN','PRESSOFF','ADDITION','SCP','BLENDING','BOTTLING','FILTRATION','RACKING','SETTLING','BLEEDOFF','TOPPING','DRYICE','LAB TEST','BOL','VINEYARD','PULL SAMPLE','BBL DOWN','HEAT TANK','DUMP','STIR','BRIXTEMP') DEFAULT NULL,
  `DUEDATE` datetime DEFAULT NULL,
  `LOT` tinytext,
  `VESSELTYPE` enum('TANK','TBIN','BBL','PORTA') DEFAULT NULL,
  `VESSELID` tinytext,
  `TIMESLOT` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `DURATION` tinyint(4) DEFAULT NULL,
  `STRENGTH` enum('LIGHT','MEDIUM','HEAVY') DEFAULT NULL,
  `STATUS` enum('PENDING','ASSIGNED','WAITING ON CUSTOMER','HOLD','COMPLETED','TEMPLATE') DEFAULT 'ASSIGNED',
  `COMPLETIONDATE` date DEFAULT NULL,
  `COMPLETEBY` tinytext,
  `ASSIGNEDTO` tinytext,
  `DELETED` tinyint(1) DEFAULT '0',
  `ADDITIONID` int(11) DEFAULT NULL,
  `RELATEDADDITIONSID` int(11) DEFAULT NULL,
  `AUTOGENERATED` enum('YES','NO') DEFAULT NULL,
  `CLIENTCODE` tinytext,
  `OTHERDESC` text,
  `WORKAREAID` int(11) DEFAULT NULL,
  `CREATIONDATE` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ASSETSID` int(11) DEFAULT NULL,
  `ALERT` tinytext,
  `MORNING` enum('YES','NO') DEFAULT NULL,
  `NOON` enum('YES','NO') DEFAULT NULL,
  `EVENING` enum('YES','NO') DEFAULT NULL,
  `WORKPERFORMEDBY` enum('CCC','CLIENT') DEFAULT 'CCC',
  `REQUESTOR` tinytext,
  `COMPLETEDDESCRIPTION` text,
  `ENDINGTANKGALLONS` float(9,3) DEFAULT NULL,
  `ENDINGBARRELCOUNT` int(11) DEFAULT NULL,
  `ENDINGTOPPINGGALLONS` int(11) DEFAULT NULL,
  `LABTESTID` int(11) DEFAULT NULL,
  `ENDDATE` datetime DEFAULT NULL,
  `STARTSLOT` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `ENDSLOT` enum('MORNING','NOON','EVENING') DEFAULT NULL,
  `AUTOTAG` int(11) DEFAULT NULL,
  `FERMPROTID` int(11) NOT NULL DEFAULT '0',
  `BRIX` tinyint(4) DEFAULT NULL,
  `LASTMODIFIEDDATETIME` timestamp NULL DEFAULT NULL,
  `DRYICE` enum('YES','NO') DEFAULT NULL,
  `STATTEMP` float(4,1) NOT NULL DEFAULT '0.0',
  `BRIXTEMP` enum('YES','NO') NOT NULL DEFAULT 'YES',
  `TASKID` int(11) NOT NULL DEFAULT '0',
  `TOPPINGLOT` tinytext NOT NULL,
  `SO2ADD` int(11) NOT NULL DEFAULT '0',
  `COST` float(8,2) NOT NULL DEFAULT '0.00',
  `SAMPLEVOLUME` tinytext NOT NULL,
  `SAMPLEQTY` int(11) NOT NULL DEFAULT '1',
  `RELATEDLABTESTID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `id` (`ID`),
  KEY `statusdeleteindex` (`STATUS`,`TYPE`)
) ENGINE=MyISAM AUTO_INCREMENT=53115 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wt`
--

DROP TABLE IF EXISTS `wt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wt` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TAGID` int(11) DEFAULT NULL,
  `VARIETY` tinytext,
  `VINEYARD` tinytext,
  `APPELLATION` tinytext,
  `TRUCKLICENSE` tinytext,
  `TRAILERLICENSE` tinytext,
  `REGIONCODE` tinyint(4) DEFAULT NULL,
  `DATETIME` datetime DEFAULT NULL,
  `CLIENTCODE` int(11) DEFAULT NULL,
  `LOT` tinytext,
  `CLONE` tinytext,
  `VOID` enum('YES','NO') DEFAULT 'NO',
  `VINEYARDID` int(11) NOT NULL DEFAULT '0',
  `COST` float(8,2) NOT NULL DEFAULT '0.00',
  `CREATIONDATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`),
  UNIQUE KEY `TAGID` (`TAGID`)
) ENGINE=MyISAM AUTO_INCREMENT=2490 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zones`
--

DROP TABLE IF EXISTS `zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zones` (
  `NUMBER` tinyint(4) DEFAULT NULL,
  `NAME` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-06-15 18:11:45

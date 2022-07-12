CREATE TABLE IF NOT EXISTS `drugs` (
  `DR_id` int(11) NOT NULL AUTO_INCREMENT,
  `DR_name` varchar(64) NOT NULL,
  `DR_min` int(11) NOT NULL,
  `DR_max` int(11) NOT NULL,
  PRIMARY KEY (`DR_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `drugPrices` (
  `DRP_location` int(11) NOT NULL,
  `DRP_drug` int(11) NOT NULL,
  `DRP_cost` int(11) NOT NULL,
  PRIMARY KEY (`DRP_location`, `DRP_drug`)
);

CREATE TABLE IF NOT EXISTS `userDrugs` (
  `UDR_user` int(11) NOT NULL,
  `UDR_drug` int(11) NOT NULL,
  `UDR_qty` varchar(64) NOT NULL,
  `UDR_cost` int(11) NOT NULL, 
  PRIMARY KEY(`UDR_user`,`UDR_drug`)
) DEFAULT CHARSET=utf8;
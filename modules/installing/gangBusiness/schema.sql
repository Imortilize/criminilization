CREATE TABLE IF NOT EXISTS `businesses` (
  `BU_id` int(11) NOT NULL AUTO_INCREMENT,
  `BU_name` varchar(255) NOT NULL,
  `BU_rank` int(11) NOT NULL,
  `BU_payout` int(11) NOT NULL,
  `BU_payoutTime` int(11) NOT NULL,
  `BU_cost` int(11) NOT NULL,
  PRIMARY KEY (`BU_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `userBusinesses` (
  `UBU_id` int(11) NOT NULL AUTO_INCREMENT,
  `UBU_user` int(11) NOT NULL,
  `UBU_business` int(255) NOT NULL,
  `UBU_lastPayout` int(11) NOT NULL,
  PRIMARY KEY (`UBU_id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `businesses` VALUES 
(1,'Crack Den',1,1500,3600,25000),
(2,'Meth Lab',3,5000,7200,100000),
(3,'Whore House',5,7500,14400,210000),
(4,'Night Club',10,15000,43200,450000),
(5,'Import Cocaine',15,25000,86400,750000);
CREATE TABLE IF NOT EXISTS `investments` (
  `IN_id` int(11) NOT NULL AUTO_INCREMENT,
  `IN_name` varchar(64) NOT NULL,
  `IN_min` int(11) NOT NULL,
  `IN_max` int(11) NOT NULL,
  `IN_maxInvest` int(11) NOT NULL,
  `IN_time` int(11) NOT NULL,
  PRIMARY KEY(`IN_id`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `userStats` ADD COLUMN `US_invest` INT(11) NOT NULL DEFAULT 0; 
ALTER TABLE `userStats` ADD COLUMN `US_investment` INT(11) NOT NULL DEFAULT 0; 
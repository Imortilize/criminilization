CREATE TABLE IF NOT EXISTS `gangCrimes` (
  `GC_id` int(11) NOT NULL AUTO_INCREMENT,
  `GC_name` varchar(120) NULL,
  `GC_cooldown` int(11) NOT NULL DEFAULT 0,
  `GC_chance` int(11) NOT NULL DEFAULT 0,
  `GC_money` int(11) NOT NULL DEFAULT 0,
  `GC_maxMoney` int(11) NOT NULL DEFAULT 0,
  `GC_bullets` int(11) NOT NULL DEFAULT 0,
  `GC_maxBullets` int(11) NOT NULL DEFAULT 0,
  `GC_level` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`GC_id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `gangCrimes` (`GC_id`, `GC_name`, `GC_cooldown`, `GC_chance`, `GC_money`, `GC_maxMoney`, `GC_level`) VALUES
(1, 'Collect drug money', 600, 66, 150, 500, 1),
(2, 'Collect protection money', 900, 50, 250, 750, 1);

ALTER TABLE `userStats` ADD COLUMN `US_gangCash` INT(11) NOT NULL DEFAULT 0;
ALTER TABLE `userStats` ADD COLUMN `US_gangBullets` INT(11) NOT NULL DEFAULT 0;
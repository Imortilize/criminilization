CREATE TABLE IF NOT EXISTS `awards` (
  `AW_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `AW_img` varchar(120) NOT NULL DEFAULT 'default.png',
  `AW_name` varchar(120) NOT NULL,
  `AW_desc` text NOT NULL,
  `AW_type` varchar(120) NOT NULL,
  `AW_required` int(11) NOT NULL,
  `AW_money` int(11) NOT NULL,
  `AW_bullets` int(11) NOT NULL,
  `AW_points` int(11) NOT NULL,
  `AW_hidden` int(1) NOT NULL,
  PRIMARY KEY (`AW_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `userAwards` (
  `UA_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `UA_user` int(11) NOT NULL,
  `UA_award` int(11) NOT NULL,
  `UA_time` int(11) NOT NULL,
  PRIMARY KEY (`UA_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `awards` (`AW_id`, `AW_img`, `AW_name`, `AW_desc`, `AW_type`, `AW_required`, `AW_money`, `AW_bullets`, `AW_points`, `AW_hidden`) VALUES
(1, '1.png', 'Master Criminal', 'Successfully complete 100 crimes.', 'Crime Success', 100, 0, 0, 0, 0),
(2, '2.png', 'Welcome To The Family', 'Successfully joined a gang.', 'Gang', 1, 0, 0, 0, 0),
(3, '3.png', 'Prison Break', 'Bust 100 people out of jail', 'Bust Success', 100, 0, 0, 0, 0),
(4, '4.png', 'Mile High Club', 'Travel over 100 times ', 'Travel Total', 100, 0, 0, 0, 1);


ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_crimesdone INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_crimesfail INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_crimesuccess INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_theftdone INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_theftfail INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_theftsuccess INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_chasedone INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_chasefail INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_chasesuccess INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_bustsdone INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_bustsfail INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_bustsuccess INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `userStats` ADD COLUMN IF NOT EXISTS US_traveltotal INT(11) NOT NULL DEFAULT '0';
CREATE TABLE IF NOT EXISTS `skills` (
  `SK_id` int(11) NOT NULL AUTO_INCREMENT,
  `SK_name` varchar(255) NULL,
  `SK_default` int(11) NOT NULL DEFAULT 0,
  `SK_max` int(11) NOT NULL DEFAULT 0,
  `SK_canUpdate` VARCHAR(1) NOT NULL DEFAULT 'y',
	`SK_isHidden` VARCHAR(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`SK_id`)
) DEFAULT CHARSET=utf8;

INSERT INTO `skills` (`SK_id`, `SK_name`, `SK_default`, `SK_max`, `SK_canUpdate`, `SK_isHidden`) VALUES
(1, 'Skill Points Available', 10, 100, 'n', 'y');

INSERT INTO `skills` (`SK_id`, `SK_name`, `SK_default`, `SK_max`, `SK_canUpdate`, `SK_isHidden`) VALUES
('Luck', 10, 10, 'y', 'n');

ALTER TABLE `userStats` ADD `US_SkillPointsAvailable` INT(11) NOT NULL DEFAULT '10';

ALTER TABLE `userStats` ADD `US_maxSkillPointsAvailable` INT(11) NOT NULL DEFAULT '100';

ALTER TABLE `userStats` ADD `US_luck` INT(11) NOT NULL DEFAULT '10';

ALTER TABLE `userStats` ADD `US_maxLuck` INT(11) NOT NULL DEFAULT '1';

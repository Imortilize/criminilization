ALTER TABLE `locations` ADD COLUMN `L_safe` INT(11) DEFAULT -1;
ALTER TABLE `locations` ADD COLUMN `L_safeOpened` INT(11) DEFAULT -1;
ALTER TABLE `userStats` ADD COLUMN `US_safeGuesses` TEXT;
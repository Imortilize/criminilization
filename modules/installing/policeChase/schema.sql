ALTER TABLE `cars` ADD COLUMN `CA_pcSuccess` INT(11) NOT NULL DEFAULT 25;
ALTER TABLE `cars` ADD COLUMN `CA_pcContinue` INT(11) NOT NULL DEFAULT 50;
ALTER TABLE `cars` ADD COLUMN `CA_pcFail` INT(11) NOT NULL DEFAULT 25;
ALTER TABLE `userStats` ADD COLUMN `US_pcCar` INT(11) NOT NULL DEFAULT 0;
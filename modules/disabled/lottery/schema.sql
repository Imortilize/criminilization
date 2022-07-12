CREATE TABLE IF NOT EXISTS `lottery` (
  `LO_date` int(11) NOT NULL,
  `LO_winner` int(11) NOT NULL,
  `LO_jackpot` int(11) NOT NULL,
  PRIMARY KEY (`LO_date`)
) DEFAULT CHARSET=utf8;

ALTER TABLE `userStats` ADD COLUMN `US_lotteryTickets` INT(11) NOT NULL DEFAULT 0; 
CREATE TABLE IF NOT EXISTS `dailyLoginBonuses` (
  `DL_id` int(11) NOT NULL AUTO_INCREMENT,
  `DL_days` int(11) NULL DEFAULT 0,
  `DL_rewardMoney` int(11) NULL DEFAULT 0,
  `DL_rewardBullets` int(11) NULL DEFAULT 0,
  `DL_rewardCarID` int(11) NULL DEFAULT 0,
  `DL_rewardType` varchar(25) NULL,
  `DL_rewardAmount` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`DL_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dailyLoginRewardLog` (
  `DLR_id` int(11) NOT NULL AUTO_INCREMENT,
  `DLR_userID` int(11) NOT NULL DEFAULT 0,
  `DLR_bonusID` int(11) NOT NULL DEFAULT 0,
  `DLR_collected` varchar(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`DLR_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dailyLoginLog` (
  `LL_id` int(11) NOT NULL AUTO_INCREMENT,
  `LL_userID` int(11) NOT NULL DEFAULT 0,
  `LL_lastDay` varchar(10) NULL,
  `LL_days` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`LL_id`)
) DEFAULT CHARSET=utf8;


INSERT INTO `dailyLoginBonuses` (`DL_id`, `DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(1, 1, 'cash', 10);

INSERT INTO `dailyLoginBonuses` (`DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(2, 'cash', 15);

INSERT INTO `dailyLoginBonuses` (`DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(3, 'cash', 20);

INSERT INTO `dailyLoginBonuses` (`DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(5, 'buDLet', 10);

INSERT INTO `dailyLoginBonuses` (`DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(7, 'cash', 15);

INSERT INTO `dailyLoginBonuses` (`DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(14, 'cash', 15);

INSERT INTO `dailyLoginBonuses` (`DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(21, 'car', 1);


INSERT INTO `dailyLoginBonuses` (`DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(28, 'cash', 100);

INSERT INTO `dailyLoginBonuses` (`DL_days`, `DL_rewardType`, `DL_rewardAmount`) VALUES 
(30, 'bulLet', 35);

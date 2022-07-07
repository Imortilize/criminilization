CREATE TABLE IF NOT EXISTS `stocks` (
  `ST_id` int(11) NOT NULL AUTO_INCREMENT,
  `ST_name` varchar(64) NOT NULL,
  `ST_desc` text NOT NULL,
  `ST_history` text NOT NULL,
  `ST_change` int(11) NOT NULL DEFAULT 35,
  `ST_rising` int(11) NOT NULL DEFAULT 1,
  `ST_vol` int(11) NOT NULL,
  `ST_min` int(11) NOT NULL,
  `ST_max` int(11) NOT NULL,
  PRIMARY KEY (`ST_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `userStocks` (
  `UST_user` int(11) NOT NULL,
  `UST_stock` int(11) NOT NULL,
  `UST_qty` varchar(64) NOT NULL,
  `UST_cost` int(11) NOT NULL, 
  PRIMARY KEY(`UST_user`,`UST_stock`)
) DEFAULT CHARSET=utf8;
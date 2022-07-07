CREATE TABLE IF NOT EXISTS `store` (
  `ST_id` int(11) NOT NULL AUTO_INCREMENT,
  `ST_desc` varchar(255) NOT NULL,
  `ST_tag` varchar(255) NOT NULL,
  `ST_points` int(11) NOT NULL,
  `ST_cost` int(11) NOT NULL,
  PRIMARY KEY (`ST_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `payments` (
    `PA_id` int(6) NOT NULL AUTO_INCREMENT,
    `PA_txnid` varchar(20) NOT NULL,
    `PA_payment_amount` decimal(7,2) NOT NULL,
    `PA_payment_status` varchar(25) NOT NULL,
    `PA_itemid` varchar(25) NOT NULL,
    `PA_createdtime` datetime NOT NULL,
    `PA_user` INT(11) NOT NULL,
    PRIMARY KEY (`PA_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
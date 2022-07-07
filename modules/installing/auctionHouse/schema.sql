CREATE TABLE IF NOT EXISTS `auctions` (
  `A_id` int(11) NOT NULL AUTO_INCREMENT,
  `A_user` int(11) NOT NULL,
  `A_bid` int(11) NOT NULL,
  `A_highestBidder` int(11) NOT NULL,
  `A_type` varchar(64) NOT NULL,
  `A_qty` int(11) NOT NULL,
  `A_buyNow` int(11) NOT NULL,
  `A_start` int(11) NOT NULL,
  `A_length` int(11) NOT NULL,
  `A_paidOut` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`A_id`)
) DEFAULT CHARSET=utf8;
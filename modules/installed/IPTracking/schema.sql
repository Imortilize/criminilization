CREATE TABLE IF NOT EXISTS `userIP` (
  `IP_u` int(11) NOT NULL,
  `IP_addr` varchar(120) NOT NULL,
  `IP_t` int(11) NOT NULL,
  PRIMARY KEY (`IP_u`, `IP_addr`)
) DEFAULT CHARSET=utf8;
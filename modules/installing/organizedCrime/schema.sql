CREATE TABLE IF NOT EXISTS `ocTypes` (
  `OCT_id` int(11) NOT NULL AUTO_INCREMENT,
  `OCT_name` varchar(255) NOT NULL,
  `OCT_cooldown` int(11) NOT NULL,
  `OCT_successEXP` int(11) NOT NULL,
  `OCT_failedEXP` int(11) NOT NULL,
  `OCT_minCash` int(11) NOT NULL,
  `OCT_cost` int(11) NOT NULL,
  `OCT_maxCash` int(11) NOT NULL,
  PRIMARY KEY (`OCT_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `oc` (
  `OC_id` int(11) NOT NULL AUTO_INCREMENT,
  `OC_leader` int(11) NOT NULL,
  `OC_type` int(11) NOT NULL,
  `OC_location` int(11) NOT NULL,
  `OC_time` int(11) NOT NULL,
  PRIMARY KEY (`OC_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ocUsers` (
  `OCU_id` int(11) NOT NULL AUTO_INCREMENT,
  `OCU_oc` int(11) NOT NULL,
  `OCU_user` int(11) NOT NULL,
  `OCU_role` int(11) NOT NULL,
  `OCU_status` int(11) NOT NULL,
  PRIMARY KEY (`OCU_id`)
) DEFAULT CHARSET=utf8;
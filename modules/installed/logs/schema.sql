CREATE TABLE IF NOT EXISTS `logs` (
    `L_id` int(11) NOT NULL AUTO_INCREMENT,
    `L_user` int(11) NOT NULL,
    `L_module` varchar(100) NOT NULL,
    `L_text` text NOT NULL,
    `L_date` int(11) NOT NULL,
    PRIMARY KEY(`L_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
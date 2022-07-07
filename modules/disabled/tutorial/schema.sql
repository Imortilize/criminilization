CREATE TABLE IF NOT EXISTS `tutorials` (
    `T_id` int(11) NOT NULL AUTO_INCREMENT,
    `T_module` varchar(75) NOT NULL,
    `T_text` text NOT NULL,
    PRIMARY KEY(`T_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
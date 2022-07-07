DROP TABLE `gameNews`;
CREATE TABLE `gameNews` (
    `GN_id` int(11) NOT NULL AUTO_INCREMENT,
    `GN_author` int(11) NOT NULL DEFAULT '0',
    `GN_title` varchar(120) DEFAULT NULL,
    `GN_type` varchar(50) DEFAULT 'news',
    `GN_text` text,
    `GN_date` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`GN_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

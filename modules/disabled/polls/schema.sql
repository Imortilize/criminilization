CREATE TABLE `poll` (
	`P_id` INT(11) PRIMARY KEY AUTO_INCREMENT, 
	`P_desc` VARCHAR(255), 
	`P_options` TEXT
);

CREATE TABLE `pollVotes` (
	`PV_poll` INT(11), 
	`PV_user` INT(11), 
	`PV_vote` INT(11), 
	PRIMARY KEY(`PV_poll`, `PV_user`)
);
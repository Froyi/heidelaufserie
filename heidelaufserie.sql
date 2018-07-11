CREATE DATABASE `heidelaufserie` /*!40100 COLLATE 'utf8_general_ci' */;

CREATE TABLE `runner` (
	`runnerId` VARCHAR(200) NOT NULL,
	`surname` VARCHAR(200) NOT NULL,
	`firstname` VARCHAR(200) NOT NULL,
	`birthYear` INT(4) NOT NULL,
	`gender` ENUM('w','m') NOT NULL,
	`club` VARCHAR(200) NULL DEFAULT NULL
)
ENGINE=MyISAM
;


CREATE TABLE `competition` (
	`competitionId` VARCHAR(200) NOT NULL,
	`competitionNumber` INT(1) NOT NULL,
	`date` DATE NOT NULL
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;


CREATE TABLE `competitionDay` (
	`competitionDayId` VARCHAR(200) NOT NULL,
	`title` VARCHAR(200) NOT NULL,
	`date` DATE NOT NULL,
	PRIMARY KEY (`competitionDayId`)
)
ENGINE=MyISAM
;

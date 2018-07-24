CREATE DATABASE `heidelaufserie` /*!40100 COLLATE 'utf8_general_ci' */;

CREATE TABLE `heidelaufserie`.`runner` (
	`runnerId` VARCHAR(200) NOT NULL,
	`surname` VARCHAR(200) NOT NULL,
	`firstname` VARCHAR(200) NOT NULL,
	`birthYear` INT(4) NOT NULL,
	`gender` ENUM('w','m') NOT NULL,
	PRIMARY KEY (`runnerId`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `heidelaufserie`.`competition` (
	`competitionId` VARCHAR(200) NOT NULL,
	`competitionTypeId` VARCHAR(200) NOT NULL,
	`title` VARCHAR(200) NOT NULL,
	`date` DATE NOT NULL,
  `startTime` DATETIME NOT NULL,
	PRIMARY KEY (`competitionId`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `heidelaufserie`.`competitionType` (
	`competitionTypeId` INT(2) NOT NULL,
	`competitionName` VARCHAR(200) NOT NULL,
	`distance` INT(6) NOT NULL,
	`rounds` INT(3) NOT NULL,
	`standardSet` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`competitionTypeId`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `heidelaufserie`.`competitionData` (
	`competitionDataId` VARCHAR(200) NOT NULL,
	`competitionId` VARCHAR(200) NOT NULL,
	`runnerId` VARCHAR(200) NOT NULL,
	`startNumber` INT(11) NOT NULL,
	`transponderNumber` INT(11) NOT NULL,
 	`club` VARCHAR(200) NULL DEFAULT NULL,
 	`date` DATE NOT NULL,
	PRIMARY KEY (`competitionDataId`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `heidelaufserie`.`timemeasure` (
  `timeMeasureId` VARCHAR(200) NOT NULL,
  `transponderNumber` VARCHAR(200) NOT NULL,
    `startTime` DATETIME NOT NULL,
  PRIMARY KEY (`timeMeasureId`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('1', '5km Laufen', '5000', '1', '1');
INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('2', '10km Laufen', '10000', '2', '1');
INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('3', '15km Laufen', '15000', '3', '1');
INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('4', '5km Nordic Walking', '5000', '1', '1');
INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('5', '10km Nordic Walking', '10000', '2', '1');

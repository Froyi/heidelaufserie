CREATE DATABASE `heidelaufserie` /*!40100 COLLATE 'utf8_general_ci' */;

create table competition
(
	competitionId varchar(200) not null
		primary key,
	competitionTypeId varchar(200) not null,
	title varchar(200) not null,
	date date not null,
	startTime datetime not null
)
;

create table competitiondata
(
	competitionDataId varchar(200) not null
		primary key,
	competitionId varchar(200) not null,
	runnerId varchar(200) not null,
	startNumber int not null,
	transponderNumber int not null,
	clubId varchar(200) null,
	date date not null
)
;

create table competitionresults
(
	competitionResultsId varchar(200) not null
		primary key,
	competitionDataId varchar(200) not null,
	runnerId varchar(200) not null,
	timeOverall int(5) null,
	points float null,
	firstRound int(5) null,
	secondRound int(5) null,
	thirdRound int(5) null
)
;

create table competitionstatistic
(
	competitionStatisticId varchar(200) not null
		primary key,
	runnerId varchar(200) not null,
	year int(4) not null,
	competitionCount int(2) default '0' not null,
	totalPoints float null,
	averagePoints float null,
	rankingPoints float null,
	bestTimeOverall int(5) null,
	averageTimeOverall int(5) null,
	bestFirstRound int(5) null,
	averageFirstRound int(5) null,
	bestSecondRound int(5) null,
	averageSecondRound int(5) null,
	bestThirdRound int(5) null,
	averageThirdRound int(5) null,
	ranking int(3) null,
	akRanking int(3) null
)
;

create table competitiontype
(
	competitionTypeId int(2) not null
		primary key,
	competitionName varchar(200) not null,
	distance int(6) not null,
	rounds int(3) not null,
	standardSet tinyint(1) default '0' not null,
	startTimeGroup int default '0' not null
)
;

create table runner
(
	runnerId varchar(200) not null
		primary key,
	surname varchar(200) not null,
	firstname varchar(200) not null,
	birthYear int(4) not null,
	gender enum('w', 'm') not null,
	proved tinyint(1) default '0' not null
)
;

create table timemeasure
(
	timeMeasureId varchar(200) not null
		primary key,
	transponderNumber varchar(200) not null,
	timestamp datetime not null,
	shown tinyint(1) default '0' not null
);

create table finishmeasure
(
	finishMeasureId   varchar(200) not null
		primary key,
	transponderNumber varchar(200) not null,
	timestamp         datetime     not null
)
;

create table club
(
	clubId   varchar(200) not null
		primary key,
	clubName varchar(250) null,
	prooved  tinyint(1)   null
);



INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('1', '5km Laufen', '5000', '1', '1');
INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('2', '10km Laufen', '10000', '2', '1');
INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('3', '15km Laufen', '15000', '3', '1');
INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('4', '5km Nordic Walking', '5000', '1', '1');
INSERT INTO `heidelaufserie`.`competitiontype` (`competitionTypeId`, `competitionName`, `distance`, `rounds`, `standardSet`) VALUES ('5', '10km Nordic Walking', '10000', '2', '1');


-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Авг 07 2016 г., 22:30
-- Версия сервера: 5.7.11
-- Версия PHP: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test`
--

DELIMITER $$
--
-- Процедуры
--
CREATE DEFINER=`test`@`localhost` PROCEDURE `getConvertion` (IN `period` INT)  BEGIN
SET @totalCount=NULL;
SET @firstDate=NULL;
SET @curPeriod=NULL;
SET @lastPeriod=NULL;
SET @curConv=NULL;
SET @predConv=NULL;
Select `cRegistered` INTO @firstDate from client ORDER BY `cRegistered` LIMIT 1;
BEGIN
    SET @lastPeriod=@firstDate;
    CREATE temporary TABLE if not exists `CONVERTION` (
        `number` BIGINT  PRIMARY KEY AUTO_INCREMENT,
        `dfrom` DATE,
        `dto` DATE,
        `conv` DOUBLE,
        `predconv` DOUBLE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
    WHILE (TO_DAYS(NOW())-TO_DAYS(@lastPeriod))>=0 DO
        SET @curPeriod=DATE_ADD(@lastPeriod,INTERVAL period DAY);
        Select COUNT(`cID`) INTO @totalCount from client WHERE `cRegistered` BETWEEN @lastPeriod AND @curPeriod;
        SELECT (COUNT(*)/@totalCount)*100 INTO @curConv FROM `client` WHERE `cStatus`=2 AND `cRegistered` BETWEEN @lastPeriod AND @curPeriod;
        SELECT (COUNT(*)/@totalCount)*100 INTO @predConv FROM `client` WHERE (`cStatus`=2 OR `cStatus`=3) AND `cRegistered` BETWEEN @lastPeriod AND @curPeriod;
        IF @curConv is null THEN
            set @curConv=0;
        end if;
        if @predConv is null THEN
            set @predConv=0;
        end if;
        INSERT INTO `CONVERTION` VALUES(null,@lastPeriod,@curPeriod,@curConv,@predConv);
        SET @lastPeriod=@curPeriod;
     END WHILE;
    END;
    SELECT * FROM CONVERTION;
END$$

DELIMITER ;

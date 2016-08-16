-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Авг 10 2016 г., 12:09
-- Версия сервера: 5.6.12-log
-- Версия PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `test`;

DELIMITER $$
--
-- Процедуры
--
CREATE DEFINER=`test`@`localhost` PROCEDURE `getConvertion`(IN `period` INT)
BEGIN
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

-- --------------------------------------------------------

--
-- Структура таблицы `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `cID` int(10) NOT NULL AUTO_INCREMENT,
  `cFIO` varchar(255) NOT NULL,
  `cPhone` varchar(15) NOT NULL,
  `cStatus` int(2) NOT NULL DEFAULT '1',
  `cRegistered` datetime NOT NULL,
  PRIMARY KEY (`cID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Дамп данных таблицы `client`
--

INSERT INTO `client` (`cID`, `cFIO`, `cPhone`, `cStatus`, `cRegistered`) VALUES
(1, 'Client', '+79', 2, '2016-07-10 00:00:00'),
(2, 'Client', '+79', 2, '2016-08-03 21:01:31'),
(3, 'Client', '+79', 3, '2016-07-10 00:00:00'),
(4, 'Client', '+79', 1, '2016-07-10 00:00:00'),
(5, 'Client', '+79', 4, '2016-07-10 00:00:00'),
(6, 'Client', '+79', 1, '2016-07-10 00:00:00'),
(7, 'Client', '+79', 1, '2016-07-10 00:00:00'),
(8, 'Client', '+79', 1, '2016-07-10 00:00:00'),
(9, 'Client', '+79', 1, '2016-07-10 00:00:00'),
(10, 'Client', '+79', 4, '2016-07-10 00:00:00'),
(11, 'Client', '+79', 2, '2016-07-10 00:00:00'),
(12, 'Client', '+79', 3, '2016-07-10 00:00:00'),
(13, 'Client', '+79', 2, '2016-07-10 00:00:00'),
(14, 'Client', '+79', 3, '2016-07-10 00:00:00'),
(15, 'Client', '+79', 2, '2016-07-10 00:00:00'),
(16, 'Client', '+79', 1, '2016-07-10 00:00:00'),
(17, 'Client', '+79', 3, '2016-07-10 00:00:00'),
(18, 'Client', '+79', 1, '2016-07-10 00:00:00'),
(19, 'Client', '+79', 4, '2016-07-10 00:00:00'),
(20, 'Client', '+79', 1, '2016-07-10 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `client_statuses`
--

CREATE TABLE IF NOT EXISTS `client_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `client_statuses`
--

INSERT INTO `client_statuses` (`id`, `name`) VALUES
(1, 'новый'),
(2, 'зарегистрирован'),
(3, 'отказался'),
(4, 'недоступен');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

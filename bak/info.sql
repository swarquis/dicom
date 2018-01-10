CREATE DATABASE IF NOT EXISTS dicom;
USE dicom;
CREATE TABLE IF NOT EXISTS `info` (
  `id` int(10) UNSIGNED KEY AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `age` TINYINT(4) NOT NULL DEFAULT 0,
  `sex` varchar(255) NOT NULL DEFAULT '',
  `jcbw` varchar(255) NOT NULL DEFAULT '',
  `jcrq` varchar(255) NOT NULL DEFAULT '',
  `dcmPath` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




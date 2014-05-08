/*
SQLyog Ultimate v9.63 
MySQL - 5.1.71 : Database - dbfuxionadmin
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


/*Table structure for table `tacl` */

DROP TABLE IF EXISTS `tacl`;

CREATE TABLE `tacl` (
  `idacl` int(10) NOT NULL AUTO_INCREMENT,
  `desacl` varchar(100) DEFAULT NULL,
  `modacl` varchar(100) DEFAULT NULL,
  `contacl` varchar(100) DEFAULT NULL,
  `actacl` varchar(100) DEFAULT NULL,
  `urlacl` varchar(250) NOT NULL,
  `state` tinyint(2) DEFAULT '1',
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idacl`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `tacl` */

insert  into `tacl`(`idacl`,`desacl`,`modacl`,`contacl`,`actacl`,`urlacl`,`state`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) values (1,'Perfil',NULL,NULL,NULL,'admin::profile::*',1,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL),(2,'Menu Inicio',NULL,NULL,NULL,'admin::dashboard::*',1,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL),(3,'Gestión de Usuarios',NULL,NULL,NULL,'admin::user::*',1,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL),(4,'Gestión de Roles',NULL,NULL,NULL,'admin::role::*',1,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL),(5,'Gestión de Productos',NULL,NULL,NULL,'admin::product::*',1,NULL,NULL,NULL,'0000-00-00 00:00:00',NULL,NULL),(6,'Gestión de Banners',NULL,NULL,NULL,'admin::banner::*',1,NULL,NULL,NULL,'2013-11-26 11:29:42',NULL,NULL),(7,'Gestión de Videos',NULL,NULL,NULL,'admin::video::*',1,NULL,NULL,NULL,'2013-11-29 11:30:47',NULL,NULL),(8,'Gestión de Fondos de Aplicación',NULL,NULL,NULL,'admin::background::*',1,NULL,NULL,NULL,'2013-12-05 09:50:32',NULL,NULL);

/*Table structure for table `tacl_to_roles` */

DROP TABLE IF EXISTS `tacl_to_roles`;

CREATE TABLE `tacl_to_roles` (
  `idaclrol` int(10) NOT NULL AUTO_INCREMENT,
  `idacl` int(10) NOT NULL,
  `idrol` tinyint(10) NOT NULL,
  PRIMARY KEY (`idaclrol`),
  KEY `idacl` (`idacl`),
  KEY `idrol` (`idrol`),
  CONSTRAINT `acl_to_roles_ibfk_1` FOREIGN KEY (`idacl`) REFERENCES `tacl` (`idacl`) ON DELETE CASCADE,
  CONSTRAINT `acl_to_roles_ibfk_2` FOREIGN KEY (`idrol`) REFERENCES `troles` (`idrol`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

/*Data for the table `tacl_to_roles` */

insert  into `tacl_to_roles`(`idaclrol`,`idacl`,`idrol`) values (1,1,1),(2,2,1),(3,3,1),(4,1,2),(5,2,2),(6,4,1),(13,1,5),(14,2,5),(15,4,5),(16,5,1),(17,6,1),(18,7,1),(19,8,1),(32,8,1),(33,8,1),(34,8,1),(35,8,1),(36,8,1);

/*Table structure for table `tbanner` */

DROP TABLE IF EXISTS `tbanner`;

CREATE TABLE `tbanner` (
  `idbanner` int(11) NOT NULL AUTO_INCREMENT,
  `codtbanner` varchar(10) DEFAULT NULL,
  `idimagen` int(11) DEFAULT NULL,
  `titulo` varchar(300) DEFAULT NULL,
  `descripcion` text,
  `url` varchar(250) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  `norder` int(11) DEFAULT NULL,
  PRIMARY KEY (`idbanner`),
  KEY `fk_tbanner_2_idx` (`codtbanner`),
  KEY `fk_tbanner_3_idx` (`idimagen`),
  CONSTRAINT `fk_banner_imagen_01` FOREIGN KEY (`idimagen`) REFERENCES `timagen` (`idimagen`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_banner_tipobanner_01` FOREIGN KEY (`codtbanner`) REFERENCES `ttipobanner` (`codtbanner`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `tbanner` */


/*Table structure for table `tblog` */

DROP TABLE IF EXISTS `tblog`;

CREATE TABLE `tblog` (
  `idblog` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(300) DEFAULT NULL,
  `descripcion` text,
  `url` varchar(250) DEFAULT NULL,
  `fecpubli` datetime DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idblog`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tblog` */

/*Table structure for table `tfile` */

DROP TABLE IF EXISTS `tfile`;

CREATE TABLE `tfile` (
  `idfile` int(11) NOT NULL AUTO_INCREMENT,
  `codtfile` varchar(10) DEFAULT NULL,
  `titulo` varchar(300) DEFAULT NULL,
  `nombre` varchar(300) DEFAULT NULL,
  `extfile` varchar(10) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idfile`),
  KEY `fk_tfile_2_idx` (`codtfile`),
  CONSTRAINT `fk_file_tipofile_01` FOREIGN KEY (`codtfile`) REFERENCES `ttipofile` (`codtfile`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*Data for the table `tfile` */

/*Table structure for table `tfondo` */

DROP TABLE IF EXISTS `tfondo`;

CREATE TABLE `tfondo` (
  `idfondo` int(11) NOT NULL AUTO_INCREMENT,
  `codtfondo` varchar(10) DEFAULT NULL,
  `titulo` varchar(300) DEFAULT NULL,
  `idimagen` int(11) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idfondo`),
  KEY `fk_tfondo_2_idx` (`codtfondo`),
  KEY `fk_tfondo_3_idx` (`idimagen`),
  CONSTRAINT `fk_fondo_imagen_01` FOREIGN KEY (`idimagen`) REFERENCES `timagen` (`idimagen`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_fondo_tipofondo_01` FOREIGN KEY (`codtfondo`) REFERENCES `ttipofondo` (`codtfondo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `tfondo` */

/*Table structure for table `timagen` */

DROP TABLE IF EXISTS `timagen`;

CREATE TABLE `timagen` (
  `idimagen` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idimagen`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

/*Data for the table `timagen` */

/*Table structure for table `tproyecto` */

DROP TABLE IF EXISTS `tproyecto`;

CREATE TABLE `tproyecto` (
  `codproy` varchar(10) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codproy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tproyecto` */

insert  into `tproyecto`(`codproy`,`nombre`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) values ('ADMIN','Administrador','A',NULL,NULL,NULL,'2013-11-26 11:56:37',NULL,NULL),('FUXIO','Desafío FUXION','A',NULL,NULL,NULL,'2013-11-26 11:56:37',NULL,NULL),('LANDI','Landing','A',NULL,NULL,NULL,'2013-11-26 11:56:37',NULL,NULL),('OFVIR','Oficina Virtual','A',NULL,NULL,NULL,'2013-11-26 11:56:37',NULL,NULL);


/*Table structure for table `troles` */

DROP TABLE IF EXISTS `troles`;

CREATE TABLE `troles` (
  `idrol` tinyint(10) NOT NULL AUTO_INCREMENT,
  `desrol` varchar(100) DEFAULT NULL,
  `coderol` varchar(100) DEFAULT NULL,
  `state` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`idrol`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `troles` */

insert  into `troles`(`idrol`,`desrol`,`coderol`,`state`) values (1,'Administrador','admin',1),(2,'Administrador Junior','admin_junior',1),(5,'Administrador Roles',NULL,1);

/*Table structure for table `ttipobanner` */

DROP TABLE IF EXISTS `ttipobanner`;

CREATE TABLE `ttipobanner` (
  `codtbanner` varchar(10) NOT NULL,
  `codproy` varchar(10) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `anchoimg` int(11) DEFAULT NULL,
  `altoimg` int(11) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codtbanner`),
  KEY `fk_ttipobanner_1_idx` (`codproy`),
  CONSTRAINT `fk_tipobanner_proyecto_01` FOREIGN KEY (`codproy`) REFERENCES `tproyecto` (`codproy`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `ttipobanner` */

insert  into `ttipobanner`(`codtbanner`,`codproy`,`nombre`,`anchoimg`,`altoimg`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) values ('HALLFAME','OFVIR','Hall de la Fama',275,540,'A',NULL,NULL,NULL,'2013-12-06 12:23:15',NULL,NULL),('HOMEOFVI','OFVIR','Portada Oficina Virtual',602,294,'A',NULL,NULL,NULL,'2013-12-06 12:23:12',NULL,NULL),('LANDPORT','LANDI','Portada Landing',486,272,'A',NULL,NULL,NULL,'2013-11-28 12:04:09',NULL,NULL);

/*Table structure for table `ttipofile` */

DROP TABLE IF EXISTS `ttipofile`;

CREATE TABLE `ttipofile` (
  `codtfile` varchar(10) NOT NULL,
  `codproy` varchar(10) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codtfile`),
  KEY `fk_ttipofile_1_idx` (`codproy`),
  CONSTRAINT `fk_tipofile_proyecto_01` FOREIGN KEY (`codproy`) REFERENCES `tproyecto` (`codproy`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `ttipofile` */

/*Table structure for table `ttipofondo` */

DROP TABLE IF EXISTS `ttipofondo`;

CREATE TABLE `ttipofondo` (
  `codtfondo` varchar(10) NOT NULL,
  `codproy` varchar(10) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codtfondo`),
  KEY `fk_ttipofondo_1_idx` (`codproy`),
  CONSTRAINT `fk_tipofondo_proyecto_01` FOREIGN KEY (`codproy`) REFERENCES `tproyecto` (`codproy`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `ttipofondo` */

insert  into `ttipofondo`(`codtfondo`,`codproy`,`nombre`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) values ('FONDO1','LANDI','Fondo 1 del Landing','A',NULL,NULL,NULL,'2013-12-05 10:07:50',NULL,NULL);

/*Table structure for table `ttipovideo` */

DROP TABLE IF EXISTS `ttipovideo`;

CREATE TABLE `ttipovideo` (
  `codtvideo` varchar(10) NOT NULL,
  `codproy` varchar(10) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codtvideo`),
  KEY `fk_ttipovideo_1_idx` (`codproy`),
  CONSTRAINT `fk_tipovideo_proyecto_01` FOREIGN KEY (`codproy`) REFERENCES `tproyecto` (`codproy`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `ttipovideo` */

insert  into `ttipovideo`(`codtvideo`,`codproy`,`nombre`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) values ('FUXIOFVI','OFVIR','Fuxion Oficina Virtual admin','A',NULL,NULL,NULL,'2013-12-06 16:37:08',NULL,NULL),('HOMEOFVI','OFVIR','Portada Oficina Virtual','A',NULL,NULL,NULL,'2013-11-29 10:04:33',NULL,NULL),('INSTOFVI','OFVIR','Insitutacional de la Oficina Virtual','A',NULL,NULL,NULL,'2013-11-29 12:11:39',NULL,NULL);

/*Table structure for table `tusers` */

DROP TABLE IF EXISTS `tusers`;

CREATE TABLE `tusers` (
  `iduser` int(10) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `apepat` varchar(50) DEFAULT NULL,
  `apemat` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(300) DEFAULT NULL,
  `lastpasschange` datetime DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `state` tinyint(2) DEFAULT '1',
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `tusers` */

insert  into `tusers`(`iduser`,`login`,`name`,`apepat`,`apemat`,`email`,`password`,`lastpasschange`,`lastlogin`,`state`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) values (1,'administrador','Administrador','Max','Max','admin@onlinebusiness.com.pe','e10adc3949ba59abbe56e057f20f883e',NULL,NULL,1,NULL,'0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00',NULL,NULL),(2,'yrving','Yrving','Ramírez','Liza','yrving@onlinebusiness.com.pe','e10adc3949ba59abbe56e057f20f883e',NULL,NULL,1,NULL,'0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00',NULL,NULL),(3,'usuario','Usuario','Prueba','Básico','basicuser@gmail.com','d41d8cd98f00b204e9800998ecf8427e','2013-10-31 11:15:45',NULL,0,NULL,'0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00',NULL,NULL),(6,'dpozo','Dennis','Pozo','Chura','dpozo@gmail.com','d41d8cd98f00b204e9800998ecf8427e','2013-10-31 12:15:32',NULL,1,NULL,'0000-00-00 00:00:00',NULL,'0000-00-00 00:00:00',NULL,NULL),(7,'23','',NULL,NULL,NULL,'d41d8cd98f00b204e9800998ecf8427e',NULL,NULL,0,NULL,'0000-00-00 00:00:00',NULL,'2013-11-20 12:28:28',NULL,NULL);

/*Table structure for table `tusers_to_roles` */

DROP TABLE IF EXISTS `tusers_to_roles`;

CREATE TABLE `tusers_to_roles` (
  `iduser` int(10) NOT NULL,
  `idrol` tinyint(10) NOT NULL,
  PRIMARY KEY (`iduser`,`idrol`),
  KEY `users_to_roles_ibfk_2` (`idrol`),
  CONSTRAINT `users_to_roles_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `tusers` (`iduser`) ON DELETE CASCADE,
  CONSTRAINT `users_to_roles_ibfk_2` FOREIGN KEY (`idrol`) REFERENCES `troles` (`idrol`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tusers_to_roles` */

insert  into `tusers_to_roles`(`iduser`,`idrol`) values (1,1),(6,1),(2,2),(3,2);

/*Table structure for table `tvideo` */

DROP TABLE IF EXISTS `tvideo`;

CREATE TABLE `tvideo` (
  `idvideo` int(11) NOT NULL AUTO_INCREMENT,
  `codtvideo` varchar(10) DEFAULT NULL,
  `titulo` varchar(300) DEFAULT NULL,
  `descripcion` text,
  `url` varchar(250) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idvideo`),
  KEY `fk_tvideo_2_idx` (`codtvideo`),
  CONSTRAINT `fk_video_tipovideo_01` FOREIGN KEY (`codtvideo`) REFERENCES `ttipovideo` (`codtvideo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `tvideo` */

insert  into `tvideo`(`idvideo`,`codtvideo`,`titulo`,`descripcion`,`url`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) values (1,'HOMEOFVI','Portada 1 admin','','www..youtube.com','A',1,'2013-11-29 12:52:13',1,'2013-12-05 14:49:40',NULL,NULL),(2,'INSTOFVI','¿Por qué Prolife?','wa','http://www.youtube.com/watch?v=rFod0PultTg','A',1,'2013-11-29 14:57:36',1,'2013-12-05 14:49:28',NULL,NULL),(3,'HOMEOFVI','Portada 2','','www..youtube.com','I',1,'2013-11-29 17:15:57',NULL,'2013-11-29 17:15:40',NULL,NULL);


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


/******** Fecha: 05/12/2013  *******/
INSERT INTO `ttipofile` (`codtfile`, `codproy`, `nombre`, `vchestado`, `vchusucrea`, `tmsfeccrea`, `vchusumodif`, `tmsfecmodif`, `vchequipo`, `vchprograma`) values('MANUOFVI','OFVIR','Manuales Ofivina Virtual','A',NULL,'2013-12-10 10:49:40',NULL,'2013-12-10 10:49:40',NULL,NULL);

INSERT INTO `tacl` (`desacl`, `urlacl`, `state`) VALUES ('Gestión de Archivos', 'admin::file::*', '1');
INSERT INTO `tacl_to_roles` (`idacl`, `idrol`) VALUES ('9', '1');



/******** Fecha: 10/12/2013  *******/

CREATE TABLE `tencuesta` (
  `idencuesta` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta` varchar(200) DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `encuestados` int(11) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NULL DEFAULT NULL,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idencuesta`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `tencuesta_alter` (
  `idtencuestaalr` int(11) NOT NULL AUTO_INCREMENT,
  `cantidad` int(11) DEFAULT NULL,
  `alternativa` varchar(120) DEFAULT NULL,
  `tencuesta` int(11) DEFAULT NULL,
  PRIMARY KEY (`idtencuestaalr`),
  KEY `fk_tencuesta_alter_1` (`tencuesta`),
  CONSTRAINT `fk_tencuesta_alter_1` FOREIGN KEY (`tencuesta`) REFERENCES `tencuesta` (`idencuesta`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




INSERT INTO `tacl` (`desacl`, `urlacl`, `state`) VALUES ('Gestión de Encuesta', 'admin::quiz::*', '1');
INSERT INTO `tacl_to_roles` (`idacl`, `idrol`) VALUES ('10', '1');


/******** Fecha: 12/12/2013  *******/

INSERT INTO `tacl` (`desacl`, `urlacl`, `state`) VALUES ('Gestión de Blog', 'admin::blog::*', '1');
INSERT INTO `tacl_to_roles` (`idacl`, `idrol`) VALUES ('11', '1');


/******** Fecha: 13/12/2013  *******/



CREATE TABLE `tempresario_encuesta` (
  `idencuesta` int(11) NOT NULL,
  `codempr` int(11) NOT NULL,
  PRIMARY KEY (`idencuesta`,`codempr`),
  KEY `fk_tempresario_encuesta_1` (`idencuesta`),
  CONSTRAINT `fk_tempresario_encuesta_1` FOREIGN KEY (`idencuesta`) REFERENCES `tencuesta` (`idencuesta`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;






/******** Fecha: 16/12/2013  *******/
INSERT INTO `tacl` (`desacl`, `urlacl`, `state`) VALUES ('Gestión de Ayuda', 'admin::help::*', '1');
INSERT INTO `tacl_to_roles` (`idacl`, `idrol`) VALUES ('12', '1');

    CREATE  TABLE `tayuda` (
  `idayuda` INT(11) NOT NULL AUTO_INCREMENT ,
  `pregunta` VARCHAR(200) NULL ,
  `respuesta` VARCHAR(500) NULL ,
  `vchestado` VARCHAR(1) NULL ,
  `vchusucrea` INT(11) NULL ,
  `tmsfeccrea` DATETIME NULL ,
  `vchusumodif` INT(11) NULL ,
  `tmsfecmodif` TIMESTAMP NULL ,
  `vchequipo` VARCHAR(50) NULL ,
  `vchprograma` VARCHAR(50) NULL ,
  PRIMARY KEY (`idayuda`) 
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;



/******** Fecha 30/12/2013  *****/

INSERT INTO `tacl` (`desacl`, `urlacl`, `state`, `tmsfecmodif`) VALUES ('Gestión de Lineas', 'admin::line::*', '1', '2013-12-30 12:51:00');
INSERT INTO `tacl_to_roles` (`idacl`, `idrol`) VALUES ('13', '1');

/******** Fecha: 30/12/2013  *******/

INSERT INTO `ttipofile`
(`codtfile`,`codproy`,`nombre`,`vchestado`,`tmsfeccrea`,`tmsfecmodif`)
VALUES(
'AFILOFVI','OFVIR','afiliacion','A','2013-12-30 10:49:40','2013-12-30 10:49:40');
INSERT INTO `ttipofile`
(`codtfile`,`codproy`,`nombre`,`vchestado`,`tmsfeccrea`,`tmsfecmodif`)
VALUES(
'BANNOFVI','OFVIR','banners','A','2013-12-30 10:49:40','2013-12-30 10:49:40');
INSERT INTO `ttipofile`
(`codtfile`,`codproy`,`nombre`,`vchestado`,`tmsfeccrea`,`tmsfecmodif`)
VALUES(
'LOGOOFVI','OFVIR','logotipo','A','2013-12-30 10:49:40','2013-12-30 10:49:40');
INSERT INTO `ttipofile`
(`codtfile`,`codproy`,`nombre`,`vchestado`,`tmsfeccrea`,`tmsfecmodif`)
VALUES(
'PRODOFVI','OFVIR','productos','A','2013-12-30 10:49:40','2013-12-30 10:49:40');
INSERT INTO `ttipofile`
(`codtfile`,`codproy`,`nombre`,`vchestado`,`tmsfeccrea`,`tmsfecmodif`)
VALUES(
'SALUOFVI','LANDI','salud verdadera','A','2013-12-30 10:49:40','2013-12-30 10:49:40');
INSERT INTO `ttipofile`
(`codtfile`,`codproy`,`nombre`,`vchestado`,`tmsfeccrea`,`tmsfecmodif`)
VALUES(
'LIBEOFVI','LANDI','libertad financiera','A','2013-12-30 10:49:40','2013-12-30 10:49:40');




/******** Fecha: 06/01/2014  *******/


INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'AFILOFVI','INSTRUCTIVO PARA ENVIO DE FACTURAS',
'ggpjkpe1zqxevop.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','INSTRUCTIVO PARA ENVIO DE FACTURAS');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'AFILOFVI','LOGOTIPO FUXION',
'f7bbj2s84jk4mne.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'AFILOFVI','LOGOTIPO FUXION AZUL',
'b9u0nz2nuqpta4r.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION AZUL');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'AFILOFVI','LOGOTIPO FUXION ',
'6rm265rjdyiarbn.png','png','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION ');



INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'BANNOFVI','INSTRUCTIVO PARA ENVIO DE FACTURAS',
'ggpjkpe1zqxevop.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','INSTRUCTIVO PARA ENVIO DE FACTURAS');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'BANNOFVI','LOGOTIPO FUXION',
'f7bbj2s84jk4mne.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'BANNOFVI','LOGOTIPO FUXION AZUL',
'b9u0nz2nuqpta4r.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION AZUL');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'BANNOFVI','LOGOTIPO FUXION ',
'6rm265rjdyiarbn.png','png','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION ');



INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'LOGOOFVI','INSTRUCTIVO PARA ENVIO DE FACTURAS',
'ggpjkpe1zqxevop.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','INSTRUCTIVO PARA ENVIO DE FACTURAS');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'LOGOOFVI','LOGOTIPO FUXION',
'f7bbj2s84jk4mne.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'LOGOOFVI','LOGOTIPO FUXION AZUL',
'b9u0nz2nuqpta4r.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION AZUL');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'LOGOOFVI','LOGOTIPO FUXION ',
'6rm265rjdyiarbn.png','png','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION ');



INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'MANUOFVI','INSTRUCTIVO PARA ENVIO DE FACTURAS',
'ggpjkpe1zqxevop.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','INSTRUCTIVO PARA ENVIO DE FACTURAS');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'MANUOFVI','LOGOTIPO FUXION',
'f7bbj2s84jk4mne.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'MANUOFVI','LOGOTIPO FUXION AZUL',
'b9u0nz2nuqpta4r.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION AZUL');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'MANUOFVI','LOGOTIPO FUXION ',
'6rm265rjdyiarbn.png','png','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION ');


INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'PRODOFVI','INSTRUCTIVO PARA ENVIO DE FACTURAS',
'ggpjkpe1zqxevop.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','INSTRUCTIVO PARA ENVIO DE FACTURAS');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'PRODOFVI','LOGOTIPO FUXION',
'f7bbj2s84jk4mne.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'PRODOFVI','LOGOTIPO FUXION AZUL',
'b9u0nz2nuqpta4r.pdf','pdf','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION AZUL');
INSERT INTO `tfile`
(`codtfile`,`titulo`,`nombre`,`extfile`,`vchestado`,`vchusucrea`,
`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`descripcion`)
VALUES
(
'PRODOFVI','LOGOTIPO FUXION ',
'6rm265rjdyiarbn.png','png','A','1','2013-12-10 15:46:22','1',
'2013-12-10 15:46:22','LOGOTIPO FUXION ');


INSERT INTO `tvideo`
(
`codtvideo`,
`titulo`,
`descripcion`,
`url`,
`vchestado`,
`vchusucrea`)
VALUES
(
'HOMEOFVI',
'FELIZ NAVIDAD',
'Navidad ',
'http://www.fuxionbiotech.com/oficina2/media/contents/banners/GIF-WEB.gif',
'A',
'1'
);



/******** Fecha 08/01/2014  *****/

INSERT INTO `tacl` (`desacl`, `urlacl`, `state`, `tmsfecmodif`) VALUES ('Gestión de Catálogo', 'admin::catalog::*', '1', '2014-01-08 22:00:00');
INSERT INTO `tacl_to_roles` (`idacl`, `idrol`) VALUES ('14', '1');


/******** Fecha 20/01/2014  *****/

INSERT INTO `ttipofondo` (`codtfondo`, `codproy`, `nombre`, `vchestado`, `tmsfecmodif`) VALUES ('CFLANDI', 'LANDI', '#ffffff', 'C', '2014-01-20 10:07:50');
INSERT INTO `ttipofondo` (`codtfondo`, `codproy`, `nombre`, `vchestado`, `tmsfecmodif`) VALUES ('FONDOM', 'LANDI', 'Fondo medio del landing', 'A', '2014-01-20 10:07:50');


INSERT INTO `tacl` (`desacl`, `urlacl`, `state`, `tmsfecmodif`) VALUES ('Color de Fondo', 'admin::background-color::*', '1', '2014-01-08 22:00:00');
INSERT INTO `tacl_to_roles` (`idacl`, `idrol`) VALUES ('15', '1');




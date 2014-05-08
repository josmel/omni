
ALTER TABLE `tempresario` 
ADD COLUMN `subdomain` VARCHAR(50) NULL , 
ADD COLUMN `testimony` TEXT NULL AFTER `subdomain` ,
ADD COLUMN `urlyoutube` VARCHAR(250) NULL  AFTER `testimony` , 
ADD COLUMN `urlfacebook` VARCHAR(250) NULL  AFTER `urlyoutube` , 
ADD COLUMN `urltwitter` VARCHAR(250) NULL  AFTER `urlfacebook` , 
ADD COLUMN `urlblog` VARCHAR(250) NULL  AFTER `urltwitter`,
ADD COLUMN `picture` VARCHAR(200) NULL  AFTER `urlblog`,
ADD UNIQUE INDEX `subdomain_UNIQUE` (`subdomain` ASC) ;

ALTER TABLE `ttipmoneda` ADD COLUMN `simbolo` VARCHAR(10) NULL  AFTER `vchprograma` ;


ALTER TABLE `ttipproducto` 
ADD COLUMN `flagview` VARCHAR(1) NULL  AFTER `vchprograma` , 
ADD COLUMN `picture` VARCHAR(250) NULL  AFTER `flagview` , 
ADD COLUMN `slug` VARCHAR(250) NULL  AFTER `picture` ,
ADD COLUMN `hexcolor` VARCHAR(10) NULL  AFTER `slug` ;


ALTER TABLE `tproducto` 
ADD COLUMN `text` TEXT NULL  AFTER `abrprod` , 
ADD COLUMN `shorttext` VARCHAR(250) NULL  AFTER `text` ,
ADD COLUMN `slug` VARCHAR(250) NULL  AFTER `vchprograma` ;


CREATE TABLE `tmailing` (
  `idmailing` INT NOT NULL AUTO_INCREMENT ,
  `from` VARCHAR(300) NULL ,
  `to` VARCHAR(300) NULL ,
  `subject` VARCHAR(300) NULL ,
  `data` TEXT NULL ,
  `template` VARCHAR(50) NULL ,
  `recorddate` DATETIME NULL ,
  PRIMARY KEY (`idmailing`) );


CREATE  TABLE `tmailing_to_empresario` (
  `idmailing` INT NOT NULL ,
  `codempr` INT NOT NULL ,
  PRIMARY KEY (`idmailing`, `codempr`) );


CREATE TABLE `tafiliado` (
  `idafiliado` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(300) DEFAULT NULL,
  `ndoc` varchar(15) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `lastupdate` datetime DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `gender` VARCHAR(1) DEFAULT NULL, 
  `civilstate` VARCHAR(1) DEFAULT NULL, 
  `codempr` int(11) DEFAULT NULL,
  PRIMARY KEY (`idafiliado`),
  CONSTRAINT `afiliado_to_empresario_ibfk_1` FOREIGN KEY (`codempr`) 
  REFERENCES `tempresario` (`codempr`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE  TABLE `tafiliadorecover` (
  `idafiliadorecover` INT NOT NULL AUTO_INCREMENT ,
  `token` VARCHAR(300) NULL ,
  `idafiliado` INT NULL ,
  `createdate` DATETIME NULL ,
  `vchestado` VARCHAR(1) NULL ,
  PRIMARY KEY (`idafiliadorecover`) ,
  CONSTRAINT `fk_tafiliadorecover_1` FOREIGN KEY (`idafiliado`)
  REFERENCES `tafiliado` (`idafiliado`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `tdirenvio` ADD COLUMN `idafiliado` INT NULL  AFTER `vchprograma` , 
  ADD CONSTRAINT `fk_tdirenvio_to_tafiliado_01`
  FOREIGN KEY (`idafiliado` )
  REFERENCES `tafiliado` (`idafiliado` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_afiliado_01_idx` (`idafiliado` ASC) ;


ALTER TABLE `tubigeo`
ADD COLUMN `codupar` VARCHAR(10) NULL  AFTER `vchprograma` ;


ALTER TABLE `tubigeo`  
ADD INDEX `fk_tubigeo_01_idx` (`codupar` ASC) ;


UPDATE tubigeo 
    SET codupar = CONCAT(SUBSTRING(codubig,1,6), '0000')
WHERE NOT SUBSTRING(codubig,7,4) LIKE '0000';


UPDATE tubigeo 
    SET codupar = CONCAT(SUBSTRING(codubig,1,2), '00000000')
WHERE NOT SUBSTRING(codubig,3,8) LIKE '00000000' AND SUBSTRING(codubig,7,4) LIKE '0000';


CREATE VIEW vbusinessman 
AS 
SELECT e.codempr, e.nomempr, e.appempr, e.apmempr, e.emaempr, e.sexempr, e.vchestado, e.subdomain,
       e.testimony, e.urlyoutube, e.urlfacebook, e.urltwitter, e.urlblog, e.picture, 
       c.idcont, c.telefono, c.celular, d.iddire, d.desdire , d.numdire, d.intdire, d.refdire,
	   p.codpais, p.nompais, p.corpais, p.sigpais, p.nivpais, p.mafpais, p.mpepais, 
	   m.destmon, m. simbolo
FROM tempresario AS e 
LEFT JOIN tcontacto AS c ON e.codempr = c.codempr AND c.vchestado LIKE 'A' AND c.codtcon LIKE 'TC5'
LEFT JOIN tdireccion AS d ON e.codempr = d.codempr AND d.vchestado LIKE 'A'
LEFT JOIN tpais AS p ON d.codpais = p.codpais AND p.vchestado LIKE 'A' 
LEFT JOIN ttipmoneda AS m ON p.codpais = m.codpais AND m.vchestado LIKE 'A' 
WHERE e.vchestado LIKE 'A';






ALTER TABLE `tafiliado` 
CHANGE COLUMN `idafiliado` `idcliper` INT(11) NOT NULL AUTO_INCREMENT, 
RENAME TO  `tclientepersonal`;


ALTER TABLE `tdirenvio` 
DROP FOREIGN KEY `fk_tdirenvio_to_tafiliado_01`;


ALTER TABLE `tdirenvio` 
CHANGE COLUMN `idafiliado` `idcliper` INT(11) NULL DEFAULT NULL,  
DROP INDEX `fk_afiliado_01_idx`;


ALTER TABLE `tdirenvio` 
ADD CONSTRAINT `fk_tclientepersonal_1`
FOREIGN KEY (`idcliper` )
REFERENCES `tclientepersonal` (`idcliper` )
ON DELETE NO ACTION ON UPDATE NO ACTION, 
ADD INDEX `fk_tclientepersonal_1_idx` (`idcliper` ASC) ;


ALTER TABLE `tpais` 
ADD COLUMN `ndopais` TINYINT(2) NULL  AFTER `vchprograma`, 
ADD COLUMN `tdopais` INT NULL  AFTER `ndopais`;


CREATE OR REPLACE 
VIEW `vbusinessman` AS
    SELECT e.codempr, e.nomempr, e.appempr, e.apmempr, e.emaempr, e.sexempr, e.vchestado, e.subdomain,
       e.testimony, e.urlyoutube, e.urlfacebook, e.urltwitter, e.urlblog, e.picture, 
       c.idcont, c.telefono, c.celular, d.iddire, d.desdire , d.numdire, d.intdire, d.refdire,
	   p.codpais, p.nompais, p.corpais, p.sigpais, p.nivpais, p.mafpais, p.mpepais, p.ndopais, p.tdopais,
	   m.destmon, m. simbolo, u.codubig, u.desubig, u.ivaubig as iva
FROM tempresario AS e 
LEFT JOIN tcontacto AS c ON e.codempr = c.codempr AND c.vchestado LIKE 'A' AND c.codtcon LIKE 'TC5'
LEFT JOIN tdireccion AS d ON e.codempr = d.codempr AND d.vchestado LIKE 'A'
LEFT JOIN tpais AS p ON d.codpais = p.codpais AND p.vchestado LIKE 'A' 
LEFT JOIN tubigeo AS u ON d.codpais = u.codpais AND d.codubig = u.codubig AND p.vchestado LIKE 'A' 
LEFT JOIN ttipmoneda AS m ON p.codpais = m.codpais AND m.vchestado LIKE 'A' 
WHERE e.vchestado LIKE 'A';


ALTER TABLE `tafiliadorecover` 
CHANGE COLUMN `idafiliadorecover` `idcliperrecover` INT(11) NOT NULL AUTO_INCREMENT,
CHANGE COLUMN `idafiliado` `idcliper` INT(11) NULL DEFAULT NULL , 
RENAME TO  `tclientepersonalrecover` ;



/* RELACION DE TABLA PEDIDO Y CLIENTE PERSONAL */
ALTER TABLE `tpedido` 
ADD COLUMN `idcliper` INT NULL  AFTER `vchprograma` , 
ADD CONSTRAINT `fk_cliper_05` FOREIGN KEY (`idcliper` )
REFERENCES `tclientepersonal` (`idcliper` )
ON DELETE NO ACTION ON UPDATE NO ACTION, 
ADD INDEX `fk_cliper_05_idx` (`idcliper` ASC);






ALTER TABLE `tmailing`  
ADD COLUMN `vchusucrea` int(11) DEFAULT NULL,
ADD COLUMN `tmsfeccrea` datetime DEFAULT NULL,
ADD COLUMN `vchusumodif` int(11) DEFAULT NULL,
ADD COLUMN `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `vchequipo` varchar(50) DEFAULT NULL,
ADD COLUMN `vchprograma` varchar(50) DEFAULT NULL;


ALTER TABLE `tclientepersonal`  
ADD COLUMN `vchusucrea` int(11) DEFAULT NULL,
ADD COLUMN `tmsfeccrea` datetime DEFAULT NULL,
ADD COLUMN `vchusumodif` int(11) DEFAULT NULL,
ADD COLUMN `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `vchequipo` varchar(50) DEFAULT NULL,
ADD COLUMN `vchprograma` varchar(50) DEFAULT NULL;


ALTER TABLE `tclientepersonalrecover`  
ADD COLUMN `vchusucrea` int(11) DEFAULT NULL,
ADD COLUMN `tmsfeccrea` datetime DEFAULT NULL,
ADD COLUMN `vchusumodif` int(11) DEFAULT NULL,
ADD COLUMN `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `vchequipo` varchar(50) DEFAULT NULL,
ADD COLUMN `vchprograma` varchar(50) DEFAULT NULL;


CREATE OR REPLACE 
VIEW `vbusinessman` AS
    SELECT e.codempr, e.nomempr, e.appempr, e.apmempr, e.emaempr, e.sexempr, e.vchestado, e.subdomain,
       e.testimony, e.urlyoutube, e.urlfacebook, e.urltwitter, e.urlblog, e.picture, 
       c.idcont, c.telefono, c.celular, d.iddire, d.desdire , d.numdire, d.intdire, d.refdire,
	   p.codpais, p.nompais, p.corpais, p.sigpais, p.nivpais, p.mafpais, p.mpepais, p.ndopais, p.tdopais, p.dolpais, 
	   m.destmon, m. simbolo, u.codubig, u.desubig, u.ivaubig as iva
FROM tempresario AS e 
LEFT JOIN tcontacto AS c ON e.codempr = c.codempr AND c.vchestado LIKE 'A' AND c.codtcon LIKE 'TC5'
LEFT JOIN tdireccion AS d ON e.codempr = d.codempr AND d.vchestado LIKE 'A'
LEFT JOIN tpais AS p ON d.codpais = p.codpais AND p.vchestado LIKE 'A' 
LEFT JOIN tubigeo AS u ON d.codpais = u.codpais AND d.codubig = u.codubig AND p.vchestado LIKE 'A' 
LEFT JOIN ttipmoneda AS m ON p.codpais = m.codpais AND m.vchestado LIKE 'A' 
WHERE e.vchestado LIKE 'A';



/******** Fecha: 21/11/2013  *******/
CREATE TABLE `tpais_to_tipopago` (
  `codpais` varchar(5) NOT NULL,
  `codtpag` varchar(5) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `responseuri` varchar(250) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codpais`,`codtpag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `tpais_to_tipopago` 
  ADD CONSTRAINT `fk_tpais_01`
  FOREIGN KEY (`codpais` )
  REFERENCES `tpais` (`codpais` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_ttipopago_02`
  FOREIGN KEY (`codtpag` )
  REFERENCES `ttipopago` (`codtpag` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `fk_tpais_01_idx` (`codpais` ASC) 
, ADD INDEX `fk_ttipopago_02_idx` (`codtpag` ASC) ;


/******** Fecha: 25/11/2013  *******/

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


CREATE TABLE `tbanner` (
  `idbanner` int(11) NOT NULL AUTO_INCREMENT,
  `codproy` varchar(10) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `titulo` varchar(300) DEFAULT NULL,
  `descripcion` text,
  `fechainicio` datetime DEFAULT NULL,
  `fechafin` datetime DEFAULT NULL,
  `slug` varchar(50) DEFAULT NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idbanner`),
  KEY `fk_tbanner_1_idx` (`codproy`),
  CONSTRAINT `fk_banner_proyecto_01` FOREIGN KEY (`codproy`) 
  REFERENCES `tproyecto` (`codproy`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******** Fecha: 26/11/2013  *******/
ALTER TABLE `tclientepersonal` 
ADD UNIQUE INDEX `email_UNIQUE` (`email` ASC) ;


/******** Fecha: 28/11/2013  *******/
CREATE  TABLE `ttipobanner` (
  `codtbanner` VARCHAR(10) NOT NULL ,
  `codproy` VARCHAR(10) NULL ,
  `nombre` VARCHAR(100) NULL ,
  `anchoimg` INT NULL ,
  `altoimg` INT NULL ,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codtbanner`),
  KEY `fk_ttipobanner_1_idx` (`codproy`),
  CONSTRAINT `fk_tipobanner_proyecto_01` FOREIGN KEY (`codproy`) 
  REFERENCES `tproyecto` (`codproy`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE  TABLE `timagen` (
  `idimagen` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(250) NULL,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idimagen`)
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE `tbanner`;


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
  PRIMARY KEY (`idbanner`),
  KEY `fk_tbanner_2_idx` (`codtbanner`),
  CONSTRAINT `fk_banner_tipobanner_01` FOREIGN KEY (`codtbanner`) 
  REFERENCES `ttipobanner` (`codtbanner`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION,
  KEY `fk_tbanner_3_idx` (`idimagen`),
  CONSTRAINT `fk_banner_imagen_01` FOREIGN KEY (`idimagen`) 
  REFERENCES `timagen` (`idimagen`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******** Fecha: 29/11/2013  *******/
CREATE  TABLE `ttipovideo` (
  `codtvideo` VARCHAR(10) NOT NULL ,
  `codproy` VARCHAR(10) NULL ,
  `nombre` VARCHAR(100) NULL ,
  `vchestado` varchar(1) DEFAULT NULL,
  `vchusucrea` int(11) DEFAULT NULL,
  `tmsfeccrea` datetime DEFAULT NULL,
  `vchusumodif` int(11) DEFAULT NULL,
  `tmsfecmodif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` varchar(50) DEFAULT NULL,
  `vchprograma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codtvideo`),
  KEY `fk_ttipovideo_1_idx` (`codproy`),
  CONSTRAINT `fk_tipovideo_proyecto_01` FOREIGN KEY (`codproy`) 
  REFERENCES `tproyecto` (`codproy`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
 )ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
  CONSTRAINT `fk_video_tipovideo_01` FOREIGN KEY (`codtvideo`) 
  REFERENCES `ttipovideo` (`codtvideo`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******** Fecha: 04/12/2013  *******/
CREATE  TABLE `ttipofondo` (
  `codtfondo` VARCHAR(10) NOT NULL ,
  `codproy` VARCHAR(10) NULL ,
  `nombre` VARCHAR(100) NULL ,
  `vchestado` VARCHAR(1) DEFAULT NULL,
  `vchusucrea` INT(11) DEFAULT NULL,
  `tmsfeccrea` DATETIME DEFAULT NULL,
  `vchusumodif` INT(11) DEFAULT NULL,
  `tmsfecmodif` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` VARCHAR(50) DEFAULT NULL,
  `vchprograma` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`codtfondo`),
  KEY `fk_ttipofondo_1_idx` (`codproy`),
  CONSTRAINT `fk_tipofondo_proyecto_01` FOREIGN KEY (`codproy`) 
  REFERENCES `tproyecto` (`codproy`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
 )ENGINE=INNODB DEFAULT CHARSET=utf8;


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
  CONSTRAINT `fk_fondo_tipofondo_01` FOREIGN KEY (`codtfondo`) 
  REFERENCES `ttipofondo` (`codtfondo`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION,
  KEY `fk_tfondo_3_idx` (`idimagen`),
  CONSTRAINT `fk_fondo_imagen_01` FOREIGN KEY (`idimagen`) 
  REFERENCES `timagen` (`idimagen`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `tbanner` 
ADD COLUMN `norder` INT(11) NULL ;


/******** Fecha: 05/12/2013  *******/
ALTER TABLE `tproducto` 
ADD COLUMN `pesoprod` DECIMAL(10, 2) NULL,
ADD COLUMN `desccaja` VARCHAR(200) NULL;


/******** Fecha: 06/12/2013  *******/
ALTER TABLE `ttipmoneda` ADD COLUMN `codint` VARCHAR(10) NULL;


CREATE OR REPLACE 
VIEW `vbusinessman` AS
    SELECT e.codempr, e.nomempr, e.appempr, e.apmempr, e.emaempr, e.sexempr, e.vchestado, e.subdomain,
       e.testimony, e.urlyoutube, e.urlfacebook, e.urltwitter, e.urlblog, e.picture, 
       c.idcont, c.telefono, c.celular, d.iddire, d.desdire , d.numdire, d.intdire, d.refdire,
	   p.codpais, p.nompais, p.corpais, p.sigpais, p.nivpais, p.mafpais, p.mpepais, p.ndopais, p.tdopais, p.dolpais, 
	   m.destmon, m.simbolo, m.codint, u.codubig, u.desubig, u.ivaubig AS iva
FROM tempresario AS e 
LEFT JOIN tcontacto AS c ON e.codempr = c.codempr AND c.vchestado LIKE 'A' AND c.codtcon LIKE 'TC5'
LEFT JOIN tdireccion AS d ON e.codempr = d.codempr AND d.vchestado LIKE 'A'
LEFT JOIN tpais AS p ON d.codpais = p.codpais AND p.vchestado LIKE 'A' 
LEFT JOIN tubigeo AS u ON d.codpais = u.codpais AND d.codubig = u.codubig AND p.vchestado LIKE 'A' 
LEFT JOIN ttipmoneda AS m ON p.codpais = m.codpais AND m.vchestado LIKE 'A' 
WHERE e.vchestado LIKE 'A';


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


/******** Fecha: 09/12/2013  *******/
CREATE  TABLE `ttipofile` (
  `codtfile` VARCHAR(10) NOT NULL ,
  `codproy` VARCHAR(10) NULL ,
  `nombre` VARCHAR(100) NULL ,
  `vchestado` VARCHAR(1) DEFAULT NULL,
  `vchusucrea` INT(11) DEFAULT NULL,
  `tmsfeccrea` DATETIME DEFAULT NULL,
  `vchusumodif` INT(11) DEFAULT NULL,
  `tmsfecmodif` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` VARCHAR(50) DEFAULT NULL,
  `vchprograma` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`codtfile`),
  KEY `fk_ttipofile_1_idx` (`codproy`),
  CONSTRAINT `fk_tipofile_proyecto_01` FOREIGN KEY (`codproy`) 
  REFERENCES `tproyecto` (`codproy`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
 )ENGINE=INNODB DEFAULT CHARSET=utf8;


CREATE TABLE `tfile` (
  `idfile` INT(11) NOT NULL AUTO_INCREMENT,
  `codtfile` VARCHAR(10) DEFAULT NULL,
  `titulo` VARCHAR(300) DEFAULT NULL,
  `nombre` VARCHAR(300) DEFAULT NULL,
  `extfile` VARCHAR(10) DEFAULT NULL,
  `vchestado` VARCHAR(1) DEFAULT NULL,
  `vchusucrea` INT(11) DEFAULT NULL,
  `tmsfeccrea` DATETIME DEFAULT NULL,
  `vchusumodif` INT(11) DEFAULT NULL,
  `tmsfecmodif` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` VARCHAR(50) DEFAULT NULL,
  `vchprograma` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`idfile`),
  KEY `fk_tfile_2_idx` (`codtfile`),
  CONSTRAINT `fk_file_tipofile_01` FOREIGN KEY (`codtfile`) 
  REFERENCES `ttipofile` (`codtfile`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=INNODB DEFAULT CHARSET=utf8;


/******** Fecha: 11/12/2013  *******/
CREATE  TABLE `ttipodocumento` (
  `idtdoc` INT(11) NOT NULL AUTO_INCREMENT,
  `codpais` VARCHAR(5) NULL,
  `destdoc` VARCHAR(100) NULL ,
  `vchestado` VARCHAR(1) DEFAULT NULL,
  `vchusucrea` INT(11) DEFAULT NULL,
  `tmsfeccrea` DATETIME DEFAULT NULL,
  `vchusumodif` INT(11) DEFAULT NULL,
  `tmsfecmodif` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` VARCHAR(50) DEFAULT NULL,
  `vchprograma` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`idtdoc`),
  KEY `fk_ttipopais_1_idx` (`codpais`)
 )ENGINE=INNODB DEFAULT CHARSET=utf8;
/*  CONSTRAINT `fk_tipodocumento_pais_01` FOREIGN KEY (`codpais`) 
  REFERENCES `tpais` (`codpais`) 
  ON DELETE NO ACTION ON UPDATE NO ACTION
*/


ALTER TABLE `tdireccion` 
ADD COLUMN `nomcont` VARCHAR(100) NULL , 
ADD COLUMN `idtdoc` INT(11) NULL ,
ADD COLUMN `pediestado` VARCHAR(1) NULL ,
ADD COLUMN `ndoc` VARCHAR(20) NULL , 
ADD COLUMN `telefono` VARCHAR(20) NULL ;
/*  ADD CONSTRAINT `fk_ttipodocumento_01` 
FOREIGN KEY (`idtdoc`) 
REFERENCES `tpais` (`idtdoc` )
ON DELETE NO ACTION ON UPDATE NO ACTION
*/

UPDATE tdireccion
SET pediestado = 'A'
WHERE vchestado LIKE 'A';


/******** Fecha: 16/12/2013  *******/
CREATE OR REPLACE 
VIEW `vbusinessman` AS
    SELECT e.codempr, e.codtemp, e.nomempr, e.appempr, e.apmempr, e.ndoempr, e.rucempr,  
           e.claempr, e.emaempr, e.fnaempr, e.sexempr, e.vchestado, e.subdomain,
           e.testimony, e.urlyoutube, e.urlfacebook, e.urltwitter, e.urlblog, e.picture, 
           c.idcont, c.telefono, c.celular, d.iddire, d.desdire , d.numdire, d.intdire, d.refdire,
	   p.codpais, p.nompais, p.corpais, p.sigpais, p.nivpais, p.mafpais, p.mpepais, p.ndopais, p.tdopais, p.dolpais, 
	   m.destmon, m.simbolo, m.codint, u.codubig, u.desubig, u.ivaubig AS iva
FROM tempresario AS e 
LEFT JOIN tcontacto AS c ON e.codempr = c.codempr AND c.vchestado LIKE 'A' AND c.codtcon LIKE 'TC5'
LEFT JOIN tdireccion AS d ON e.codempr = d.codempr AND d.vchestado LIKE 'A'
LEFT JOIN tpais AS p ON d.codpais = p.codpais AND p.vchestado LIKE 'A' 
LEFT JOIN tubigeo AS u ON d.codpais = u.codpais AND d.codubig = u.codubig AND p.vchestado LIKE 'A' 
LEFT JOIN ttipmoneda AS m ON p.codpais = m.codpais AND m.vchestado LIKE 'A' 
WHERE e.vchestado LIKE 'A';



/******** Fecha: 19/12/2013  *******/
ALTER TABLE `tpais` 
ADD COLUMN `nomniv1` VARCHAR(50) , 
ADD COLUMN `nomniv2` VARCHAR(50) , 
ADD COLUMN `nomniv3` VARCHAR(50);


CREATE OR REPLACE 
VIEW `vbusinessman` AS
    SELECT e.codempr, e.codtemp, e.nomempr, e.appempr, e.apmempr, e.ndoempr, e.rucempr,  
           e.claempr, e.emaempr, e.fnaempr, e.sexempr, e.vchestado, e.subdomain,
           e.testimony, e.urlyoutube, e.urlfacebook, e.urltwitter, e.urlblog, e.picture, 
           c.idcont, c.telefono, c.celular, d.iddire, d.desdire , d.numdire, d.intdire, d.refdire,
	   p.codpais, p.nompais, p.corpais, p.sigpais, p.nivpais, p.mafpais, p.mpepais, p.ndopais, 
           p.tdopais, p.dolpais, p.nomniv1, p.nomniv2, p.nomniv3, 
	   m.destmon, m.simbolo, m.codint, u.codubig, u.desubig, u.ivaubig AS iva
FROM tempresario AS e 
LEFT JOIN tcontacto AS c ON e.codempr = c.codempr AND c.vchestado LIKE 'A' AND c.codtcon LIKE 'TC5'
LEFT JOIN tdireccion AS d ON e.codempr = d.codempr AND d.vchestado LIKE 'A'
LEFT JOIN tpais AS p ON d.codpais = p.codpais AND p.vchestado LIKE 'A' 
LEFT JOIN tubigeo AS u ON d.codpais = u.codpais AND d.codubig = u.codubig AND p.vchestado LIKE 'A' 
LEFT JOIN ttipmoneda AS m ON p.codpais = m.codpais AND m.vchestado LIKE 'A' 
WHERE e.vchestado LIKE 'A';


ALTER TABLE `ttipvia` 
ADD COLUMN `abbrtvia` VARCHAR(10);




/******** Fecha: 20/12/2013  *******/

ALTER TABLE `tempresario` ADD COLUMN `token` VARCHAR(150) NULL  AFTER `picture` ;
ALTER TABLE `tempresario` ADD COLUMN `createdatetoken` TIMESTAMP NULL  AFTER `token` ;



/******** Fecha: 30/12/2013  *******/
CREATE  TABLE `ttipproducto_to_producto` (
  `codtpro` VARCHAR(2) NOT NULL ,
  `codprod` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`codtpro`, `codprod`) ,
  KEY `ttipproducto_to_producto_fk_1_idx` (`codtpro` ASC) ,
  KEY `ttipproducto_to_producto_fk_2_idx` (`codprod` ASC) 
)ENGINE=INNODB DEFAULT CHARSET=utf8;


DELIMITER $$
CREATE PROCEDURE UpdateDataProductTypes()
    BEGIN
        DECLARE cp VARCHAR(10);
        DECLARE ctp VARCHAR(2);
        DECLARE done INT DEFAULT 0;
        DECLARE cur_main CURSOR FOR SELECT p.codprod, tp.codtpro FROM tproducto p
                                    INNER JOIN ttipproducto tp ON tp.codtpro = p.codtpro;
        DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

        OPEN cur_main;
        
        lp:WHILE NOT done DO
            FETCH cur_main INTO cp, ctp;
            IF done = 1 THEN
                LEAVE lp;   
            END IF;
            INSERT INTO ttipproducto_to_producto(codprod, codtpro) VALUES (cp, ctp);
        END WHILE lp;
        CLOSE cur_main;
    END $$
DELIMITER ;

CALL UpdateDataProductTypes();



-- 2014-01-02
ALTER TABLE `ttipproducto` 
ADD COLUMN `picture2` TEXT NULL AFTER `hexcolor`;


ALTER TABLE `tproducto` 
ADD COLUMN `issalient` VARCHAR(1) NULL  AFTER `desccaja` ;


-- 2014-01-08
ALTER TABLE `tproducto` 
ADD COLUMN `imgextcat` VARCHAR(5) NULL,  
ADD COLUMN `imgextdet` VARCHAR(5) NULL;


ALTER TABLE `tclientepersonal` 
DROP INDEX `email_UNIQUE` ;


ALTER TABLE `ttipproducto` 
ADD COLUMN `line` VARCHAR(1) NULL DEFAULT '0', 
ADD COLUMN `catalog` VARCHAR(1) NULL DEFAULT '0';


-- 2014-01-09
ALTER TABLE `tpais_to_tipopago` ADD COLUMN `viewlanding` VARCHAR(1) NULL DEFAULT 0;


-- 2014-01-15
CREATE  TABLE `torderdatatemp` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `idpedi` INT NULL ,
  `bizpay` INT NULL ,
  `data` TEXT NULL ,
  `fecregistro` DATETIME NULL,
  `fecupdate` DATETIME NULL,
  PRIMARY KEY (`id`) )
ENGINE=INNODB DEFAULT CHARSET=utf8;



-- 2014-01-29
CREATE  TABLE `tproducto_favorito` (
  `codempr` INT NOT NULL ,
  `codprod` VARCHAR(10) NOT NULL ,
  `vchestado` VARCHAR(1) DEFAULT NULL,
  `vchusucrea` INT(11) DEFAULT NULL,
  `tmsfeccrea` DATETIME DEFAULT NULL,
  `vchusumodif` INT(11) DEFAULT NULL,
  `tmsfecmodif` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vchequipo` VARCHAR(50) DEFAULT NULL,
  `vchprograma` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`codempr`, `codprod`) )
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- 2014-02-12
ALTER TABLE `tdireccion` 
ADD COLUMN `zipcode` VARCHAR(20) NULL  AFTER `telefono`;


SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `dbfuxion_desafio` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `dbfuxion_desafio` ;

-- -----------------------------------------------------
-- Table `dbfuxion_desafio`.`ttipomusculo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbfuxion_desafio`.`ttipomusculo` (
  `idtipmusc` INT NOT NULL AUTO_INCREMENT,
  `musculo` VARCHAR(250) NULL,
  `vchestado` VARCHAR(1) NULL DEFAULT 'A',
  `vchusucrea` INT(11) NULL,
  `tmsfeccrea` DATETIME NULL,
  `vchusumodif` INT(11) NULL,
  `tmsfecmodif` TIMESTAMP NULL,
  PRIMARY KEY (`idtipmusc`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `dbfuxion_desafio`.`tparticipantes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbfuxion_desafio`.`tparticipantes` (
  `idparti` INT NOT NULL AUTO_INCREMENT,
  `codemp` INT NULL,
  `seudonimo` VARCHAR(50) NULL,
  `vchestado` VARCHAR(1) NULL DEFAULT 'A',
  `vchusucrea` INT(11) NULL,
  `tmsfeccrea` DATETIME NULL,
  `vchusumodif` INT(11) NULL,
  `tmsfecmodif` TIMESTAMP NULL,
  PRIMARY KEY (`idparti`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `dbfuxion_desafio`.`tciclo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbfuxion_desafio`.`tciclo` (
  `idciclo` INT NOT NULL AUTO_INCREMENT,
  `nomciclo` VARCHAR(250) NULL,
  `fecinicio` DATETIME NULL,
  `fecfin` DATETIME NULL,
  `nrosemana` INT NULL,
  `vchestado` VARCHAR(1) NULL DEFAULT 'A',
  `vchusucrea` INT(11) NULL,
  `tmsfeccrea` DATETIME NULL,
  `vchusumodif` INT(11) NULL,
  `tmsfecmodif` TIMESTAMP NULL,
  PRIMARY KEY (`idciclo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `dbfuxion_desafio`.`tmentor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbfuxion_desafio`.`tmentor` (
  `idmentor` INT NOT NULL AUTO_INCREMENT,
  `codemp` INT NULL,
  `nommentor` VARCHAR(250) NULL,
  `apepaterno` VARCHAR(250) NULL,
  `apematerno` VARCHAR(250) NULL,
  `telefono` VARCHAR(50) NULL,
  `correo` VARCHAR(60) NULL,
  `vchestado` VARCHAR(1) NULL DEFAULT 'A',
  `vchusucrea` INT(11) NULL,
  `tmsfeccrea` DATETIME NULL,
  `vchusumodif` INT(11) NULL,
  `tmsfecmodif` TIMESTAMP NULL,
  PRIMARY KEY (`idmentor`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `dbfuxion_desafio`.`tcicloparticipantes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbfuxion_desafio`.`tcicloparticipantes` (
  `idcicparti` INT NOT NULL AUTO_INCREMENT,
  `idciclo` INT NOT NULL,
  `idparti` INT NOT NULL,
  `idmentor` INT NOT NULL,
  `tiembaja` VARCHAR(100) NULL,
  `kilobaja` CHAR(11) NULL,
  `indgrasa` CHAR(11) NULL,
  `espalda` CHAR(11) NULL,
  `cintura` CHAR(11) NULL,
  `cadera` CHAR(11) NULL,
  `motivo` TEXT NULL,
  `compromiso` TEXT NULL,
  `fecini` DATETIME NULL,
  `fecfin` DATETIME NULL,
  `nrosemana` INT NULL,
  `vchestado` VARCHAR(1) NULL DEFAULT 'A',
  `vchusucrea` INT(11) NULL,
  `tmsfeccrea` DATETIME NULL,
  `vchusumodif` INT(11) NULL,
  `tmsfecmodif` TIMESTAMP NULL,
  PRIMARY KEY (`idcicparti`),
  INDEX `fk_cicloparticipantes_tciclo1_idx` (`idciclo` ASC),
  INDEX `fk_cicloparticipantes_tparticipantes1_idx` (`idparti` ASC),
  INDEX `fk_tcicloparticipantes_tmentor1_idx` (`idmentor` ASC),
  CONSTRAINT `fk_cicloparticipantes_tciclo1`
    FOREIGN KEY (`idciclo`)
    REFERENCES `dbfuxion_desafio`.`tciclo` (`idciclo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cicloparticipantes_tparticipantes1`
    FOREIGN KEY (`idparti`)
    REFERENCES `dbfuxion_desafio`.`tparticipantes` (`idparti`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tcicloparticipantes_tmentor1`
    FOREIGN KEY (`idmentor`)
    REFERENCES `dbfuxion_desafio`.`tmentor` (`idmentor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `dbfuxion_desafio`.`tdetaciclo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbfuxion_desafio`.`tdetaciclo` (
  `iddetacic` INT NOT NULL AUTO_INCREMENT,
  `idcicparti` INT NOT NULL,
  `idtipmusc` INT NOT NULL,
  `talla` CHAR(11) NULL,
  `muneca` CHAR(11) NULL,
  `peso` CHAR(11) NULL,
  `indgrasa` CHAR(11) NULL,
  `pecho` CHAR(11) NULL,
  `espalda` CHAR(11) NULL,
  `cintura` CHAR(11) NULL,
  `cadera` CHAR(11) NULL,
  `fotowincha` TEXT NULL,
  `fotoperfil` TEXT NULL,
  `fotofrente` TEXT NULL,
  `fotootros` TEXT NULL,
  `codfactcompra` CHAR(11) NULL,
  `cantcompra` INT NULL,
  `fecini` DATETIME NULL,
  `fecfin` DATETIME NULL,
  `semana` INT NULL,
  `vchestado` VARCHAR(1) NULL DEFAULT 'A',
  `vchusucrea` INT(11) NULL,
  `tmsfeccrea` DATETIME NULL,
  `vchusumodif` INT(11) NULL,
  `tmsfecmodif` TIMESTAMP NULL,
  PRIMARY KEY (`iddetacic`),
  INDEX `fk_tdetaciclo_tcicloparticipantes1_idx` (`idcicparti` ASC),
  INDEX `fk_tdetaciclo_ttipomusculo1_idx` (`idtipmusc` ASC),
  CONSTRAINT `fk_tdetaciclo_tcicloparticipantes1`
    FOREIGN KEY (`idcicparti`)
    REFERENCES `dbfuxion_desafio`.`tcicloparticipantes` (`idcicparti`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tdetaciclo_ttipomusculo1`
    FOREIGN KEY (`idtipmusc`)
    REFERENCES `dbfuxion_desafio`.`ttipomusculo` (`idtipmusc`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `dbfuxion_desafio`.`tcontacto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbfuxion_desafio`.`tcontacto` (
  `idcontacto` INT NOT NULL AUTO_INCREMENT,
  `nomconta` VARCHAR(200) NULL,
  `emailconta` VARCHAR(100) NULL,
  `consulta` TEXT NULL,
  `vchestado` VARCHAR(1) NULL DEFAULT 'A',
  `vchusucrea` INT(11) NULL,
  `tmsfeccrea` DATETIME NULL,
  `vchusumodif` INT(11) NULL,
  `tmsfecmodif` TIMESTAMP NULL,
  PRIMARY KEY (`idcontacto`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `dbfuxion_desafio`.`tuseradmin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dbfuxion_desafio`.`tuseradmin` (
  `iduseradmin` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NULL,
  `apellidos` VARCHAR(200) NULL,
  `usuario` VARCHAR(50) NULL,
  `contrasena` VARCHAR(100) NULL,
  `vchestado` VARCHAR(1) NULL,
  `tmsfeccrea` DATETIME NULL,
  PRIMARY KEY (`iduseradmin`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


-- --------------10-01-2014---------------

INSERT INTO `ttipomusculo` (`musculo`, `vchestado`, `tmsfeccrea`) VALUES ('Alta', 'A', '2014-01-10');
INSERT INTO `ttipomusculo` (`musculo`, `vchestado`, `tmsfeccrea`) VALUES ('Media', 'A', '2014-01-10');
INSERT INTO `ttipomusculo` (`musculo`, `vchestado`, `tmsfeccrea`) VALUES ('Baja', 'A', '2014-01-10');


ALTER TABLE `tdetaciclo` 
ADD COLUMN `deporte` CHAR(1) NULL AFTER `semana`;


ALTER TABLE `tmentor` 
ADD COLUMN `celular` VARCHAR(50) NULL AFTER `telefono`;


ALTER TABLE `tparticipantes` 
ADD COLUMN `edad` INT NULL  AFTER `tmsfecmodif` ;


-- --------------22-01-2014---------------

ALTER TABLE `tparticipantes` 
ADD COLUMN `nombre` VARCHAR(100) NULL  AFTER `edad` , 
ADD COLUMN `apellidos` VARCHAR(100) NULL  AFTER `nombre` , 
ADD COLUMN `estadoofi` TINYINT(1) NULL  AFTER `apellidos` ,
ADD COLUMN `sexo` VARCHAR(1) NULL  AFTER `estadoofi` ;


-- --------------24-01-2014---------------

ALTER TABLE `tciclo` ADD COLUMN `habilitado` TINYINT(1) NULL  AFTER `tmsfecmodif` ;


-- --------------28-01-2014---------------

ALTER TABLE `tparticipantes` ADD COLUMN `email` VARCHAR(250) NULL  AFTER `sexo` ;

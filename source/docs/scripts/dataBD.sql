UPDATE tempresario
SET 
    subdomain = CONCAT(TRIM(appempr), codempr) 
WHERE vchestado LIKE 'A';


UPDATE ttipmoneda
SET simbolo = 'S/.'
WHERE idtmon = 1;



UPDATE `ttipproducto` SET `flagview`='1', `picture`='bajapeso.png', `slug`='bajar-de-peso-y-reducir-medida', `hexcolor`='307DD5' WHERE `codtpro`='01';
UPDATE `ttipproducto` SET `flagview`='1', `picture`='inmuno.png', `slug`='fortalecer-el-sistema-inmunolo', `hexcolor`='219258' WHERE `codtpro`='02';
UPDATE `ttipproducto` SET `flagview`='1', `picture`='antiedad.png', `slug`='anti-edad', `hexcolor`='663872' WHERE `codtpro`='03';
UPDATE `ttipproducto` SET `flagview`='1', `picture`='mental.png', `slug`='incrementar-el-vigor-mental', `hexcolor`='BA2121' WHERE `codtpro`='04';
UPDATE `ttipproducto` SET `flagview`='1', `picture`='musculo.png', `slug`='incrementar-musc-y-rend-depo', `hexcolor`='000000' WHERE `codtpro`='05';
UPDATE `ttipproducto` SET `flagview`='1', `picture`='mujer.png', `slug`='mujer', `hexcolor`='D70784' WHERE `codtpro`='07';
UPDATE `ttipproducto` SET `flagview`='1', `picture`='kids.png', `slug`='kids', `hexcolor`='E1C92C' WHERE `codtpro`='08';


/******** Fecha: 21/11/2013  *******/
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`, `responseuri`) VALUES ('604', 'TP004', 'Alignet', '');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`, `tmsfecmodif`) VALUES ('604', 'TP011', 'Tarjeta Fuxion', '2013-11-21 12:27:25');


/******** Fecha: 26/11/2013  *******/
INSERT INTO `tproyecto` (`codproy`, `nombre`, `vchestado`) VALUES ('ADMIN', 'Administrador', 'A');
INSERT INTO `tproyecto` (`codproy`, `nombre`, `vchestado`) VALUES ('LANDI', 'Landing', 'A');
INSERT INTO `tproyecto` (`codproy`, `nombre`, `vchestado`) VALUES ('FUXIO', 'Desafío FUXION', 'A');
INSERT INTO `tproyecto` (`codproy`, `nombre`, `vchestado`) VALUES ('OFVIR', 'Oficina Virtual', 'A');


/******** Fecha: 28/11/2013  *******/
INSERT INTO `ttipobanner` (`codtbanner`, `codproy`, `nombre`, `anchoimg`, `altoimg`, `vchestado`) VALUES ('LANDPORT', 'LANDI', 'Portada Landing', '486', '272', 'A');


/******** Fecha: 29/11/2013  *******/
INSERT INTO `ttipovideo` (`codtvideo`, `codproy`, `nombre`, `vchestado`) VALUES ('HOMEOFVI', 'OFVIR', 'Portada Oficina Virtual', 'A');


/******** Fecha: 05/12/2013  *******/
INSERT INTO `ttipofondo` (`codtfondo`, `codproy`, `nombre`, `vchestado`) VALUES ('FONDO1', 'LANDI', 'Fondo 1 del Landing', 'A');


/******** Fecha: 06/12/2013  *******/
INSERT INTO `ttipobanner` (`codtbanner`, `codproy`, `nombre`, `anchoimg`, `altoimg`, `vchestado`) VALUES ('HOMEOFVI', 'OFVIR', 'Portada Oficina Virtual', '602', '294', 'A');
INSERT INTO `ttipobanner` (`codtbanner`, `codproy`, `nombre`, `anchoimg`, `altoimg`, `vchestado`) VALUES ('HALLFAME', 'OFVIR', 'Hall de la Fama', '275', '540', 'A');

INSERT INTO `ttipovideo` (`codtvideo`, `codproy`, `nombre`, `vchestado`) VALUES ('FUXIOFVI', 'OFVIR', 'Fuxion Oficina Virtual', 'A');


/******** Fecha: 19/12/2013  *******/
UPDATE `tpais` SET nomniv1 = 'Departamento', nomniv2 = 'Provincia', nomniv3 = 'Distrito' WHERE codpais = 604;

UPDATE `ttipvia` SET `abbrtvia`='Ala.' WHERE `codtvia`='AL001';
UPDATE `ttipvia` SET `abbrtvia`='Carr.' WHERE `codtvia`='AL002';
UPDATE `ttipvia` SET `abbrtvia`='Ca.' WHERE `codtvia`='CL001';
UPDATE `ttipvia` SET `abbrtvia`='Jr.' WHERE `codtvia`='JR001';
UPDATE `ttipvia` SET `abbrtvia`='Mal.' WHERE `codtvia`='ML001';
UPDATE `ttipvia` SET `abbrtvia`='Psj.' WHERE `codtvia`='PJ001';
UPDATE `ttipvia` SET `abbrtvia`='Prol.' WHERE `codtvia`='PR001';
UPDATE `ttipvia` SET `abbrtvia`='Pas.' WHERE `codtvia`='PS001';
UPDATE `ttipvia` SET `abbrtvia`='Sec.' WHERE `codtvia`='SC001';
UPDATE `ttipvia` SET `abbrtvia`='Av.' WHERE `codtvia`='TV001';


INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('188', 'TP004', 'NextPay');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('218', 'TP004', 'NextPay');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('591', 'TP004', 'NextPay');

UPDATE `ttipmoneda` SET `simbolo`='¢', `codint`='CRC' WHERE `idtmon`='6';
UPDATE `ttipmoneda` SET `simbolo`='$', `codint`='DOL' WHERE `idtmon`='3';
UPDATE `ttipmoneda` SET `simbolo`='B/.', `codint`='PAB' WHERE `idtmon`='8';

UPDATE `tpais` SET `nomniv1`='Estado', `nomniv2`='Ciudad', `nomniv3`='Distrito' WHERE `codpais`='188';
UPDATE `tpais` SET `nomniv1`='Estado', `nomniv2`='Ciudad', `nomniv3`='Distrito' WHERE `codpais`='218';
UPDATE `tpais` SET `nomniv1`='Estado', `nomniv2`='Ciudad', `nomniv3`='Distrito' WHERE `codpais`='591';


-- Date: 2013-12-21 10:08

INSERT INTO `tayuda` (`idayuda`,`pregunta`,`respuesta`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) VALUES (8,'¿Cómo se que estoy en mi semana de activación?','Porque en la oficina virtual aparece semana 0.','I',1,'2013-12-16 10:57:09',1,'2013-12-16 11:40:28',NULL,NULL);
INSERT INTO `tayuda` (`idayuda`,`pregunta`,`respuesta`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) VALUES (9,'¿Cómo llego a un rango?','Acumulando el puntaje requerido, si solo si estas activo y calificado, y respetando los requisitos necesarios indicados en el plan de evolución.','A',1,'2013-12-16 11:11:06',NULL,NULL,NULL,NULL);
INSERT INTO `tayuda` (`idayuda`,`pregunta`,`respuesta`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) VALUES (10,'¿Desde qué momento empiezo a cobrar?','Desde que afilias a alguien (bono de patrocinio/bono compra por patrocinio) o cuando alcanzas un rango (bono por rango binario).','A',1,'2013-12-16 11:11:23',NULL,NULL,NULL,NULL);
INSERT INTO `tayuda` (`idayuda`,`pregunta`,`respuesta`,`vchestado`,`vchusucrea`,`tmsfeccrea`,`vchusumodif`,`tmsfecmodif`,`vchequipo`,`vchprograma`) VALUES (11,'¿Qué debo hacer para cobrar?','
    Para cobrar sus comisiones generadas deberá aperturar su nº de RUC en la SUNAT como facturas, las cuales deben ser de:
    -Tercera Categoría
    -Régimen General
    -Actividad: Comisión Mercantil
    Para así, presentarnos la factura girada por el monto que aparece en su Reporte de Comisiones.
    Si usted tiene más de 700 soles por cobrar deberá aperturar también una cuenta de DETRACCION en el Banco de la Nación, nosotros realizamos los pagos de dos formas:
    -POR CHEQUE (Son ch','A',1,'2013-12-16 11:11:42',NULL,NULL,NULL,NULL);




-- Date: 2013-12-30
UPDATE `ttipproducto` SET `flagview`='2', `picture`='prodclave.png', `hexcolor`='60378C' WHERE `codtpro`='09';

UPDATE `ttipproducto` SET `abrtpro`='SISTEMA INMUNOLÓGICO' WHERE `codtpro`='02';



-- Date:  2014-01-02 
UPDATE `ttipproducto` SET `picture2`='bajarPeso.gif' WHERE `codtpro`='01';
UPDATE `ttipproducto` SET `picture2`='antiEdad.gif' WHERE `codtpro`='03';
UPDATE `ttipproducto` SET `picture2`='inmunologico.gif' WHERE `codtpro`='02';
UPDATE `ttipproducto` SET `picture2`='kids.gif' WHERE `codtpro`='08';
UPDATE `ttipproducto` SET `picture2`='vigor.gif' WHERE `codtpro`='04';
UPDATE `ttipproducto` SET `picture2`='muscular.gif' WHERE `codtpro`='05';
UPDATE `ttipproducto` SET `picture2`='pclave.gif' WHERE `codtpro`='09';
UPDATE `ttipproducto` SET `picture2`='mujer.gif' WHERE `codtpro`='07';



-- Date:  2014-01-03 
UPDATE `ttipproducto` SET `hexcolor`='307DD5' WHERE `codtpro`='22';
UPDATE `ttipproducto` SET `hexcolor`='60378C' WHERE `codtpro`='21';
UPDATE `ttipproducto` SET `hexcolor`='E1C92C' WHERE `codtpro`='20';
UPDATE `ttipproducto` SET `hexcolor`='BA2121' WHERE `codtpro`='10';
UPDATE `ttipproducto` SET `hexcolor`='663872' WHERE `codtpro`='61';
UPDATE `ttipproducto` SET `hexcolor`='219258' WHERE `codtpro`='62';






-- Date:  2014-01-06 
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('170', 'TP004', 'PagosOnline');

UPDATE `ttipmoneda` SET `simbolo`='$', `codint`='COP' WHERE `idtmon`='4';

UPDATE `tpais` SET `nomniv1`='Departamento', `nomniv2`='Ciudad', `nomniv3`='Distrito' WHERE `codpais`='170';


INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('218', 'TP005', 'Fuxion Ecuador');



UPDATE `ttipproducto` SET `line`='1' WHERE `codtpro`='01';
UPDATE `ttipproducto` SET `line`='1' WHERE `codtpro`='02';
UPDATE `ttipproducto` SET `line`='1' WHERE `codtpro`='03';
UPDATE `ttipproducto` SET `line`='1' WHERE `codtpro`='04';
UPDATE `ttipproducto` SET `line`='1' WHERE `codtpro`='05';
UPDATE `ttipproducto` SET `line`='1' WHERE `codtpro`='07';
UPDATE `ttipproducto` SET `line`='1' WHERE `codtpro`='08';
UPDATE `ttipproducto` SET `line`='1' WHERE `codtpro`='09';
UPDATE `ttipproducto` SET `catalog`='1' WHERE `codtpro`='10';
UPDATE `ttipproducto` SET `catalog`='1' WHERE `codtpro`='20';
UPDATE `ttipproducto` SET `catalog`='1' WHERE `codtpro`='21';
UPDATE `ttipproducto` SET `catalog`='1' WHERE `codtpro`='22';
UPDATE `ttipproducto` SET `catalog`='1' WHERE `codtpro`='61';
UPDATE `ttipproducto` SET `catalog`='1' WHERE `codtpro`='62';


-- Date:  2014-01-08
UPDATE `tpais_to_tipopago` SET `codtpag`='TP004' WHERE `codpais`='218' and`codtpag`='TP005';
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('068', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('068', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('152', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('152', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('170', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('170', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('188', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('188', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('218', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('218', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('484', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('484', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('591', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('591', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('604', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('604', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('840', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('840', 'TP006', 'Depósito Bancario');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('862', 'TP005', 'Pago en oficina');
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`) VALUES ('862', 'TP006', 'Depósito Bancario');


UPDATE `tpais_to_tipopago` SET `viewlanding`='1' WHERE `codpais`='170' and`codtpag`='TP004';
UPDATE `tpais_to_tipopago` SET `viewlanding`='1' WHERE `codpais`='188' and`codtpag`='TP004';
UPDATE `tpais_to_tipopago` SET `viewlanding`='1' WHERE `codpais`='218' and`codtpag`='TP004';
UPDATE `tpais_to_tipopago` SET `viewlanding`='1' WHERE `codpais`='591' and`codtpag`='TP004';
UPDATE `tpais_to_tipopago` SET `viewlanding`='1' WHERE `codpais`='604' and`codtpag`='TP004';
UPDATE `tpais_to_tipopago` SET `viewlanding`='1' WHERE `codpais`='604' and`codtpag`='TP011';


-- Date:  2014-01-23
INSERT INTO `tpais_to_tipopago` (`codpais`, `codtpag`, `nombre`, `tmsfecmodif`, `viewlanding`) VALUES ('591', 'TP004', 'PAGO EN LINEA T. CREDITO', '2014-01-23 17:01:45', '1');

UPDATE `ttipmoneda` SET `simbolo`='Bs', `codint`='BOB' WHERE `idtmon`='2';


UPDATE `tpais` SET `nomniv1`='Departamento', `nomniv2`='Ciudad', `nomniv3`='Distrito' WHERE `codpais`='068';


--Date:   2014-02-12


UPDATE `tpais`
SET `nomniv1`='Departamento',`nomniv2`='Provincia',`nomniv3`='Municipio'
 where `nompais`='BOLIVIA';

UPDATE `tpais`
SET `nomniv1`='Region',`nomniv2`='Provincia',`nomniv3`='Comuna'
 where `nompais`='CHILE';

UPDATE `tpais`
SET `nomniv1`='Departamento',`nomniv2`='Provincia',`nomniv3`=null
 where `nompais`='COLOMBIA';

UPDATE `tpais`
SET `nomniv1`='Provincia',`nomniv2`='Canton',`nomniv3`='Distrito'
 where `nompais`='COSTA RICA';

UPDATE `tpais`
SET `nomniv1`='Estado',`nomniv2`='Municipio',`nomniv3`=null
 where `nompais`='VENEZUELA';
UPDATE `dbfuxion_desa`.`tpais`
SET `nomniv1`='State',`nomniv2`='County',`nomniv3`=null
 where `nompais`='USA';
UPDATE `tpais`
SET `nomniv1`='Provincia',`nomniv2`='Distrito',`nomniv3`= 'Corregimiento'
 where `nompais`='PANAMA';

UPDATE `tpais`
SET `nomniv1`='Estado',`nomniv2`='Ciudad',`nomniv3`= 'Municipio'
 where `nompais`='MEXICO';

UPDATE `tpais`
SET `nomniv1`='Region',`nomniv2`='Provincia',`nomniv3`= 'Canton'
where `nompais`='ECUADOR';


-- Date:  2014-02-14  UBIGEO MEXICO: CIUDAD DE MEXICO
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160000', '484', 'CIUDAD DE MEXICO', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900000000');


INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160001', '484', 'ALVARO OBREGON', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160002', '484', 'AZCAPOTZALCO', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160003', '484', 'BENITO JUAREZ', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160004', '484', 'COYOACAN', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160005', '484', 'CUAJIMALPA DE MORELOS', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160006', '484', 'GUSTAVO A. MADERO', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160007', '484', 'IZTACALCO', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160008', '484', 'IZTAPALAPA', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160009', '484', 'LA MAGDALENA CONTRERAS', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160010', '484', 'MIGUEL HIDALGO', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160011', '484', 'MILPA ALTA', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160012', '484', 'TLAHUAC', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160013', '484', 'TLALPAN', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160014', '484', 'VENUSTIANO CARRANZA', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');
INSERT INTO `tubigeo` (`codubig`, `codpais`, `desubig`, `fleubig`, `ivaubig`, `vchestado`, `tmsfecmodif`, `codupar`) 
VALUES ('0900160015', '484', 'XOCHIMILCO', '0.00', '0.16', 'A', '2014-02-14 14:39:31', '0900160000');


UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900150000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900140000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900130000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900120000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900100000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900110000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900070000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900090000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900080000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900050000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900040000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900060000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900030000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900010000' and`codpais`='484';
UPDATE `tubigeo` SET `codupar`='-1' WHERE `codubig`='0900020000' and`codpais`='484';


UPDATE `tpais` SET `nomniv1`='State', `nomniv2`='City', `nomniv3`='Condado' WHERE `codpais`='840';

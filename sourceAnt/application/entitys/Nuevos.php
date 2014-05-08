<?php
/**
 * Description of Querys
 *
 * @author marrselo
 */
class Application_Entity_Nuevos {
       
    const TIPOTRANSPORTE='nuevotipotransporte';
    const AGENCIA='nuevaagencia';
    const SUCURSAL='nuevasucursal';
    const RUTA='nuevaruta';
    const TRANSPORTE='nuevotransporte';
    const CIUDAD='nuevaciudad';
    const PAIS='nuevopais';
    const COMPANYRENT='nuevacompanyrent';
    const AUTO='nuevoauto';
    const CATEGORIA='nuevacategoria';
    const OTRACATEGORIA='nuevaotracategoria';
    const RENTCAR='nuevorentcar';
    /**
     * Funcion para seleccionar el formulario a mostrar en el modal     
     * @param string $tipoNuevo 
     */
    static function selectForm($tipoNuevo)
    {
        switch ($tipoNuevo) {
            case self::TIPOTRANSPORTE :                 
                $form= new Application_Form_TipoTransporte();
                break;
            case self::AGENCIA :
                $form=new Application_Form_Agencia();
                break;
            case self::RUTA :
                $form=new Application_Form_Ruta();
                break;
            case self::SUCURSAL :
                $form=new Application_Form_Sucursal();
                break;
            case self::TRANSPORTE :
                $form=new Application_Form_Transporte();
                break;
            case self::CIUDAD :
                $form=new Application_Form_Ciudad();
                break;
            case self::PAIS :
                $form=new Application_Form_Pais();
                break;
            case self::AUTO:
                $form=new Application_Form_Auto();
                break;
            case self::COMPANYRENT:
                $form=new Application_Form_CompanyRent();
                break;
            case self::CATEGORIA:
                $form=new Application_Form_Categoria();
                break;
            case self::OTRACATEGORIA :
                $form=new Application_Form_OtraCategoria();
                break;
            case self::RENTCAR :
                $form=new Application_Form_RentCar();
                break;
            default:
                $form=null;
                break;
            
        }
        return $form;
    }
    
    static function setNameTable($tipoNuevo)
    {
        switch ($tipoNuevo) {
            case self::TIPOTRANSPORTE : 
                $nameTable=new Application_Entity_RunSql(
                    Application_Model_DbTable_TipoTransporte::nameTable);
                break;
            case self::AGENCIA : 
                $nameTable=new Application_Entity_RunSql(
                Application_Model_DbTable_Agencia::nameTable);
                break;
            case self::RUTA :
                $nameTable=new Application_Entity_RunSql(
                Application_Model_DbTable_Ruta::nameTable);
                break;
            case self::SUCURSAL :
                $nameTable=new Application_Entity_RunSql(
                Application_Model_DbTable_Sucursal::nameTable);
                break;
            case self::CIUDAD :
                $nameTable=new Application_Entity_RunSql(
                        Application_Model_DbTable_Ciudad::nameTable);
                break;
            case self::PAIS :
                $nameTable=new Application_Entity_RunSql(
                        Application_Model_DbTable_Pais::nameTable);
                break;
            case self::COMPANYRENT :
                $nameTable=new Application_Entity_RunSql(
                        Application_Model_DbTable_CompanyRent::nameTable);
                break;
            case self::AUTO :
                $nameTable=new Application_Entity_RunSql(
                        Application_Model_DbTable_Auto::nameTable);
                break;
            case self::CATEGORIA :
            case self::OTRACATEGORIA:
                $nameTable=new Application_Entity_RunSql(
                Application_Model_DbTable_CategoriaActividad::nameTable);
                break;   
            case self::RENTCAR:
                $nameTable=new Application_Entity_RunSql(
                Application_Model_DbTable_RentCar::nameTable);
                break;
            default:
                $nameTable= new stdClass();
                break;
        }
        return $nameTable;
    }
    
    static function setArrayValues($tipoNuevo,$params)
    {
        switch($tipoNuevo){
            case self::TIPOTRANSPORTE : 
               $array=array('id'=>array(),
                'data'=>array('nombreTipoTransporte'=>$params['nombre'],
                              'abreviaturaTipoTransporte'=>$params['abrev']));
               break;
            case self::AGENCIA :               
               $array=array('id'=>array(),
                'data'=>array('nombreAgencia'=>$params['nombre'],
                              'url_foto'=>$params['url_foto']));
               break;
            case self::RUTA :
                $array=array('id'=>array(),
                'data'=>array('ciudadOrigen'=>$params['ciudadOrigen'],
                    'ciudadDestino'=>$params['ciudadDestino'],
                    'idPais'=>$params['pais']));
                break;
            case self::SUCURSAL :
                $array=array('id'=>array(),
                    'data'=>array('ciudadSucursal'=>$params['ciudadSucursal'],
                        'longitud'=>$params['longitud'],
                        'latitud'=>$params['latitud'],
                        'direccion'=>$params['direccion'],
                        'url_foto'=>$params['url_foto'],
                        'nombreSucursal'=>$params['nombreSucursal'],
                        'idAgencia'=>$params['agencia']));
                break;
            case self::TRANSPORTE :
                $array=array('id'=>array(),
                    'data'=>array());
                break;
             case self::PAIS :
                $arrId=empty($params['idPais'])?array() : 
                    array('idPais'=>$params['idPais']);
                $array=array('id'=>$arrId,
                    'data'=>array('nombrePais'=>$params['nombrePais'],
                        'abreviatura'=>$params['abreviatura'],
                        'idObjetoFotografia'=>$params['idObjeto'],
                        'idContinente'=>$params['idContinente']                        
                     ));
                break;
            case self::CIUDAD :
                $arrId=empty($params['idCiudad'])?array() : 
                    array('idCiudad'=>$params['idCiudad']);
                $array=array('id'=>$arrId,
                    'data'=>array('idPais'=>$params['pais'],
                        'nombreCiudad'=>$params['nombreCiudad'],
                        'descripcionCiudad'=>$params['descripcionCiudad'],
                        'descripcionESCiudad'=>$params['descripcionCiudad'],
                        'descripcionENCiudad'=>$params['descripcionENCiudad'],
                        'idObjetoFotografia'=>$params['idObjeto'],
                     ));
                break;
            case self::COMPANYRENT :
                $arrId=empty($params['idCompanyRent'])?array() : 
                    array('idCompanyRent'=>$params['idCompanyRent']);
                $array=array('id'=>$arrId,
                    'data'=>array('nombreCompanyRent'=>$params['nombreCompanyRent'],                        
                        'idCiudad'=>$params['idCiudad'],
                        'descripcion'=>$params['descripcion'],
                        'urlImagen'=>$params['rutaImagen']
                     ));
            break;
            case self::AUTO :
                $arrId=empty($params['idAuto'])?array() : 
                    array('idAuto'=>$params['idAuto']);
                $array=array('id'=>$arrId,
                    'data'=>array('idCompanyRent'=>$params['idCompanyRent'],                        
                        'marca'=>$params['marca'],
                        'modelo'=>$params['modelo'],
                        'urlImagen'=>$params['rutaImagen'],                       
                        'anio'=>$params['anio'],
                        'idTipoAuto'=>$params['idTipoAuto']
                     ));
            break;
            case self::CATEGORIA :
            case self::OTRACATEGORIA:
                $arraydev=array(                        
                        'NombreCategoria'=>$params['NombreCategoria'],
                        'DescripcionCategoria'=>$params['DescripcionCategoria']);
                if(isset($params['idCategoriaPadre'])){
                    $arraydev['idCategoriaPadre']=$params['idCategoriaPadre'];
                }
                $array=array('id'=>array(),
                    'data'=>$arraydev
                     );
            break;
            case self::RENTCAR:
                $arrId=empty($params['idRentCar'])?array() : 
                    array('idRentCar'=>$params['idRentCar']);
                $array=array('id'=>$arrId,
                    'data'=>array('idCompanyRent'=>$params['idCompanyRent'],                        
                        'totalxdia'=>$params['totalxdia'],
                        'idProducto'=>$params['idProducto'],
                        'idTipoTransporte'=>$params['idTipoTransporte'],                       
                        'idAuto'=>$params['idAuto']
                     ));
                break;
                
            default : $array=array('');
                
        }
        return $array;
    }    
}
/*idRentCar
idCompanyRent
totalxdia
titulo
idProducto
idTipoTransporte
idAuto*/

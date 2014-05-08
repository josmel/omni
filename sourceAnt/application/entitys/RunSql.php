<?php
/*
 * Operations basic for make CRUD of tables
 * @author Marcelo Carranza 
 */
class Application_Entity_RunSql extends Core_Db_Table
{
    /** 
     * nombre tabla
     * @var type string
     */   
    protected  $_name ;
    /**
     * array(id=>array('idtable'=>'value'),data=>array('nameCamp'=>'value'))
     */
    const INSERT = 'insert';
    /**
     * array('id'=>array('idtable'=>'value'),'data'=>array('nameCamp'=>'value'))
     */
    const UPDATE = 'update';
    const LISTED = 'listed';
    const GETONE = 'getone';
    /**
     * array('id'=>array('idtable'=>'value'))
     */
    const DELETE = 'erase';  
    /**
     * Colección de operacion sql (insertar, update,listar,delete,getone)
     * @var type array
     */
    private $_operations = array('insert','update','listed','getone','erase');
    /**
     * Localización de los datos sobrecargados
     * @var type array
     */
    private $data = array();
    
    public function __construct($nameTable)
    {    
      $this->_name=$nameTable;
    }
    
    public function __set($name,$value)
    {
        if(in_array($name,$this->_operations))
        {          
            $objTable=$this->factoryTable(); 
            $namePrimay=array();
            switch ($name) {
                case self::INSERT :                     
                    $objTable->insert($value['data']);                    
                    $this->data[$name]=$objTable->getAdapter()->lastInsertId();                    
                    break;
                case self::DELETE :
                    $namePrimay = each($value['id']);                     
                    $where=$objTable->getAdapter()->quoteInto($namePrimay[0].'=?',$namePrimay[1]);                    
                    $objTable->delete($where);
                    break;
                case self::LISTED :
                    $smt = $objTable->select()->query();
                    $result = $smt->fetchAll();                                      
                    $this->data[$name]=$result;
                    $smt->closeCursor();
                    break;
                case self::UPDATE :                                        
                    $namePrimay = each($value['id']);
                    $where=$objTable->getAdapter()->quoteInto($namePrimay[0].'=?',$namePrimay[1]);  
                    $objTable->update($value['data'],$where);
                    break;
                case self::GETONE : 
                    $namePrimay = each($value['id']);                      
                    $where=$objTable->getAdapter()->quoteInto($namePrimay[0].'=?',$namePrimay[1]); 
                    $smt = $objTable->select()
                            ->where($where)
                            ->query();
                    $result = $smt->fetch();                    
                    $this->data[$name]= $result;
                    $smt->closeCursor();
                    break;
                default:                    
                    $this->data[$name] = null ;
                    break;
            }
        }
    }
        
    public function __get($name) {        
        return $this->data[$name];
    }
    
    public function factoryTable()
    {
        switch($this->_name){
            case 'TipoTransporte' : $objTable= new Application_Model_DbTable_TipoTransporte;                
                break;     
            case 'Agencia' : $objTable = new Application_Model_DbTable_Agencia();                
                break;            
            case 'Ruta' : $objTable=new Application_Model_DbTable_Ruta();
                break;
            case 'Sucursal' : $objTable=new Application_Model_DbTable_Sucursal();
                break;
            case 'Pais':$objTable=new Application_Model_DbTable_Pais();
                break;
            case 'Ciudad': $objTable=new Application_Model_DbTable_Ciudad();                
                break;
            case 'CompanyRent': $objTable=new Application_Model_DbTable_CompanyRent();
                break;
            case 'Auto' :$objTable= new Application_Model_DbTable_Auto();
                break;
            case 'CategoriaActividad' : $objTable=new Application_Model_DbTable_CategoriaActividad();
                break;
            case 'Actividad' : $objTable=new Application_Model_DbTable_Actividad();
                break;  
            case 'Transporte' : $objTable=new Application_Model_DbTable_Transporte();
                break;               
            default : $objTable = new stdClass();
        }
        return $objTable;
    }
    
    
}

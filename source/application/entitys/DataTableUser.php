<?php
/**
 * Description of Querys
 *
 * @author marrselo
 */
class Application_Entity_DataTableUser {
       
    protected $_tableName;
    protected $_objTable;
    protected $_columnDisplay;
    protected $_columnSearch;
    protected $_dtwhere = '';
    protected $_flagActive;
    protected $_primaryKey;
    protected $_numberPage;
    protected $_limit;
    protected $_newIcon=null;
    
    public function __construct($nameTable,$limit,$numberPage,$flagActive=true) {
        $this->_tableName=$nameTable;
        $this->_objTable=  $this->factoryTable();
        $this->_columnDisplay=$this->getColumnDisplay();
      //  $this->_dtwhere=$this->setSearch();
        $this->_flagActive=$flagActive;
        $this->_primaryKey=$this->_objTable->getPrimaryKey();
        $this->_numberPage=$numberPage;
        $this->_limit=" LIMIT $limit ";
        
    }
    
    public function setSearch($sSearch)
    {
        
       return  $this->_dtwhere = !empty($sSearch)? " AND (nameUser like '%".$sSearch."%' 
            OR email like '%".$sSearch."%' ) " :''; 
        
    }
    
    public function factoryTable()
    {
        $strinNameTable='Application_Model_DbTable_'.$this->_tableName;
        return $objTable = new $strinNameTable();
    }
 
    public function defineWhereDisplay()
    {

    }
     public function getColumnDisplay()
    {
        return $this->_objTable->columnDisplay();
    }
    
    public function getQuery($displayStart=null,$displayLength=null)
    {       
	if (!empty($displayStart) && $displayLength != '-1' )
	{
		$this->_limit = " LIMIT ".intval($displayStart).", ".
			intval($displayLength);
	}
        $whereActive=($this->_flagActive==true)?$this->_objTable->getWhereActive():'';
        $id=$this->_primaryKey;
        $query="
            SELECT SQL_CALC_FOUND_ROWS ".$id.", ".str_replace(" , ", " ",
                implode(", ",$this->_columnDisplay))."
            FROM 
            ".$this->_tableName."
            INNER JOIN AgeSpan ON AgeSpan.idAgeSpan=User.idAgetSpan
            WHERE 1 ".
            $whereActive.
            $this->_dtwhere
            .$this->_limit;
      // echo $query; exit;
        $smt = $this->_objTable->getAdapter()->query($query);
  
        $output = array(
                'sEcho' =>intval($this->_numberPage),  
                 'iTotalRecords'=> 0,
                 'iTotalDisplayRecords'=>0,
                 'aaData' => array()
                 );
         while ( $aRow = $smt->fetch() )
         {
            $row = array();
            $row[0]=$aRow['nameUser'];
            $row[1]=$aRow['email'];
            $row[2]=$aRow['nameUserType'];
            $row[3]=$aRow['flagAct'];
            $row[4]=$aRow['lastUpdate'];
            $row[5]="
                <a class=\"action\" onclick=\"deleteRow(".$aRow[$id].")\">
               <span class=\"icon icon-color icon-trash\" 
                     title=\"Eliminar ".$aRow[$this->_columnDisplay[0]]."\">                                    
               </span>                                   
                </a>
                <a class=\"action\"  onclick=\"editRow(".$aRow[$id].")\">
                 <span class=\"icon icon-color icon-edit\" 
                       title=\"Editar ".$aRow[$this->_columnDisplay[0]]."\">                                    
                 </span>                                   
            </a> ".str_replace("__ID__",$aRow[$id],$this->_newIcon);         
            
            // Add the row ID and class to the object
            $row['DT_RowId'] = 'row_'.$aRow[$id];
            $output['aaData'][] = $row;
         }         
         $total=$smt->getAdapter()->fetchOne('SELECT FOUND_ROWS()');
         $output['iTotalRecords']=$total;
         $output['iTotalDisplayRecords']=$total;              
         $smt->closeCursor();
         return $output;
    }
    public function setNewIcon($stringHTML)
    {
        return $this->_newIcon=$stringHTML;
    }
    
    public function setNameTable($newNameTable)
    {
        $this->_tableName=$newNameTable;
    }
}


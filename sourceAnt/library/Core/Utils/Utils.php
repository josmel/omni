<?php
/**
 * Description of Utils
 *
 * @author Marrselo
 */
class Core_Utils_Utils {
   /**
    * 
    * @param array $array
    * @param int $keyDos nroColumna del que se extrae el segundo parametro
    * @return array
    */
    static function fetchPairs($array,$nroCol=null){
        $arrayResponse = array();
        $nroCol=!empty($nroCol)?$nroCol:1;
        foreach($array as $index => $datos){
            $keys = array_keys($datos);
            $arrayResponse[$datos[$keys[0]]]=$datos[$keys[$nroCol]];
        }
        return $arrayResponse;
    }
    //put your code here
    
    /**
     * 
     * @param array $array resultado de una consulta    
     * @return array
     */
    static function fetchPairsConcat($array)
    {
        $arrayResponse = array();                
        foreach($array as $index => $datos){
            $keys = array_keys($datos);            
            $arrayResponse[$datos[$keys[0]]]=$datos[$keys[1]].'-'.$datos[$keys[2]];
        }
        return $arrayResponse;
    }
    
    /**
     * Concatena columna 2 y 3
     * @param type $array
     * @return string
     */
    static function fetchPairsConcat23($array)
    {
        $arrayResponse = array();                
        foreach($array as $index => $datos){
            $keys = array_keys($datos);            
            $arrayResponse[$datos[$keys[0]]]=$datos[$keys[2]].'-'.$datos[$keys[3]];
        }
        return $arrayResponse;
    }
}



<?php
/**
 * Description of Querys
 *
 * @author Laptop
 */
class Application_Entity_Dashboard {
    //put your code here
    
    static function countUser()
    {
      $model = new Application_Model_Usuario;
      return $model->countUser();
    }
    static  function countRentCar()
    {
        $model = new Application_Model_Historial();
        return $model->countProductoTipo(
            Application_Model_DbTable_Producto::RENTCAR);
    }
    static function countTransport()
    {
        $model = new Application_Model_Historial();
        return $model->countProductoTipo(
            Application_Model_DbTable_Producto::TRANSPORTE);
    }
    static function countActivities()
    {
        $model = new Application_Model_Historial();
        return $model->countProductoTipo(
            Application_Model_DbTable_Producto::ACTIVITIE);
    }
    
}

?>

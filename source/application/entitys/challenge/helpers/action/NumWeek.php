<?php

class Challenge_Action_Helper_NumWeek extends Zend_Controller_Action_Helper_Abstract {
    
    public function numWeek($date1, $date2, $exact = FALSE) 
    { 
        $first = DateTime::createFromFormat('Y-m-d', $date1); 
        $second = DateTime::createFromFormat('Y-m-d', $date2); 
        if($date1 > $date2) return numWeek($date2, $date1); 
        $day = $first->diff($second)->days%7; 
        $week = floor($first->diff($second)->days/7); 
        if(($day > 0) && $exact) $week++; 
        return $week; 
    } 
    //var_dump(datediffInWeeks('1/2/2013', '1/14/2013', TRUE));
}

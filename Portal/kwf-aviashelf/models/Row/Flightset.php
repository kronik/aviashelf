<?php
class Row_Flightset extends Kwf_Model_Db_Row
{
    public function __get($name)
    {
        if ($name === 'setEndDateEx')
        {
            $endDateDate = parent::__get('setEndDate');
            
            if ($endDateDate == NULL) {
                return $endDateDate;
            }
            
//            $dateStr = $endDateDate->format('d-m-Y');

            return $endDateDate . '|' . (parent::__get('finished') ? "1" : "0");
        }
        return parent::__get($name);
    }
    
    public function __set($name, $value)
    {
        if ($name === 'setEndDateEx')
        {
        }
        else
        {
            return parent::__set($name, $value);
        }
    }
}

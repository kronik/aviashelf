<?php
class Row_Employee extends Kwf_Model_Db_Row
{
    protected $_cacheImages = array(
        'default' => array(120, 0)
    );

    public function __toString()
    {
        return $this->lastname . ' ' . substr($this->firstname, 0, 2) . '. ' . substr($this->middlename, 0, 2) . '.';
    }
    
    public function __get($name)
    {
        if ($name === 'totalTimeStr')
        {
            $timeInMinutes = parent::__get('totalTimeInMinutes');
            $this->totalTimeStr = str_pad ((int)($timeInMinutes / 60), 2, "0", STR_PAD_LEFT) . ':' . str_pad (($timeInMinutes % 60), 2, "0", STR_PAD_LEFT);
        }
        return parent::__get($name);
    }
    
    public function __set($name, $value)
    {
        if ($name === 'totalTimeStr')
        {
            $timeParts = explode(":", $value);
            
            try
            {
                $hours = intval($timeParts[0]);
                
                
                $minutes = 0;
                
                if (count($timeParts) > 1)
                {
                    $minutes = intval($timeParts[1]);
                }
                                
                parent::__set('totalTimeInMinutes', $hours * 60 + $minutes);
            } catch (Exception $e)
            {
            }
            parent::__set('totalTimeStr', $value);
        }
        else
        {
            return parent::__set($name, $value);
        }
    }
}

<?php
class Row_Flightplan extends Kwf_Model_Db_Row
{
    public function __toString()
    {
        return $this->planDate . ': ' . $this->employeeName;
    }
}

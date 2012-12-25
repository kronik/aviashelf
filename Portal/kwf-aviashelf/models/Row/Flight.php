<?php
class Row_Flight extends Kwf_Model_Db_Row
{
    public function __toString()
    {
        return $this->number . ' ' . $this->subCompanyName . ' ' . $this->planeName . ' ' . $this->routeName;
    }
}

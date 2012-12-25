<?php
class Row_Airplane extends Kwf_Model_Db_Row
{
    public function __toString()
    {
        return $this->State . '-' . $this->Number;
    }
}

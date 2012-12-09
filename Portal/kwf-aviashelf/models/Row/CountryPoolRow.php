<?php
class CountryPoolRow extends Kwf_Db_Table_Row_Abstract
{
    public function __toString()
    {
        var_dump($this->Name);

        return $this->Name;
    }
}
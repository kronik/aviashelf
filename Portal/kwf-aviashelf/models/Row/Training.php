<?php
class Row_Training extends Kwf_Model_Db_Row
{
    public function __toString()
    {
        return $this->type . ': ' . $this->title;
    }
}

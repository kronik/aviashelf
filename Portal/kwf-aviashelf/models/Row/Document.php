<?php
class Row_Document extends Kwf_Model_Db_Row
{
    public function __toString()
    {
        return $this->typeName . ' #' . $this->number . ' (' . $this->startDate . ')';
    }
}

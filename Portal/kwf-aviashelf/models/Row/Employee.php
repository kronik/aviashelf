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
}

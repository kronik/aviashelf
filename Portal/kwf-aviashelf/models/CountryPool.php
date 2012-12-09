<?php
class CountryPool extends Kwf_Db_Table
{
    protected $_name = 'countries';
    protected $_rowClass = 'CountryPoolRow';
    
    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
        #$filter->setGroupBy('name');
        $this->_filters = array('id' => $filter);
    }
    
    public function fetchPool($poolname, $order = 'id')
    {
        $return = array();
        foreach ($this->fetchAll() as $row)
        {
            var_dump($row);
            $return[$row->Name] = $row->Name;
        }
        return $return;
    }
}

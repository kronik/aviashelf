<?php
class FlightgroupsfilterController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Employees';

    protected function _initColumns()
    {            
        $this->_columns[] = new Kwf_Grid_Column('id');
        $this->_columns[] = new Kwf_Grid_Column('name');
    }
    
    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        
        if ($this->_getParam('positionId'))
        {
            $s = new Kwf_Model_Select();
            $s->whereEquals('groupId', $this->_getParam('positionId'));
            $ret->where(new Kwf_Model_Select_Expr_Child_Contains('EmployeeFlightRoles', $s))->whereEquals('groupType', '1')
            ->whereEquals('isOOO', false)
            ->whereEquals('isAllowed', '1')
            ->order('lastname');
        }
        
        return $ret;
    }
}

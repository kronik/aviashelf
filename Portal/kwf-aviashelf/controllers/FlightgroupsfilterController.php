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
//        $ret = parent::_getSelect();
//        if ($this->_getParam('id'))
//        {
//            $groupModel = Kwf_Model_Abstract::getInstance('EmployeeFlightRoles');
//            $groupSelect = $groupModel->select()->whereEquals('groupId', $this->_getParam('id'));
//
//            $ret->where(new Kwf_Model_Select_Expr_Child_Contains('EmployeeFlightRoles', $groupSelect));
//        }
//        return $ret;
        
        
        $ret = parent::_getSelect();
        
        if ($this->_getParam('positionId'))
        {
            $s = new Kwf_Model_Select();
            $s->whereEquals('groupId', $this->_getParam('positionId'));
            $ret->where(new Kwf_Model_Select_Expr_Child_Contains('EmployeeFlightRoles', $s));
        }
        
//        if ($this->_getParam('positionId'))
//        {
//            $ret->whereEquals('positionId', $this->_getParam('positionId'));
//        }
        
        return $ret;
    }
}

<?php
    require_once 'GridEx.php';

class StaffgroupsfilterController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Employees';

    protected function _initColumns()
    {
        parent::_initColumns();
        
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
            $ret->where(new Kwf_Model_Select_Expr_Child_Contains('EmployeeStaffRoles', $s));
        }
        
        return $ret;
    }
}

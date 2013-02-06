<?php
class FlightgroupsfilterController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        $dataSet = array();
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('visible', '1');

        $groupModel = Kwf_Model_Abstract::getInstance('EmployeeFlightRoles');

        $employees = $employeesModel->getRows($employeesSelect);
        
        foreach ($employees as $employee)
        {
            $groupSelect = $groupModel->select()->whereEquals('employeeId', $employee->id);
            $groups = $groupModel->getRows($groupSelect);
            
            foreach ($groups as $group)
            {
                array_push($dataSet, array('id'=>$employee->id, 'name'=>(string)$employee, 'positionId'=>$group->groupId));
            }
        }
                
        $this->_model = new Kwf_Model_FnF(array('data' => $dataSet));
            
        $this->_columns[] = new Kwf_Grid_Column('id');
        $this->_columns[] = new Kwf_Grid_Column('name');
        $this->_columns[] = new Kwf_Grid_Column('positionId');
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
            $ret->whereEquals('positionId', $this->_getParam('positionId'));
        }
        
        return $ret;
    }
}

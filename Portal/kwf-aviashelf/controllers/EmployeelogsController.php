<?php
class EmployeelogsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'EmployeeLogs';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 100;
//    protected $_buttons = NULL;
    
    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_filters = array('loginDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('loginDate');
        
        $this->_columns->add(new Kwf_Grid_Column_Datetime('loginDate', 'Дата входа', 200));
    }
    
    protected function _getWhere() {
        
        $ret = parent::_getWhere();
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('id', $this->_getParam('employeeId'));
        
        $employee = $employeesModel->getRow($employeesSelect);
        $userId = -1;
        
        if ($employee != NULL) {
            $userId = $employee->userId;
        }
        
        $ret['userId = ?'] = $userId;
        
        return $ret;
    }
}

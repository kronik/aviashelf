<?php
class EmployeetasksController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Tasks';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 300;
    protected $_buttons = array('add', 'delete');

    public function indexAction()
    {
        $this->view->ext('Tasks');
    }
    
    protected function _initColumns()
    {
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'admin' ||
            $users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
            
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            if ($users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
                
                unset($this->_buttons ['delete']);
            }
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/employeetask',
                                       'width' => 550,
                                       'height' => 440
                                       );
        } else {
            $this->_buttons = array();
        }

        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 200));
        $this->_columns->add(new Kwf_Grid_Column_Date('startDate', trlKwf('Start Date'), 100));
        $this->_columns->add(new Kwf_Grid_Column_Date('endDate', trlKwf('End Date'), 100))->setRenderer('taskCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('description', trlKwf('Description'), 500));
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
        
        $ret['status = ?'] = 0;
        $ret['userId = ?'] = $userId;
        return $ret;
    }
}

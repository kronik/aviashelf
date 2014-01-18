<?php
    require_once 'GridEx.php';

class MyresultsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'PersonResults';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 100;
    protected $_buttons = array('xls');
//    protected $_grouping = array('groupField' => 'trainingGroupName');

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Myresults');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
     
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));

        $this->_columns->add(new Kwf_Grid_Column('trainingGroupName', 'Группа'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('trainingName', 'Дисциплина'))->setWidth(300);
        $this->_columns->add(new Kwf_Grid_Column('startDate', trlKwf('Start Date')))->setWidth(80)->setRenderer('taskCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('endDate', trlKwf('End Date')))->setWidth(90)->setRenderer('taskCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('recordDate', 'Пройден'))->setWidth(90);
        $this->_columns->add(new Kwf_Grid_Column('currentScore', trlKwf('Score')))->setWidth(80)->setRenderer('highlightScore');
        $this->_columns->add(new Kwf_Grid_Column('totalScore', trlKwf('Total Score')))->setWidth(80);
        $this->_columns->add(new Kwf_Grid_Column('gradeName', trlKwf('Grade')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(400);
    }
    
    protected function _getWhere()
    {
        $users = Kwf_Registry::get('userModel');
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('userId', $users->getAuthedUserId());
        
        $employee = $employeesModel->getRow($employeesSelect);
        
        $ret = parent::_getWhere();

        $employeeId = -1;
        
        if ($employee != NULL)
        {
            $employeeId = $employee->id;
        }
        
        $ret['employeeId = ?'] = $employeeId;

        return $ret;
    }
}

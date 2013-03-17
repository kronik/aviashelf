<?php
class MyresultsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'TrainingResults';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 30;
    protected $_buttons = array();

    public function indexAction()
    {
        $this->view->ext('Myresults');
    }
    
    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('trainingName', trlKwf('Training')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('trainingGroupName', trlKwf('Group')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('currentScore', trlKwf('Score')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('totalScore', trlKwf('Total Score')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('gradeName', trlKwf('Grade')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);

    }
    
    protected function _getWhere()
    {
        $users = Kwf_Registry::get('userModel');
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('userId', $users->getAuthedUserId());
        
        $employee = $employeesModel->getRow($employeesSelect);
        
        $ret = parent::_getWhere();

        if ($employee != NULL)
        {
            $ret['employeeId = ?'] = $employee->id;
        }

        return $ret;
    }
}

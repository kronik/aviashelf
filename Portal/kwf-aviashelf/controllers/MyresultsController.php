<?php
class MyresultsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'TrainingResults';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 0;
    protected $_buttons = array();

//    public function indexAction()
//    {
//        $this->view->ext('Myresults');
//    }
    
    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('currentScore', trlKwf('Score')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('gradeName', trlKwf('Grade')))->setWidth(500);
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
            $ret['trainingGroupId = ?'] = $this->_getParam('groupId');
            $ret['employeeId = ?'] = $employee->id;
            $ret['currentScore = ?'] = 0;
        }

        return $ret;
    }
}

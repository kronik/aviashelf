<?php
    require_once 'GridEx.php';

class MytrialgroupsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'PersonResults';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 100;
    protected $_buttons = array();
    protected $_editDialog = NULL;

    public function indexAction()
    {
        parent::indexAction();
        $this->view->ext('Mytrialgroups');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_columns->add(new Kwf_Grid_Column('trainingGroupName', 'Группа'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('trainingName', 'Дисциплина'))->setWidth(400)->setRenderer('checkResultScore');
        $this->_columns->add(new Kwf_Grid_Column('startDate', trlKwf('Start Date')))->setWidth(80)->setRenderer('taskCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('endDate', trlKwf('End Date')))->setWidth(90)->setRenderer('taskCheckDate');
        
        $this->_columns->add(new Kwf_Grid_Column_Button('customButton', trlKwf('Action'), 100))
        ->setTooltip(trlKwf('Start the test'))
        ->setButtonIcon(new Kwf_Asset('application_go.png'));
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        
        $users = Kwf_Registry::get('userModel');
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('userId', $users->getAuthedUserId());
        
        $employee = $employeesModel->getRow($employeesSelect);
        $employeeId = -1;
        
        if ($employee != NULL)
        {
            $employeeId = $employee->id;
        }
        
        $ret['employeeId = ?'] = $employeeId;
        $ret['currentScore = ?'] = 0;
        $ret['isTrial = ?'] = 1;
        
        return $ret;
    }
    
    public function jsonCustomButtonAction() {
        $recordId = $this->getRequest()->getParam('groupId');
    }
}

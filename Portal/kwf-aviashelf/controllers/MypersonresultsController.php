<?php
    require_once 'GridEx.php';

class MypersonresultsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'PersonResults';
    protected $_defaultOrder = array('field' => 'trainingName', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array();
    protected $_editDialog = NULL;

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Mypersonresults');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_columns->add(new Kwf_Grid_Column('trainingName', 'Дисциплина'))->setWidth(300);
        $this->_columns->add(new Kwf_Grid_Column('totalScore', trlKwf('Total Score')))->setWidth(80);
        
        $this->_columns->add(new Kwf_Grid_Column_Button('customButton', trlKwf('Action'), 100))
        ->setTooltip(trlKwf('Start the test'))
        ->setButtonIcon(new Kwf_Asset('application_go.png'));
    }
    
    protected function _getWhere() {
        $ret = parent::_getWhere();
        
        $users = Kwf_Registry::get('userModel');
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('userId', $users->getAuthedUserId());
        
        $employee = $employeesModel->getRow($employeesSelect);
        
        $employeeId = -1;
        
        if ($employee != NULL) {
            $employeeId = $employee->id;
            $ret['employeeId = ?'] = $employeeId;
        }
        
        $ret['trainingGroupId = ?'] = $this->_getParam('groupId');
        return $ret;
    }
    
    public function jsonCustomButtonAction() {
        $recordId = $this->getRequest()->getParam('groupId');
    }
}

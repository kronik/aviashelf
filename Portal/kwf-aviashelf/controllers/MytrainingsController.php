<?php
class MytrainingsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Trainings';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_paging = 30;
    protected $_grouping = array('groupField' => 'type');
    protected $_buttons = array();

    public function indexAction()
    {
        $this->view->ext('Mytrainings');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number')))->setWidth(50);
        $this->_columns->add(new Kwf_Grid_Column('type', trlKwf('Type')))->setWidth(60);
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title')))->setWidth(300);
    }
    
    protected function _getSelect()
    {
        $ret = parent::_getSelect();

        $users = Kwf_Registry::get('userModel');
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('userId', $users->getAuthedUserId());

        $employee = $employeesModel->getRow($employeesSelect);
        $employeeId = -1;
        
        if ($employee != NULL)
        {
            $employeeId = $employee->id;
        }

        $s = new Kwf_Model_Select();
        $s->whereEquals('employeeId', $employeeId);
        $ret->where(new Kwf_Model_Select_Expr_Child_Contains('TrainingResults', $s));
        
        return $ret;
    }
}

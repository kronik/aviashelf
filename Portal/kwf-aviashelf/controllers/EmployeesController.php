<?php
    require_once 'GridEx.php';

class EmployeesController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Employees';
    protected $_defaultOrder = 'listPosition';
    protected $_paging = 100;
    protected $_buttons = array('add', 'delete');
    protected $_grouping = array('groupField' => 'subCompanyName');
    
    protected $_editDialog = array(
                                   'controllerUrl' => '/employee',
                                   'width' => 700,
                                   'height' => 730
                                   );

    public function indexAction()
    {
        parent::indexAction();
        $this->view->ext('Employees');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Lastname'), 80));
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('Firstname'), 80));
        $this->_columns->add(new Kwf_Grid_Column('middlename', trlKwf('Middlename'), 90));
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('isAllowed', trlKwf('Allowed'), 60));
        $this->_columns->add(new Kwf_Grid_Column('subCompanyName', 'Подразделение', 100));
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['groupType = ?'] = 1;
        return $ret;
    }
}

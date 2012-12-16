<?php
class EmployeesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Employees';
    protected $_defaultOrder = 'lastname';
    protected $_paging = 0;
    protected $_buttons = array('add');

    public function indexAction()
    {
        $this->view->ext('Employees');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Lastname'), 100));
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('Firstname'), 100));
        $this->_columns->add(new Kwf_Grid_Column('middlename', trlKwf('Middlename'), 100));
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('isAllowed', trlKwf('Allowed'), 50));
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('visible', trlKwf('Active'), 50));
    }
}

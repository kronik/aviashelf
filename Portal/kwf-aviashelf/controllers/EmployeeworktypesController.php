<?php
    require_once 'GridEx.php';

class EmployeeworktypesController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'EmployeeWorkTypes';
    protected $_defaultOrder = 'pos';
    protected $_paging = 100;
    protected $_buttons = array('add', 'delete');

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Employeeworktypes');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));

        $this->_columns->add(new Kwf_Grid_Column('pos', 'Позиция', 70));
        $this->_columns->add(new Kwf_Grid_Column('value', 'Название', 100));
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('needTime', 'Учитывать время', 100));
    }
}

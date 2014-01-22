<?php
    require_once 'GridEx.php';

class AirportsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Airports';
    protected $_defaultOrder = 'Name';
    protected $_paging = 30;
    protected $_buttons = array('add', 'delete');

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Airports');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('Name', trlKwf('Title'), 300));
    }
}

<?php
    require_once 'GridEx.php';

class AirportsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Airports';
    protected $_defaultOrder = 'Name';
    protected $_paging = 30;
    protected $_buttons = array('add');

    public function indexAction()
    {
        parent::indexAction();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->view->ext('Airports');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('Name', trlKwf('Title'), 300));
    }
}

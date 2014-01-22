<?php
    require_once 'GridEx.php';

class CompaniesController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Companies';
    protected $_defaultOrder = 'Name';
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');

    public function indexAction()
    {
        parent::indexAction();
        
        $users = Kwf_Registry::get('userModel');
        
        $this->view->ext('Companies');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('Name', trlKwf('Title'), 300));
    }
}

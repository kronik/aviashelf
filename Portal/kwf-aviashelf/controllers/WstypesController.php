<?php
    require_once 'GridEx.php';

class WstypesController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Wstypes';
    protected $_defaultOrder = 'Name';
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    
    public function indexAction()
    {
        parent::indexAction();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->view->ext('Wstypes');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('Name', trlKwf('Title'), 100));
        $this->_columns->add(new Kwf_Grid_Column('NameEn', trlKwf('English name'), 100));
        $this->_columns->add(new Kwf_Grid_Column('IKAO', trlKwf('IKAO'), 50));
        $this->_columns->add(new Kwf_Grid_Column('IATA', trlKwf('IATA'), 50));
    }
}

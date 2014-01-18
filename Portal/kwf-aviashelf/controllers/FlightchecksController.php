<?php
    require_once 'GridEx.php';

class FlightchecksController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightchecks';
    protected $_defaultOrder = 'title';
    protected $_paging = 100;
    protected $_buttons = array('add', 'delete');

    public function indexAction()
    {
        parent::indexAction();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }
        
        $this->view->ext('Flightchecks');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('title', 'Название', 160));
        $this->_columns->add(new Kwf_Grid_Column('times', 'Кол-во раз', 70));
        $this->_columns->add(new Kwf_Grid_Column('months', 'В месяцев', 70));
    }
}

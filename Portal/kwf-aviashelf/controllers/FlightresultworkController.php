<?php
class FlightresultworkController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightresultwork';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 1000;
    protected $_buttons = array('add', 'delete');

    public function indexAction()
    {
        $this->view->ext('Flightresultwork');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
            unset($this->_buttons ['add']);
        }
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_queryFields = array('workName', 'resultName');

        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        
        $this->_columns->add(new Kwf_Grid_Column('resultName', 'Налет', 100));
        $this->_columns->add(new Kwf_Grid_Column('workName', 'Наработка', 150));
    }
}

<?php
    require_once 'GridEx.php';

class AirplanesController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Airplanes';
    protected $_defaultOrder = 'Number';
    protected $_paging = 30;
    protected $_buttons = array('add');

    public function indexAction()
    {
        parent::indexAction();
        $this->view->ext('Airplanes');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('NBort', trlKwf('Bort Title'), 100));
        $this->_columns->add(new Kwf_Grid_Column('Mass', trlKwf('Weight'), 50));
        $this->_columns->add(new Kwf_Grid_Column('Center', trlKwf('Center Point'), 75));
        $this->_columns->add(new Kwf_Grid_Column('LotsNumber', trlKwf('Seats Number'), 70));
    }
}

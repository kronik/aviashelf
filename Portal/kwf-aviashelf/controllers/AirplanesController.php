<?php
class AirplanesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Airplanes';
    protected $_defaultOrder = 'Number';
    protected $_paging = 30;
    protected $_buttons = array('add');

    public function indexAction()
    {
        $this->view->ext('Airplanes');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('NBort', trlKwf('Bort Title'), 100));
        $this->_columns->add(new Kwf_Grid_Column('Mass', trlKwf('Weight'), 100));
        $this->_columns->add(new Kwf_Grid_Column('Center', trlKwf('Center Point'), 100));
        $this->_columns->add(new Kwf_Grid_Column('LotsNumber', trlKwf('Seats Number'), 100));
    }
}

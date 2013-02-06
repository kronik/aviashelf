<?php
class LandpointsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Landpoints';
    protected $_defaultOrder = 'name';
    protected $_buttons = array('add');

    public function indexAction()
    {
        $this->view->ext('Landpoints');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Title'), 100));
        $this->_columns->add(new Kwf_Grid_Column('responsibleName', trlKwf('Responsible')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('phone', trlKwf('Phone')))->setWidth(100);
    }
}

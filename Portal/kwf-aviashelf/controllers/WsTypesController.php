<?php
class WsTypesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'WsTypes';
    protected $_defaultOrder = 'Name';
    protected $_paging = 0;
    protected $_buttons = array('add');
    
    public function indexAction()
    {
        $this->view->ext('WsTypes');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('Name', trlKwf('Title'), 100));
        $this->_columns->add(new Kwf_Grid_Column('NameEn', trlKwf('English name'), 100));
        $this->_columns->add(new Kwf_Grid_Column('IKAO', trlKwf('IKAO'), 100));
        $this->_columns->add(new Kwf_Grid_Column('IATA', trlKwf('IATA'), 100));
    }
}

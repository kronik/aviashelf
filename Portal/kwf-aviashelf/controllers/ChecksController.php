<?php
class ChecksController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Checks';
    protected $_defaultOrder = array('field' => 'title', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');

    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title')))->setWidth(170);
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(130);
    }
}

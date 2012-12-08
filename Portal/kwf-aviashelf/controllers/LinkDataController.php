<?php
class LinkDataController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'LinkData';
    protected $_defaultOrder = 'name';
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
        'controllerUrl' => '/link-dataentry',
        'width' => 450,
        'height' => 300
    );

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('name', trl('Title')));
        $this->_columns->add(new Kwf_Grid_Column('value', trl('Value')));
        $this->_columns->add(new Kwf_Grid_Column('desc', trl('Desc')))
            ->setRenderer('nl2Br')
            ->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['link_id = ?'] = $this->_getParam('link_id');
        return $ret;
    }
}

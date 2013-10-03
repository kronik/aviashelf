<?php
class FlightaccessesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightaccesses';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_buttons = array('add', 'delete');
    protected $_grouping = array('groupField' => 'wsTypeName');
    protected $_editDialog = array(
        'controllerUrl' => '/flightaccess',
        'width' => 550,
        'height' => 300
    );

    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('accessDate', 'Дата'))->setWidth(80);
        $this->_columns->add(new Kwf_Grid_Column('docName', trlKwf('Document')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('accessTypeName', 'Тип допуска'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('accessName', trlKwf('Name')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['employeeId = ?'] = $this->_getParam('employeeId');
        return $ret;
    }
}

<?php
    require_once 'GridEx.php';

class FlightaccessesController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightaccesses';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_buttons = array('add', 'delete');
    protected $_grouping = array('groupField' => 'wsTypeName');
    protected $_editDialog = array(
        'controllerUrl' => '/flightaccess',
        'width' => 550,
        'height' => 310
    );

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('accessDate', 'Дата начала'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('accessEndDate', 'Дата окончания'))->setWidth(100)->setRenderer('docCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('accessTypeName', 'Тип проверки'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('accessName', 'Метеоминимум'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['employeeId = ?'] = $this->_getParam('employeeId');
        return $ret;
    }
}

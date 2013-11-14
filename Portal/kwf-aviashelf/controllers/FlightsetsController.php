<?php
require_once 'GridEx.php';
class FlightsetsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightset';
    protected $_defaultOrder = array('field' => 'employeeName', 'direction' => 'ASC');
    protected $_buttons = array('add', 'delete');
    protected $_grouping = array('groupField' => 'employeeName');
    protected $_editDialog = array(
        'controllerUrl' => '/flightset',
        'width' => 550,
        'height' => 420
    );

    protected function _initColumns()
    {
        parent::_initColumns();
                
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('setStartDate', 'Дата начала'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setEndDate', 'Дата окончания'))->setWidth(100)->setRenderer('docCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setName', 'Тип захода'))->setWidth(200);
//        $this->_columns->add(new Kwf_Grid_Column('setTypeName', 'Аэропорт'))->setWidth(150);
//        $this->_columns->add(new Kwf_Grid_Column('setMeteoTypeName', 'Метеоминимум'))->setWidth(200);
//        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['flightId = ?'] = $this->_getParam('flightId');
        return $ret;
    }
}

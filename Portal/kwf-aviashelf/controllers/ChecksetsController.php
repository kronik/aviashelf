<?php
class ChecksetsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightset';
    protected $_defaultOrder = array('field' => 'employeeName', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'employeeName');
    protected $_buttons = array('xls');

    protected function _initColumns()
    {
        $this->_filters = array('employeeName' => array('type' => 'TextField'), 'setEndDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('employeeName', 'wsTypeName', 'setName', 'setTypeName', 'setMeteoTypeName', 'comment');
        
        $this->_columns->add(new Kwf_Grid_Column('employeeName', 'ФИО'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('setStartDate', 'Дата начала'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setEndDate', 'Дата окончания'))->setWidth(100)->setRenderer('setsCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('flightsCount', 'Кол-во полетов'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setsCount', 'Кол-во заходов'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setName', 'Тип захода'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setTypeName', 'Тип допуска'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('setMeteoTypeName', 'Метеоминимум'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(500);
    }
}

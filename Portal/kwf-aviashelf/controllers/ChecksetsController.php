<?php
class ChecksetsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightset';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_buttons = array('xls');
    protected $_grouping = array('groupField' => 'wsTypeName');
//    protected $_editDialog = array(
//        'controllerUrl' => '/flightset',
//        'width' => 550,
//        'height' => 390
//    );

    protected function _initColumns()
    {
        $this->_filters = array('wsTypeName' => array('type' => 'TextField'), 'setEndDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('wsTypeName', 'setName', 'setTypeName', 'setMeteoName', 'comment');
        
        $this->_columns->add(new Kwf_Grid_Column('setStartDate', 'Дата начала'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setEndDate', 'Дата окончания'))->setWidth(100)->setRenderer('setsCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setName', 'Тип захода'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('setTypeName', 'Тип допуска'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('setMeteoName', 'Метеоминимум'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('flightsCount', 'Кол-во полетов'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('setsCount', 'Кол-во заходов'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

//    protected function _getWhere()
//    {
//        $ret = parent::_getWhere();
//        $ret['flightId = ?'] = $this->_getParam('flightId');
//        return $ret;
//    }
}

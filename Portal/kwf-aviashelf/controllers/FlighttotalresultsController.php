<?php
    require_once 'GridEx.php';

class FlighttotalresultsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightresults';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 1000;
    protected $_grouping = array('groupField' => 'ownerName');
    protected $_buttons = array('xls');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('typeName' => array('type' => 'TextField'), 'flightDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('typeName', 'ownerName', 'planeName');
        
        $this->_columns->add(new Kwf_Grid_Column('ownerName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column_Date('flightDate', trlKwf('Date')));
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('flightsCount', 'Кол-во полетов'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('flightTime', trlKwf('Time')))->setProperty('summaryType', 'totalFlightTime');
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('showInTotal', trlKwf('Show in total')))->setWidth(60);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(500);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        
        $ret['flightsCount > ?'] = 0;
        $ret['flightTime <> ?'] = '00:00';
        
        return $ret;
    }
}

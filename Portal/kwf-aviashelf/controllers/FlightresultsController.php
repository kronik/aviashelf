<?php
class FlightresultsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightresults';
    protected $_defaultOrder = array('field' => 'flightDate', 'direction' => 'DESC');
    protected $_paging = 10;
    protected $_grouping = array('groupField' => 'planeName');
    protected $_buttons = array('add', 'save');
    protected $_editDialog = array(
        'controllerUrl' => '/flightresult',
        'width' => 550,
        'height' => 280
    );

    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('WsType')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column_Date('flightDate', trlKwf('Date')));
        $this->_columns->add(new Kwf_Grid_Column('flightTime', trlKwf('Time')))
        ->setSummaryType('totalTime');
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['ownerId = ?'] = $this->_getParam('ownerId');
        return $ret;
    }
}

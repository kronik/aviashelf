<?php
class FlightresultsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightresults';
    protected $_defaultOrder = array('field' => 'flightDate', 'direction' => 'DESC');
    protected $_paging = 10;
    protected $_grouping = array('groupField' => 'planeName');
    protected $_buttons = array('add');
    protected $_editDialog = array(
        'controllerUrl' => '/flightresult',
        'width' => 550,
        'height' => 310
    );

    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column_Date('flightDate', trlKwf('Date')));
        $this->_columns->add(new Kwf_Grid_Column('flightTime', trlKwf('Time')))->setProperty('summaryType', 'totalTime');
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('showInTotal', trlKwf('Show in total')))->setWidth(80);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(500);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['ownerId = ?'] = $this->_getParam('ownerId');

        return $ret;
    }
}

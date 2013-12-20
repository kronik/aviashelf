<?php
    require_once 'GridEx.php';

class FlightresultsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightresults';
    protected $_defaultOrder = array('field' => 'flightDate', 'direction' => 'DESC');
    protected $_paging = 1000;
    protected $_grouping = array('groupField' => 'planeName');
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        $this->_filters = array('typeName' => array('type' => 'TextField'), 'flightDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('typeName');
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' || $users->getAuthedUserRole() == 'power')
        {
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/flightresult',
                                       'width' => 550,
                                       'height' => 340
                                       );
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column_Date('flightDate', trlKwf('Date')));
        $this->_columns->add(new Kwf_Grid_Column('flightsCount', 'Кол-во полетов'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('flightTime', trlKwf('Time')))->setProperty('summaryType', 'totalFlightTime');
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('showInTotal', trlKwf('Show in total')))->setWidth(80);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(500);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['ownerId = ?'] = $this->_getParam('ownerId');
        $ret['flightTime <> ?'] = '00:00';

        return $ret;
    }
}

<?php
    require_once 'GridEx.php';

class FlightfullresultsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightresults';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 100;
    protected $_grouping = array('groupField' => 'ownerName');
    protected $_buttons = array('add', 'xls', 'delete');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        $users = Kwf_Registry::get('userModel');
        
        $this->_filters = array('typeName' => array('type' => 'TextField'), 'flightDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('typeName', 'ownerName');
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' ||
            $users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws')
        {
            if ($users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
                unset($this->_buttons ['delete']);
            }

            if ($users->getAuthedUserRole() == 'kws') {
                unset($this->_buttons ['add']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/flightfullresult',
                                       'width' => 550,
                                       'height' => 320
                                       );
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('ownerName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('flightsCount', 'Кол-во полетов'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('flightTime', trlKwf('Time')));//->setProperty('summaryType', 'totalFlightTime');
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('showInTotal', trlKwf('Show in total')))->setWidth(60);
        //$this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(500);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        
        if ($this->_getParam('flightId') != NULL) {
            $ret['flightId = ?'] = $this->_getParam('flightId');
        }
                
        return $ret;
    }
}

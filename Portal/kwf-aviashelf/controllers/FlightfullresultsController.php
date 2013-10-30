<?php
class FlightfullresultsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flightresults';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 10;
    protected $_grouping = array('groupField' => 'ownerName');
    protected $_buttons = array('add', 'xls');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        $users = Kwf_Registry::get('userModel');
        
        $this->_filters = array('typeName' => array('type' => 'TextField'), 'flightDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('typeName', 'ownerName');
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan')
        {
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/flightfullresult',
                                       'width' => 550,
                                       'height' => 290
                                       );
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('ownerName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('flightTime', trlKwf('Time')))->setProperty('summaryType', 'totalTime')->setWidth(60);
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('showInTotal', trlKwf('Show in total')))->setWidth(60);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(500);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['flightId = ?'] = $this->_getParam('flightId');
        return $ret;
    }
}

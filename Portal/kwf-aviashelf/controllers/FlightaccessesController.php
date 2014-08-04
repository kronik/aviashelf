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
        'height' => 450
    );

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $users = Kwf_Registry::get('userModel');

        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' ||
            $users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
            
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            if ($users->getAuthedUserRole() != 'admin') {
                
                unset($this->_buttons ['delete']);
            }
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/flightaccess',
                                       'width' => 550,
                                       'height' => 510
                                       );
        } else {
            $this->_buttons = array();
            $this->_editDialog = NULL;
        }
        
        if ($this->_getParam('flightId') != NULL) {
            $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(150);
            $this->_grouping = array('groupField' => 'employeeName');
        }
        
        $this->_columns->add(new Kwf_Grid_Column('accessDate', 'Дата начала'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('accessEndDate', 'Дата окончания'))->setWidth(100)->setRenderer('exCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('flightsCount', 'Кол-во полетов'))->setWidth(90);
        $this->_columns->add(new Kwf_Grid_Column('setsCount', 'Кол-во сп/подъемов'))->setWidth(110);
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('finished', ''));
        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('accessTypeName', 'Тип проверки'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('accessName', 'Метеоминимум'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('docNumber', 'Номер приказа'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        
        if ($this->_getParam('employeeId') != NULL) {
            $ret['employeeId = ?'] = $this->_getParam('employeeId');
        }
        
        if ($this->_getParam('flightId') != NULL) {
            
            $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
            $flightsSelect = $flightsModel->select()->whereEquals('id', $this->_getParam('flightId'));
            $flight = $flightsModel->getRow($flightsSelect);
            
            $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
            $planesSelect = $planesModel->select()->whereEquals('id', $flight->planeId);
            $plane = $planesModel->getRow($planesSelect);
            
            $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
            $wstypeSelect = $wstypeModel->select()->whereEquals('id', $plane->twsId);
            $planeType = $wstypeModel->getRow($wstypeSelect);
            
            $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
            $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $this->_getParam('flightId'))->whereEquals('mainCrew', TRUE);
            
            $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
            
            $memberIds = array();
            
            foreach ($flightMembers as $flightMember) {
                array_push($memberIds, $flightMember->employeeId);
            }
            
            if ($planeType != NULL) {
                $ret['wsTypeId = ?'] = $planeType->id;
            }
            
            if (count($memberIds) > 0) {
                $ret['employeeId IN (?)'] = $memberIds;
            } else {
                $ret['employeeId = ?'] = '0';
            }
        }
        
        return $ret;
    }
}

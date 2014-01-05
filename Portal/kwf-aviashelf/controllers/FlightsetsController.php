<?php
require_once 'GridEx.php';
class FlightsetsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightset';
    protected $_defaultOrder = array('field' => 'employeeName', 'direction' => 'ASC');
    protected $_buttons = array('add', 'delete');
    protected $_grouping = array('groupField' => 'employeeName');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');

        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' || $users->getAuthedUserRole() == 'power') {
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            if ($users->getAuthedUserRole() == 'power') {
                
                unset($this->_buttons ['delete']);
            }
            
            $this->_editDialog = array(
                                    'controllerUrl' => '/flightset',
                                    'width' => 550,
                                    'height' => 440
                                );
        } else {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('setStartDate', 'Дата начала'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setEndDate', 'Дата окончания'))->setWidth(100)->setRenderer('docCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('setTypeName', 'Аэропорт'))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('setName', 'Тип захода'))->setWidth(200);
        //        $this->_columns->add(new Kwf_Grid_Column('wsTypeName', trlKwf('WsType')))->setWidth(100);
//        $this->_columns->add(new Kwf_Grid_Column('setMeteoTypeName', 'Метеоминимум'))->setWidth(200);
//        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();

        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('id', $this->_getParam('flightId'));
        $flight = $flightsModel->getRow($flightsSelect);
        
        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $flight->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $wstypeModel->getRow($wstypeSelect);

        $ret['wsTypeId = ?'] = $planeType->id;
        $ret['finished = ?'] = '0';
        
        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $this->_getParam('flightId'))->whereEquals('mainCrew', TRUE);
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);

        if (count($flightMembers) > 0) {
            $crew = '(';
            
            foreach ($flightMembers as $flightMember) {
                $crew = $crew . $flightMember->employeeId . ',';
            }
            
            $crew = $crew . '0)';

            $ret[] = 'employeeId IN ' . $crew;
        }
        
        return $ret;
    }
}

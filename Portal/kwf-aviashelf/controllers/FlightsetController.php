<?php
class FlightsetController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightset';
    protected $_permissions = array('save', 'add', 'delete');
    protected $_paging = 0;

    protected function _initFields()
    {
        $landpointsModel = Kwf_Model_Abstract::getInstance('Airports');
        $landpointsSelect = $landpointsModel->select()->order('Name');

        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('id', $this->_getParam('flightId'));
        
        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $this->_getParam('flightId'))->whereEquals('mainCrew', TRUE);
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
        
        $memberIds = array();
        
        foreach ($flightMembers as $flightMember) {
            array_push($memberIds, $flightMember->employeeId);
        }
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        
        if (count($memberIds) > 0) {
            $employeesSelect = $employeesModel->select()
            ->whereEquals('visible', '1')
            ->whereEquals('groupType', '1')
            ->whereEquals('isOOO', false)
            ->whereEquals('isAllowed', '1')
            ->where('id IN (?)', $memberIds)->order('lastname');
        } else {
            $employeesSelect = $employeesModel->select()
            ->whereEquals('visible', '1')
            ->whereEquals('groupType', '1')
            ->whereEquals('isOOO', false)
            ->whereEquals('isAllowed', '1')
            ->order('lastname');
        }
        
        $setTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $setTypeSelect = $setTypeModel->select()->whereEquals('name', 'Тип захода')->order('value');
        
        $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('setId', 'Тип захода'))
        ->setValues($setTypeModel)
        ->setSelect($setTypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_TextField('setMeteoTypeName', 'Метеоминимум'))
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_Select('setTypeId', 'Аэропорт'))
        ->setValues($landpointsModel)
        ->setSelect($landpointsSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('flightsCount', 'Кол-во полетов'))
        ->setValues(array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20'))
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_Select('setsCount', 'Кол-во заходов'))
        ->setValues(array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20'))
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_DateField('setStartDate', 'Дата начала'))->setAllowBlank(true);
        $this->_form->add(new Kwf_Form_Field_DateField('setEndDate', 'Дата окончания'))->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('finished', 'Выполнено'));
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'kws') {
            
            $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
            $flightsSelect = $flightsModel->select()->whereEquals('id', $this->_getParam('flightId'));
            $flight = $flightsModel->getRow($flightsSelect);

            $flightDate = new DateTime ($flight->flightStartDate);
            
            $dateLimit = new DateTime('NOW');
            $dateLimit->sub( new DateInterval('P2D') );
            
            if ($flightDate < $dateLimit) {
                throw new Kwf_Exception_Client('ПЗ закрыто для изменений.');
            }
        }

        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        $m3 = Kwf_Model_Abstract::getInstance('Airports');

        $s = $m1->select()->whereEquals('id', $row->setId);
        $prow = $m1->getRow($s);
        
        $row->setName = $prow->value;
        
        $row->setMeteoTypeId = 0;
        
        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        
        $row->employeeName = (string)$prow;
        
        $landpointSelect = $m3->select()->whereEquals('id', $row->setTypeId);
        $prow = $m3->getRow($landpointSelect);
        $row->setTypeName = $prow->Name;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('id', $this->_getParam('flightId'));
        $flight = $flightsModel->getRow($flightsSelect);
        
        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $flight->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $wstypeModel->getRow($wstypeSelect);
        
        if (($row->flightId == 0) || ($row->flightId == NULL)) {
            $row->flightId = $this->_getParam('flightId');
        }
        
        $row->wsTypeId = $planeType->id;
        
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

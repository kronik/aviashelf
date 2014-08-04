<?php
    
require_once 'FormEx.php';

class FlightfullresultController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_modelName = 'Flightresults';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->where(new Kwf_Model_Select_Expr_Sql('visible = 1 AND groupType = 1'))->order('lastname');
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Типы налета');
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Type')))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('ownerId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_TimeField('flightTime', trlKwf('Time')))
        ->setDefaultValue('07:12')
        ->setIncrement(5);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('flightsCount', 'Кол-во полетов'))->setWidth(400);

        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $users = Kwf_Registry::get('userModel');

        if ($users->getAuthedUserRole() == 'admin') {
            $this->_form->add(new Kwf_Form_Field_Checkbox('showInTotal', trlKwf('Show in total')));
        }
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('workOnHoliday', 'Код РВ'));
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row) {
        
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        $m3 = Kwf_Model_Abstract::getInstance('Flights');

        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        $row->typeName = $prow->value;
        
        $s = $m3->select()->whereEquals('id', $this->_getParam('flightId'));
        $prow = $m3->getRow($s);
        
        $row->flightDate = $prow->flightStartDate;
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'kws') {
            
            $flightDate = new DateTime ($prow->flightStartDate);
            
            $dateLimit = new DateTime('NOW');
            $dateLimit->sub( new DateInterval('P2D') );
            
            if ($flightDate < $dateLimit) {
                throw new Kwf_Exception_Client('ПЗ закрыто для изменений.');
            }
        }
        
        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $prow->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $typeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $typeSelect = $typeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $typeModel->getRow($typeSelect);
        $row->planeId = $plane->twsId;

        $row->planeName = $planeType->Name;

        $s = $m2->select()->whereEquals('id', $row->ownerId);
        $prow = $m2->getRow($s);
        
        $row->ownerName = (string)$prow;
        
        if ($this->isContain('Время работы', $row->typeName)) {
            $row->flightsCount = 0;
        }
    }
    
    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateWorkForResult($row);

        if (($row->flightTime != NULL) && ($row->flightTime != '00:00') && ($row->flightTime != '00:00:00')) {
            $resultsModel = Kwf_Model_Abstract::getInstance('Flightresults');
            
            $resultsSelect = $resultsModel->select()
            ->where(new Kwf_Model_Select_Expr_Sql('flightId = ' . $row->flightId . ' AND ownerId <> ' . $row->ownerId . ' AND typeId = ' . $row->typeId));
            
            $results = $resultsModel->getRows($resultsSelect);
            
            $objectivesModel = Kwf_Model_Abstract::getInstance('FlightObjectives');
            $objectivesSelect = $objectivesModel->select()->whereEquals('flightId', $row->flightId);
            $objectiveRows = $objectivesModel->getRows($objectivesSelect);
            
            $objectiveIds = array();
            
            foreach ($objectiveRows as $objectiveRow) {
                array_push($objectiveIds, $objectiveRow->objectiveId);
            }
            
            $canAddFlightResults = TRUE;
            
            $objectivesModel = Kwf_Model_Abstract::getInstance('Objectives');
            $objectivesSelect = $objectivesModel->select()->whereEquals('name', 'Цель полета')->where('id IN (?)', $objectiveIds);
            $objectiveRows = $objectivesModel->getRows($objectivesSelect);
            
            foreach ($objectiveRows as $objectiveRow) {
                if ($this->isContain('Тренировка', $objectiveRow->value)) {
                    $canAddFlightResults = FALSE;
                    break;
                }
            }
            
            if ($canAddFlightResults == FALSE) {
                return;
            }
            
            foreach ($results as $result) {
                if (($result->flightTime == NULL) || ($result->flightTime == '00:00') || ($result->flightTime == '00:00:00')) {
                    
                    $result->flightTime = $row->flightTime;
                    $result->flightsCount = $row->flightsCount;
                    $result->showInTotal = $row->showInTotal;
                    
                    $result->save();
                    
                    $this->updateWorkForResult($result);
                }
            }
        }
    }
    
    protected function updateWorkForResult ($result) {

        $helper = new Helper ();
        
        $resultDate = new DateTime ($result->flightDate);

        $worksModel = Kwf_Model_Abstract::getInstance('Works');
        $worksSelect = $worksModel->select()->whereEquals('month', $resultDate->format('m'))->whereEquals('year', $resultDate->format('Y'));
        $work = $worksModel->getRow($worksSelect);
        
        if ($work == NULL) {
            return;
        }

        $helper->updateWorkEntries($work->id, $result->ownerId, true);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->flightId = $this->_getParam('flightId');
        
        if ($row->showInTotal == NULL) {
            $row->showInTotal = false;
        }
    }
    
    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateWorkForResult($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

<?php
class FlightgroupController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightgroups';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Позиции на борту')->order('value');
        
        $positions = new Kwf_Form_Field_Select('positionId', trlKwf('Position'));
        $positions->setValues($typeModel->getRows($typeSelect));
        $positions->setAllowBlank(false);
        $positions->setWidth(400);
        
        $employees = new Kwf_Form_Field_Select('employeeId', trlKwf('Employee'));
        $employees->setValues('/flightgroupsfilter/json-data');
        $employees->setAllowBlank(false);
        $employees->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_FilterField())
        ->setFilterColumn('positionId')
        ->setFilteredField($employees)
        ->setFilterField($positions)
        ->setWidth(400);
                
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);        
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        
        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('id', $row->flightId);
        
        $s = $m1->select()->whereEquals('id', $row->positionId);
        $prow = $m1->getRow($s);
        $row->positionName = $prow->value;
                
        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        
        $row->employeeName = (string)$prow;

        $flightRow = $flightsModel->getRow($flightsSelect);

        if (($this->isContain(trlKwf('KWS'), $row->positionName)) && ($row->mainCrew == TRUE))
        {
            $flightRow->firstPilotName = (string)$prow;
         
            $users = Kwf_Model_Abstract::getInstance('Employees');
            $userSelect = $users->select()->whereEquals('id', $row->employeeId);
            $employee = $users->getRow($userSelect);
            
            if ($employee->userId != NULL)
            {
                $tasks = Kwf_Model_Abstract::getInstance('Tasks');
                
                $taskRow = $tasks->createRow();
                
                $taskRow->title = 'Выполнить полет: ' . $flightRow->number;
                $taskRow->description = 'Выполнить полет: ' . $flightRow->number . ' ' . $flightRow->flightStartDate . ' ' . $flightRow->flightStartTime;
                $taskRow->startDate = $flightRow->flightStartDate;
                $taskRow->userId = $employee->userId;
                $taskRow->status = 0;
                
                $taskRow->save();
            }
        }
        else if (($this->isContain(trlKwf('Second pilot'), $row->positionName)) && ($row->mainCrew == TRUE))
        {
            $flightRow->secondPilotName = (string)$prow;
        }
        else if (($this->isContain(trlKwf('Technic'), $row->positionName)) && ($row->mainCrew == TRUE))
        {
            $flightRow->technicName = (string)$prow;
        }
        else if ($this->isContain(trlKwf('Resquer'), $row->positionName))
        {
            $flightRow->resquerName = (string)$prow;
        }
        else if (($this->isContain(trlKwf('Instructor'), $row->positionName)) ||
                 ($this->isContain(trlKwf('Checker'), $row->positionName)))
        {
            $flightRow->checkPilotName = (string)$prow;
        }
        
        $flightRow->save();
        
        if ($row->mainCrew == TRUE)
        {
            if ($this->isContain(trlKwf('KWS'), $row->positionName))
            {
                $this->addFlightResult($flightRow, $row, 'Налет КВС');
            }
            
            if (($this->isContain(trlKwf('Instructor'), $row->positionName)) ||
                ($this->isContain(trlKwf('Checker'), $row->positionName)))
            {
                $this->addFlightResult($flightRow, $row, 'Налет КВС');
                $this->addFlightResult($flightRow, $row, 'Инструктор');
            }

            
            $this->addFlightResult($flightRow, $row, 'Налет');
            $this->addFlightResult($flightRow, $row, 'Время работы');
        }
    }
    
    protected function addFlightResult($flight, $groupRow, $type)
    {
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Типы налета')->whereEquals('value', $type);
        $typeRow = $typeModel->getRow($typeSelect);
        
        $result = Kwf_Model_Abstract::getInstance('Flightresults');
        $resultSelect = $result->select()->where(new Kwf_Model_Select_Expr_Sql("ownerId = " . $groupRow->employeeId
                                                                               ." AND flightId = " . $flight->id
                                                                               ." AND typeId = " . $typeRow->id));
        $resultRow = $result->getRow($resultSelect);
        
        if ($resultRow == NULL)
        {
            $resultRow = $result->createRow();
            
            $resultRow->typeId = $typeRow->id;
            $resultRow->typeName = $typeRow->value;
            $resultRow->planeId = $flight->planeId;
            $resultRow->planeName = $flight->planeName;
            $resultRow->flightsCount = 0;
            $resultRow->flightDate = $flight->flightStartDate;
            $resultRow->flightId = $flight->id;
            $resultRow->flightTime = '00:00';
            $resultRow->ownerId = $groupRow->employeeId;
            $resultRow->ownerName = $groupRow->employeeName;
            $resultRow->showInTotal = 0;
            
            $resultRow->save();
        }
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->flightId = $this->_getParam('flightId');
        $row->mainCrew = TRUE;

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

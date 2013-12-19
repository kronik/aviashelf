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
                
        if ($row->mainCrew == TRUE)
        {
            $positionModel = Kwf_Model_Abstract::getInstance('Flightresultdefaults');
            $positionSelect = $positionModel->select()->whereEquals('positionId', $row->positionId);

            $positionRows = $positionModel->getRows($positionSelect);
            
            foreach ($positionRows as $positionRow) {
                $this->addFlightResult($flightRow, $row, $positionRow->resultId);
            }
        }
    }
    
    protected function addFlightResult($flight, $groupRow, $typeId)
    {
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('id', $typeId);
        $typeRow = $typeModel->getRow($typeSelect);

        if ($typeRow == NULL) {
            throw new Kwf_Exception_Client('Тип налета: <' . $typeId . '> не найден в словаре.');
        }
        
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
            $resultRow->flightsCount = 1;
            $resultRow->flightDate = $flight->flightStartDate;
            $resultRow->flightId = $flight->id;
            $resultRow->flightTime = '00:00';
            $resultRow->ownerId = $groupRow->employeeId;
            $resultRow->ownerName = $groupRow->employeeName;
            $resultRow->showInTotal = 0;
            
            $resultRow->save();
        }
    }

    protected function addFlightSet($flight, $groupRow)
    {
        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $flight->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $wstypeModel->getRow($wstypeSelect);

        $result = Kwf_Model_Abstract::getInstance('Flightset');
        $resultSelect = $result->select()->where(new Kwf_Model_Select_Expr_Sql("employeeId = " . $groupRow->employeeId
                                                                               ." AND flightId = " . $flight->id));
        $resultRow = $result->getRow($resultSelect);
        
        if ($resultRow == NULL)
        {
            $resultRow = $result->createRow();
            
            $resultRow->flightId = $flight->id;
            $resultRow->flightsCount = 0;
            $resultRow->setsCount = 0;
            $resultRow->employeeId = $groupRow->employeeId;
            $resultRow->employeeName = $groupRow->employeeName;
            $resultRow->wsTypeId = $planeType->id;
            $resultRow->wsTypeName = $planeType->Name;
            $resultRow->setId = 0;
            $resultRow->setName = '';
            $resultRow->setMeteoTypeId = 0;
            $resultRow->setMeteoTypeName = '';
            $resultRow->setTypeId = 0;
            $resultRow->setTypeName = '';
            $resultRow->setStartDate = $flight->flightStartDate;
            $resultRow->setEndDate = $flight->flightStartDate;

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
    
    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $row->flightId)->order('id');
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);

        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('id', $row->flightId);

        $users = Kwf_Model_Abstract::getInstance('Employees');

        $flightRow = $flightsModel->getRow($flightsSelect);

        $flightRow->firstPilotName = '';
        $flightRow->secondPilotName = '';
        $flightRow->technicName = '';
        $flightRow->resquerName = '';
        $flightRow->checkPilotName = '';
        
        foreach ($flightMembers as $flightMember) {

            $userSelect = $users->select()->whereEquals('id', $flightMember->employeeId);
            $employee = $users->getRow($userSelect);

            if ($employee == NULL) {
                continue;
            }
            
            if (($this->isContain('КВС', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->firstPilotName = (string)$employee;
                
                if ($employee->userId != NULL)
                {
                    $tasks = Kwf_Model_Abstract::getInstance('Tasks');
                    
                    $taskRow = $tasks->createRow();
                    
                    $dateLimit = new DateTime($flightRow->flightStartDate);
                    $dateLimit->add( new DateInterval('P1D') );

                    $taskRow->title = 'Выполнить полет: ' . $flightRow->number;
                    $taskRow->description = 'Выполнить полет: ' . $flightRow->number . ' ' . $flightRow->flightStartDate . ' ' . $flightRow->flightStartTime;
                    $taskRow->startDate = $flightRow->flightStartDate;
                    $taskRow->endDate = $dateLimit->format('Y-m-d') . ' 23:59';
                    $taskRow->userId = $employee->userId;
                    $taskRow->status = 0;
                    
                    $taskRow->save();
                    
                    $this->sendMessage($flightMember->employeeId, $flightRow);
                }
            }
            else if (($this->isContain('Второй пилот', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->secondPilotName = (string)$employee;
                $this->sendMessage($flightMember->employeeId, $flightRow);
            }
            else if (($this->isContain('Пилот', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->secondPilotName = (string)$employee;
                $this->sendMessage($flightMember->employeeId, $flightRow);
            }
            else if (($this->isContain(trlKwf('Technic'), $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->technicName = (string)$employee;
            }
            else if ($this->isContain('Спасатель', $flightMember->positionName))
            {
                $flightRow->resquerName = (string)$employee;
            }
            else if (($this->isContain('Проверяющий', $flightMember->positionName)) ||
                     ($this->isContain('Инструктор', $flightMember->positionName)))
            {
                $flightRow->checkPilotName = (string)$employee;
                $this->sendMessage($flightMember->employeeId, $flightRow);
            }
        }
        
        $flightRow->save();
    }
    
    public function sendMessage ($employeeId, $flightRow) {
        
        return;
        
        if ($employeeId == NULL) {
            return;
        }
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('id', $employeeId);
        
        $employeeRow = $employeesModel->getRow($employeesSelect);
        
        if (($employeeRow == NULL) || ($employeeRow->userId == NULL) || ($employeeRow->userId <= 0)) {
            return;
        }
        
        $userModel = Kwf_Model_Abstract::getInstance('Kwf_User_Model');
        $userSelect = $userModel->select()->whereEquals('id', $employeeRow->userId);
        
        $userRow = $userModel->getRow($userSelect);
        
        if (($userRow == NULL)) {
            return;
        }
        
        $phoneNumber = $employeeRow->privatePhone;
        $phoneEmail = NULL;
        
        if ($phoneNumber != NULL) {
            $symbols = array ("+", "-", " ", "/");
            $phoneNumber = str_replace ($symbols, "", $phoneNumber);
            $phoneOperator = '';
            
            if ((strpos($phoneNumber, "7914") === 0) || (strpos($phoneNumber, "8914") === 0)) {
                $phoneOperator = "@sms.mtsdv.ru";
            } else if (((strpos($phoneNumber, "7924") === 0) || (strpos($phoneNumber, "8924") === 0)) ||
                       ((strpos($phoneNumber, "7929") === 0) || (strpos($phoneNumber, "8929") === 0))) {
                $phoneOperator = "@sms.megafondv.ru";
            } else {
                $phoneOperator = "@sms.beemail.ru";
            }
            
            $phoneEmail = $phoneNumber . $phoneOperator;
        }
                
        $needToSend = 0;
        
        $mail = new Kwf_Mail_Template('NewFlightTaskTemplate');
        
        $mail->fullname = (string)$employeeRow;
        $mail->flight = $flightRow->number;
        $mail->flightdescription = 'ПЗ: ' . $flightRow->requestNumber . ' ' . $flightRow->number . ' ' . $flightRow->flightStartDate . ' ' . $flightRow->flightStartTime;
        
        if ($userRow->email != NULL) {
            $mail->addTo($userRow->email);
            $needToSend ++;
        }
        
        if ($phoneEmail != NULL) {
            $mail->addTo($phoneEmail);
            $needToSend ++;
        }
        
        //$mail->addTo('dmitry.klimkin@gmail.com');
        $mail->setFrom('puls@aviashelf.com', 'Авиашельф Пульс');
        $mail->setSubject('ПЗ: ' . $flightRow->number);
                
        if ($needToSend > 0) {
            $mail->send();
        }
    }
}

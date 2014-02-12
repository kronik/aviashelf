<?php
class Helper {
    
    public function transferStatesFromPreviousPlan ($planId) {
        
        if ($planId == NULL) {
            return;
        }
        
        $today = new DateTime('NOW');
        $yesterday = new DateTime('NOW');
        $yesterday->sub( new DateInterval('P1D') );
        $tomorrow = new DateTime('NOW');
        $tomorrow->add( new DateInterval('P1D') );
        
        $flightPlanModel = Kwf_Model_Abstract::getInstance('Flightplans');
        $flightPlanSelect = $flightPlanModel->select()->whereEquals('id', $planId);
        $flightPlan = $flightPlanModel->getRow($flightPlanSelect);
        
        $planDate = new DateTime($flightPlan->planDate);
        
        if ($flightPlan == NULL || $planDate > $tomorrow) {
            return;
        }
        
        $planerstatesModel = Kwf_Model_Abstract::getInstance('Planerstates');
        $planerstatesSelect = $planerstatesModel->select()->whereEquals('planId', $planId);
        $planerstates = $planerstatesModel->getRows($planerstatesSelect);
        
        if (count($planerstates) > 0) {
            return;
        }
        
        $flightPlanSelect = $flightPlanModel->select()->where('planDate < ?', $flightPlan->planDate)->order('planDate', 'DESC');
        
        $lastFlightPlan = $flightPlanModel->getRow($flightPlanSelect);
        
        if ($lastFlightPlan == NULL) {
            return;
        }
        
        $planerstatesSelect = $planerstatesModel->select()->whereEquals('planId', $lastFlightPlan->id);
        
        $planerstates = $planerstatesModel->getRows($planerstatesSelect);
        
        $db = Zend_Registry::get('db');
        
        $db->delete('planerStates', array('planId = ?' => $planId));
        
        $planes = array();

        foreach ($planerstates as $planerstate) {
            
            if (in_array($planerstate->planeId, $planes) == true) {
                continue;
            }

            $resultRow = $planerstatesModel->createRow();
            
            $resultRow->planId = $planId;
            $resultRow->priority = $planerstate->priority;
            $resultRow->statusDate = $flightPlan->planDate;
            $resultRow->expectedDate = $planerstate->expectedDate;
            $resultRow->comment = $planerstate->comment;
            
            $resultRow->typeId = $planerstate->typeId;
            $resultRow->typeName = $planerstate->typeName;
            
            $resultRow->planeId = $planerstate->planeId;
            $resultRow->planeName = $planerstate->planeName;
            
            $resultRow->responsibleId = $planerstate->responsibleId;
            $resultRow->responsibleName = $planerstate->responsibleName;
            
            $resultRow->landpointId = $planerstate->landpointId;
            $resultRow->landpointName = $planerstate->landpointName;
            
            $resultRow->statusId = $planerstate->statusId;
            $resultRow->statusName = $planerstate->statusName;
            
            array_push($planes, $planerstate->planeId);

            $resultRow->save();
        }
    }
    
    public function updateFlightResults ($flightId) {
        
        if ($flightId == NULL) {
            return;
        }
        
        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $flightId)->order('id');
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
        
        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('id', $flightId);
        
        $users = Kwf_Model_Abstract::getInstance('Employees');
        
        $flightRow = $flightsModel->getRow($flightsSelect);
        
        $db = Zend_Registry::get('db');
        
        $db->delete('flightResults', array('flightId = ?' => $flightRow->id));
        
        $flightRow->firstPilotName = '';
        $flightRow->secondPilotName = '';
        $flightRow->technicName = '';
        $flightRow->resquerName = '';
        $flightRow->checkPilotName = '';
        
        $trained = array();
        
        $objectivesModel = Kwf_Model_Abstract::getInstance('FlightObjectives');
        $objectivesSelect = $objectivesModel->select()->whereEquals('flightId', $flightRow->id);
        $objectiveRows = $objectivesModel->getRows($objectivesSelect);
        
        $objectiveIds = array();
        
        foreach ($objectiveRows as $objectiveRow) {
            array_push($objectiveIds, $objectiveRow->objectiveId);
        }
        
        $positionModel = Kwf_Model_Abstract::getInstance('Flightresultdefaults');
        
        $positionSelect = $positionModel->select()
        ->where('positionId IN (?)', $objectiveIds)
        ->whereEquals('typeName', 'objective');
        
        $positionExtraRows = array();
        
        if (count($objectiveIds) > 0) {
            $positionExtraRows = $positionModel->getRows($positionSelect);
        }
        
        foreach ($flightMembers as $flightMember) {
            
            $userSelect = $users->select()->whereEquals('id', $flightMember->employeeId);
            $employee = $users->getRow($userSelect);
            
            if ($employee == NULL) {
                continue;
            }
            
            $resultsToAdd = array();
            $existingResults = array();
            
            if ($flightMember->mainCrew == TRUE)
            {
                $positionSelect = $positionModel->select()
                ->whereEquals('positionId', $flightMember->positionId)
                ->whereEquals('typeName', 'position');
                
                $positionRows = $positionModel->getRows($positionSelect);
                
                foreach ($positionExtraRows as $positionExtraRow) {
                    array_push($resultsToAdd, $positionExtraRow);
                }
                
                foreach ($positionRows as $positionRow) {
                    
                    if (in_array($positionRow->resultId, $existingResults) == true) {
                        continue;
                    }
                    
                    $this->addFlightResult($flightRow, $flightMember, $positionRow);
                    
                    array_push($existingResults, $positionRow->resultId);
                }
                
                foreach ($positionExtraRows as $positionExtraRow) {
                    
                    if (in_array($positionExtraRow->resultId, $existingResults) == true) {
                        continue;
                    }
                    
                    $this->addFlightResult($flightRow, $flightMember, $positionExtraRow);
                    
                    array_push($existingResults, $positionExtraRow->resultId);
                }
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
                }
            }
            else if (($this->isContain('Второй пилот', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->secondPilotName = (string)$employee;
            }
            else if (($this->isContain('Пилот', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->secondPilotName = (string)$employee;
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
            } else if ($this->isContain('Тренируемый', $flightMember->positionName)) {
                if (in_array((string)$employee, $trained) == false) {
                    array_push($trained, (string)$employee);
                }
            }
            
            $this->sendMessage($flightMember->employeeId, $flightRow);
        }
        
        if (count($trained) > 0) {
            $flightRow->comments = 'Тр-ые: ' . implode(',', $trained);
        } else if ($this->isContain('Тр-ые:', $flightRow->comments)) {
            $flightRow->comments = '';
        }
        
        $flightRow->save();
    }
    
    public function timeForStatus ($statusName) {
        switch (strtoupper($statusName)) {
            case 'Я':
            case 'Н':
            case 'РВ':
            case 'С':
            case 'ВМ':
            case 'К':
            case 'ПК':
            case 'ПМ':
            case 'КСЭ':
            case 'У':
            case 'УВ':
            case 'ЛЧ':
            case 'НС':
            case 'НЗ':
                return '07:12';
                break;
                
            default:
                return '00:00';
        }
    }
    
    public function isHoliday($date) {
        $holidays = array(
                          '01-01',
                          '01-02',
                          '01-03',
                          '01-04',
                          '01-05',
                          '01-07',
                          '02-23',
                          '03-08',
                          '05-01',
                          '05-09',
                          '06-12',
                          '11-04'
                          );
        # 1, 2, 3, 4 и 5 января - Новогодние каникулы;
        # 7 января - Рождество Христово;
        # 23 февраля - День защитника Отечества;
        # 8 марта - Международный женский день;
        # 1 мая - Праздник Весны и Труда;
        # 9 мая - День Победы;
        # 12 июня - День России;
        # 4 ноября - День народного единства
        
        $localDate = clone $date;

        return in_array($localDate->format('m-d'), $holidays);
    }
    
    public function isWorkingDay ($date) {
        $localDate = clone $date;
        
        return (($this->isHoliday($localDate) == false) && ($localDate->format('N') != 6) && ($localDate->format('N') != 7));
    }
    
    public function isNextDayHoliday($date) {
        $localDate = clone $date;

        $localDate->add( new DateInterval('P1D') );

        return $this->isHoliday($localDate);
    }
    
    protected function addFlightResult($flight, $groupRow, $positionRow)
    {
        $typeId = $positionRow->resultId;
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('id', $typeId);
        $typeRow = $typeModel->getRow($typeSelect);
        
        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $flight->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $wstypeModel->getRow($wstypeSelect);
        
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
            $resultRow->planeId = $planeType->id;
            $resultRow->planeName = $planeType->Name;
            $resultRow->flightDate = $flight->flightStartDate;
            $resultRow->flightId = $flight->id;
            
            if ($this->isContain('Время работы', $resultRow->typeName)) {
                $resultRow->flightTime = '00:00';
                $resultRow->flightsCount = 0;
            } else {
                $resultRow->flightTime = '00:00';
                $resultRow->flightsCount = 1;
            }
            
            $resultRow->ownerId = $groupRow->employeeId;
            $resultRow->ownerName = $groupRow->employeeName;
            $resultRow->showInTotal = $positionRow->inTotal;
            
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

    public function sendMessage ($employeeId, $flightRow) {
                
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
        $mail->flightdescription = 'Маршрут: ' . $flightRow->routeName . ' (' . $flightRow->objectiveName . ') ' . $flightRow->flightStartDate . ' ' . $flightRow->flightStartTime;
        
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
            try {
                $mail->send();
            } catch (Exception $e) {
            }
        }
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
}

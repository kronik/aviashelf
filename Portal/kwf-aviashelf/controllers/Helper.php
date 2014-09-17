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
            $resultRow->reason = $planerstate->reason;
            
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
                    
                    if ((in_array($positionRow->resultId, $existingResults) == TRUE)) {
                        continue;
                    }
                    
                    $this->addFlightResult($flightRow, $flightMember, $positionRow);
                    
                    array_push($existingResults, $positionRow->resultId);
                }
                
                foreach ($positionExtraRows as $positionExtraRow) {
                    
                    if ((in_array($positionExtraRow->resultId, $existingResults) == TRUE)) {
                        continue;
                    }
                    
                    $this->addFlightResult($flightRow, $flightMember, $positionExtraRow);
                    
                    array_push($existingResults, $positionExtraRow->resultId);
                }
            }
            
            
            if (($this->isContain('КВС', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->firstPilotName = (string)$employee;
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
        }
        
        if (count($trained) > 0) {
            $flightRow->comments = 'Тр-ые: ' . implode(',', $trained);
        } else if ($this->isContain('Тр-ые:', $flightRow->comments)) {
            $flightRow->comments = '';
        }
        
        $flightRow->save();
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
        $specModel = Kwf_Model_Abstract::getInstance('Specialities');
        $linksModel = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $employeeModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeeSelect = $employeeModel->select()->whereEquals('id', $groupRow->employeeId);

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
            
            $employeeRow = $employeeModel->getRow($employeeSelect);

            $specSelect = $specModel->select()->whereEquals('id', $employeeRow->specId);
            $specRow = $specModel->getRow($specSelect);
            
            $linksSelect = $linksModel->select()->whereEquals('id', $employeeRow->subCompanyId);
            $linksRow = $linksModel->getRow($linksSelect);

            $prow = $specModel->getRow($specSelect);
            
            $row->speciality = (string)$prow;
            
            $prow = $m1->getRow($s);
            
            $row->department = $prow->value;
            
            $resultRow->flightId = $flight->id;
            $resultRow->flightsCount = 0;
            $resultRow->setsCount = 0;
            $resultRow->employeeId = $groupRow->employeeId;
            $resultRow->employeeName = $groupRow->employeeName;
            $resultRow->speciality = (string)$specRow;
            $resultRow->department = $linksRow->value;
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
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    public function updateWorkEntries($workId, $employeeId, $forced) {
        
        ini_set('memory_limit', "1024M");
        ini_set('max_execution_time', '0');
        set_time_limit(300); // 5 minutes
        
        $employeeworksModel = Kwf_Model_Abstract::getInstance('EmployeeWorks');
        $employeeworksSelect = $employeeworksModel->select()->whereEquals('workId', $workId);
        $employeework = $employeeworksModel->getRow($employeeworksSelect);
        
        if ($employeework != NULL && $forced == false) {
            return;
        }
        
        if ($forced) {
            $db = Zend_Registry::get('db');
            
            if ($employeeId == NULL) {
                $db->delete('employeeWorks', array('workId = ?' => $workId, 'autogenerated = ?' => '1'));
            } else {
                $db->delete('employeeWorks', array('workId = ?' => $workId, 'employeeId = ?' => $employeeId, 'autogenerated = ?' => '1'));
            }
        }
        
        $employeeSelectStmt = 'visible = 1 AND groupType = 1 AND timePerDay > \'00:00:00\'';
        
        if ($employeeId != NULL) {
            $employeeSelectStmt = $employeeSelectStmt . ' AND id = ' . $employeeId;
        }
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->where(new Kwf_Model_Select_Expr_Sql($employeeSelectStmt))->order('lastname');
        $employees = $employeesModel->getRows($employeesSelect);
        
        if (($employeeId != NULL) && (count($employees) == 0)) {
            return;
        }
        
        $statusModel = Kwf_Model_Abstract::getInstance('EmployeeWorkTypes');

        $worksModel = Kwf_Model_Abstract::getInstance('Works');
        $worksSelect = $worksModel->select()->whereEquals('id', $workId);
        $work = $worksModel->getRow($worksSelect);
        
        $startDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);
        $endDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);
        $endDate->add( new DateInterval('P1M') );
        
        $calendarModel = Kwf_Model_Abstract::getInstance('Calendar');
        $calendarSelect = $calendarModel->select()->where(new Kwf_Model_Select_Expr_Sql('startDate <= \'' . $endDate->format('Y-m-d') . '\' OR endDate >= \'' . $startDate->format('Y-m-d') . '\''));
        $calendar = $calendarModel->getRows($calendarSelect);
        
        $flightResultWorkModel = Kwf_Model_Abstract::getInstance('Flightresultwork');
        $flightResultWorkSelect = $flightResultWorkModel->select();
        $flightResultWorks = $flightResultWorkModel->getRows($flightResultWorkSelect);

        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        
        foreach ($employees as $employee) {
            
//            if ($employee->lastname != 'Афонин') {
//                
//                continue;
//            }
            
            $startDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);
            
            $resultsSelectStmt = 'flightDate <= \'' . $endDate->format('Y-m-d') . '\' AND flightDate >= \'' . $startDate->format('Y-m-d') . '\'  AND ownerId = ' . $employee->id;
            
            //                if ($employeeId != NULL) {
            //                    $resultsSelectStmt = $resultsSelectStmt . ' AND ownerId = ' . $employeeId;
            //                }
            
            $resultsModel = Kwf_Model_Abstract::getInstance('Flightresults');
            $resultsSelect = $resultsModel->select()->where(new Kwf_Model_Select_Expr_Sql($resultsSelectStmt));
            $results = $resultsModel->getRows($resultsSelect);
            
            $workSelectStmt = 'workId = ' . $workId . ' AND employeeId = ' . $employee->id;
            
            $employeeworksSelect = $employeeworksModel->select()->where(new Kwf_Model_Select_Expr_Sql($workSelectStmt));
            $employeeworks = $employeeworksModel->getRows($employeeworksSelect);
            
            $specModel = Kwf_Model_Abstract::getInstance('Specialities');
            
            $specRow = NULL;
            
            if ($employee->specId == NULL) {
                throw new Kwf_Exception_Client('Нет специальности у сотрудника: ' . (string)$employee);
            } else {
                $specSelect = $specModel->select()->whereEquals('id', $employee->specId);
                $specRow = $specModel->getRow($specSelect);
            }
            while ($startDate < $endDate) {
                
                $startDateStr = $startDate->format('Y-m-d');
                
//                if ($startDateStr != '2014-04-26') {
//                    $startDate->add( new DateInterval('P1D') );
//                    continue;
//                }
                
                $skipThisDay = false;
                
                foreach ($employeeworks as $employeework) {
                    if ($employeework->workDate == $startDateStr) {
                        $skipThisDay = true;
                        break;
                    }
                }
                
                if ($skipThisDay) {
                    $startDate->add( new DateInterval('P1D') );
                    continue;
                }
                
                $suggestedTypeName = '';
                $suggestedTypeMask = 0;
                
                $typeMaskSEIK         = 1;
                $typeMaskENL          = 2;
                $typeMaskGeneralWork  = 4;
                $typeMaskPSO          = 8;
                $typeMaskOtherCompany = 16;
                
                $calendarRecords = $this->findCalendarRecordsByEmployeeId($employee->id, $calendar, $startDate);
                $resultRecords = $this->findFlightResultRecordsByEmployeeId($employee->id, $results, $startDate);
                
                $s = new Kwf_Model_Select();
                $s->where(new Kwf_Model_Select_Expr_Sql("employeeId = " . $employee->id . " AND Hidden = 0"));
                
                $flightsSelect = $flightsModel->select()->where(new Kwf_Model_Select_Expr_Sql('flightStartDate = \'' . $startDate->format('Y-m-d') . '\''))->where(new Kwf_Model_Select_Expr_Child_Contains('Flightgroups', $s));
                
                $flights = $flightsModel->getRows($flightsSelect);

                foreach ($flights as $flight) {
                    if ($flight->subCompanyName == 'СЭИК') {
                        $suggestedTypeMask |= $typeMaskSEIK;
                    } else if ($flight->subCompanyName == 'ЭНЛ') {
                        $suggestedTypeMask |= $typeMaskENL;
                    } else {
                        $suggestedTypeMask |= $typeMaskOtherCompany;
                    }
                    
                    if ($flight->objectiveName == 'обеспеч. ПСО день. ' || $flight->objectiveName == 'обеспеч. ПСО ночь. ') {
                        $suggestedTypeMask |= $typeMaskPSO;
                    } else {
                    }
                }
                
                $newRow = $employeeworksModel->createRow();
                
                $newRow->workId = $workId;
                $newRow->autogenerated = true;
                
                $newRow->employeeId = $employee->id;
                $newRow->employeeName = (string)$employee;
                
                $newRow->typeId = 0; //Unused field
                
                $newRow->workDate = $startDate->format('Y-m-d');
                
                $newRow->workTime1 = '00:00';
                $newRow->workTime2 = '00:00';
                $newRow->workTime3 = '00:00';
                $newRow->workTime4 = '00:00';
                $newRow->workTime5 = '00:00';
                
                $newRow->speciality = (string)$specRow;

                if ($newRow->speciality == NULL) {
                    $newRow->speciality = '';
                }
                
                foreach ($resultRecords as $resultRecord) {
                    
                    if (($resultRecord->flightTime != '00:00') && ($resultRecord->flightTime != '00:00:00')) {
                        
                        if ($resultRecord->typeName == 'Налет общий') {
                            $suggestedTypeMask |= $typeMaskGeneralWork;
                        }
                        
                        if ($resultRecord->workOnHoliday == true) {
                            $newRow->subTypeName = 'РВ';
                        }
                        
                        foreach ($flightResultWorks as $flightResultWork) {
                            
                            if ($resultRecord->typeId == $flightResultWork->resultId) {
                        
                                switch ($flightResultWork->workId) {
                                    case 'workTime1':
                                        $newRow->workTime1 = $this->addStrTime($resultRecord->flightTime, $newRow->workTime1);
                                        break;
                                        
                                    case 'workTime2':
                                        $newRow->workTime2 = $this->addStrTime($resultRecord->flightTime, $newRow->workTime2);
                                        break;
                                        
                                    case 'workTime3':
                                        $newRow->workTime3 = $this->addStrTime($resultRecord->flightTime, $newRow->workTime3);
                                        break;
                                        
                                    case 'workTime4':
                                        $newRow->workTime4 = $this->addStrTime($resultRecord->flightTime, $newRow->workTime4);
                                        break;
                                        
                                    case 'workTime5':
                                        $newRow->workTime5 = $this->addStrTime($resultRecord->flightTime, $newRow->workTime5);
                                        break;
                                        
                                    default:
                                        break;
                                }
                            }
                        }
                    }
                }
                
                switch ($suggestedTypeMask) {
                    case $typeMaskGeneralWork:
                        $suggestedTypeName = 'КЯДР';
                        break;
                    case $typeMaskOtherCompany:
                        $suggestedTypeName = 'ЯДР';
                        break;
                    case $typeMaskOtherCompany | $typeMaskGeneralWork:
                        $suggestedTypeName = 'ЯП';
                        break;
                    case $typeMaskSEIK | $typeMaskOtherCompany:
                    case $typeMaskSEIK:
                        $suggestedTypeName = 'КЯДРс';
                        break;
                    case $typeMaskENL | $typeMaskOtherCompany:
                    case $typeMaskENL:
                        $suggestedTypeName = 'КЯДРэ';
                        break;
                    case $typeMaskSEIK | $typeMaskENL | $typeMaskOtherCompany:
                    case $typeMaskSEIK | $typeMaskENL:
                        $suggestedTypeName = 'КЯДРсэ';
                        break;
                    case $typeMaskOtherCompany | $typeMaskSEIK | $typeMaskGeneralWork:
                    case $typeMaskSEIK | $typeMaskGeneralWork:
                    case $typeMaskOtherCompany | $typeMaskSEIK | $typeMaskGeneralWork | $typeMaskPSO:
                    case $typeMaskSEIK | $typeMaskGeneralWork | $typeMaskPSO:
                        $suggestedTypeName = 'КЯПс';
                        break;
                    case $typeMaskOtherCompany | $typeMaskENL | $typeMaskGeneralWork:
                    case $typeMaskENL | $typeMaskGeneralWork:
                    case $typeMaskOtherCompany | $typeMaskENL | $typeMaskGeneralWork | $typeMaskPSO:
                    case $typeMaskENL | $typeMaskGeneralWork | $typeMaskPSO:
                        $suggestedTypeName = 'КЯПэ';
                        break;
                    case $typeMaskOtherCompany | $typeMaskSEIK | $typeMaskENL | $typeMaskGeneralWork:
                    case $typeMaskOtherCompany | $typeMaskSEIK | $typeMaskENL | $typeMaskGeneralWork | $typeMaskPSO:
                    case $typeMaskSEIK | $typeMaskENL | $typeMaskGeneralWork:
                    case $typeMaskSEIK | $typeMaskENL | $typeMaskGeneralWork | $typeMaskPSO:
                        $suggestedTypeName = 'КЯПсэ';
                        break;
                    case $typeMaskOtherCompany | $typeMaskSEIK | $typeMaskPSO:
                    case $typeMaskSEIK | $typeMaskPSO:
                        $suggestedTypeName = 'КЯДРЖс';
                        break;
                    case $typeMaskOtherCompany | $typeMaskENL | $typeMaskPSO:
                    case $typeMaskENL | $typeMaskPSO:
                        $suggestedTypeName = 'КЯДРЖэ';
                        break;
                    case $typeMaskOtherCompany | $typeMaskSEIK | $typeMaskENL | $typeMaskPSO:
                    case $typeMaskSEIK | $typeMaskENL | $typeMaskPSO:
                        $suggestedTypeName = 'КЯДРЖсэ';
                        break;
                        
                    default:
                        $suggestedTypeName = '';
                        break;
                }
                
                $isWorkingDay = $this->isWorkingDay($startDate);
                $isNextDayHoliday = $this->isNextDayHoliday($startDate);
                
                if (count($calendarRecords) == 0) {
                    if ($isWorkingDay) {
                        if ($isNextDayHoliday) {
                            $newRow->typeName = 'ЛЧ';
                        } else {
                            $newRow->typeName = 'Я';
                        }
                    } else {
                        $newRow->typeName = 'В';
                    }
                } else {
                    
                    foreach ($calendarRecords as $calendarRecord) {
                        if ($calendarRecord->employeeId == NULL) {

                            $newRow->typeName = $calendarRecord->statusName;
                            
                            if ($calendarRecord->statusName == 'В') {
                                $isWorkingDay = false;
                            }
                        }
                    }
                    
                    foreach ($calendarRecords as $calendarRecord) {
                        if ($calendarRecord->employeeId == $employee->id) {
                            
                            if ($calendarRecord->statusName == 'РВ') {
                                if ($newRow->typeName == NULL) {
                                    $newRow->typeName = $calendarRecord->statusName;
                                }
                            } else {
                                $newRow->typeName = $calendarRecord->statusName;
                            }
                        }
                    }
                }

                $statusSelect = $statusModel->select()->whereEquals('value', $newRow->typeName);
                $currentStatus = $statusModel->getRow($statusSelect);
                
                $needTime = $currentStatus->needTime;

                if ((0 === mb_strpos($newRow->typeName, 'В')) && ($newRow->typeName != 'В')) {
                    if ($isWorkingDay == false) {
                        $needTime = false;
                    }
                }
                
                if (($startDate->format('N') == 5) && ($needTime == true) &&
                    (($employee->timePerDay == '07:15') || (($employee->timePerDay == '07:15:00'))) &&
                    ($employee->sex == 'female')) {
                    
                    $timePerDay = '07:00';
                } else {
                    
                    if ($needTime == true) {
                        $timePerDay = $employee->timePerDay;
                    } else {
                        $timePerDay = '00:00:00';
                    }
                }
                
//                $needTime = $this->needTimeForStatus($newRow->typeName);
//                
//                $isWorkingDay = $needTime && $isWorkingDay; //($newRow->typeId != $holidayStatus->id);
                
//                if ($startDate->format('d') == 11) {
//                    p('Work: ' . ($isWorkingDay ? 'YES' : 'NO') . ' Next day holiday: ' . ($isNextDayHoliday ? 'YES' : 'NO') . ' Status: ' . $newRow->typeName);
//                    p($calendarRecords);
//                }
                
                if ($isWorkingDay && ($isNextDayHoliday || ($newRow->typeName == 'ЛЧ')) && $needTime) {
                    
                    $minutesPerDay = $this->minutesFromDateTime($timePerDay);
                    
                    if ($minutesPerDay > 59) {
                        $minutesPerDay -= 60;
                        
                        $timePerDay = $this->timeFromMinutes($minutesPerDay);
                    }
                    
                    $newRow->timePerDay = $timePerDay;
                } else {
                    if ($needTime) {
                        if ($newRow->typeName == 'ЛЧ') {
                            
                            $minutesPerDay = $this->minutesFromDateTime($timePerDay);
                            
                            if ($minutesPerDay > 59) {
                                $minutesPerDay -= 60;
                                
                                $timePerDay = $this->timeFromMinutes($minutesPerDay);
                            }
                        } else {
                            if ($isWorkingDay) {
                                $newRow->timePerDay = $timePerDay;
                            } else {
                                $newRow->timePerDay = '00:00:00';
                            }
                        }
                    } else {
                        $newRow->timePerDay = '00:00:00';
                    }
                }
                

                if (($newRow->timePerDay == '07:12:00' || $newRow->timePerDay == '07:12') &&
                    $newRow->typeName == 'Я') {
                    
                    $newRow->typeName = 'ЯУ';
                }
                
                if ($suggestedTypeName != '') {
                    $newRow->typeName = $suggestedTypeName;
                } else {
                    if ($suggestedTypeMask > 0) {
                        throw new Kwf_Exception_Client('Нет типа для маски: <' . $suggestedTypeMask . '>.');
                    }
                }
//                p($startDate->format('Y-m-d') . ': ' . $suggestedTypeName . ': ' . $newRow->typeName);

//                if ($employee->lastname == 'Афонин') {
//                    
//                    p($suggestedTypeName);
//                    p('Mask ' . $suggestedTypeMask);
//                    p($newRow);
//                    p($resultRecords);
//                }
                
                $newRow->save();
                $startDate->add( new DateInterval('P1D') );
            }
        }
    }
    
    protected function findFlightRecordsByDate($workDate, $flights) {
        
        $records = array();

        $workDateStr = $workDate->format('Y-m-d');

        foreach ($flights as $flight) {
            if ($flight->flightStartDate == $workDateStr) {
                array_push($records, $flight);
            }
        }
        
        return $records;
    }
    
    protected function findCalendarRecordsByEmployeeId ($employeeId, $calendarRecords, $workDate) {
        $records = array();
        
        foreach ($calendarRecords as $calendarRecord) {
            if (($calendarRecord->employeeId == $employeeId) || ($calendarRecord->employeeId == NULL)) {
                
                $startDate = new DateTime($calendarRecord->startDate);
                $endDate = new DateTime($calendarRecord->endDate);
                $endDate->add( new DateInterval('P1D') );
                
                if (($startDate <= $workDate) && ($workDate <= $endDate)) {
                    array_push($records, $calendarRecord);
                }
            }
        }
        
        return $records;
    }
    
    protected function findFlightResultRecordsByEmployeeId ($employeeId, $flightResults, $workDate) {
        $records = array();
        
        $workDateStr = $workDate->format('Y-m-d');
        
        foreach ($flightResults as $flightResult) {
            if (($flightResult->ownerId == $employeeId) && ($flightResult->flightDate == $workDateStr)) {
                array_push($records, $flightResult);
            }
        }
        
        return $records;
    }
    
    public function needTimeForStatus ($statusName) {
        switch (mb_strtoupper($statusName)) {
            case 'Я':
            case 'Н':
            case 'ВРД' :
//            case 'РВ':
            case 'С':
//            case 'ВМ':
            case 'К':
//            case 'ПК':
//            case 'ПМ':
            case 'КСЭ':
//            case 'У':
//            case 'УВ':
            case 'ЛЧ':
//            case 'НС':
//            case 'НЗ':
                return true;
                break;
                
            default:
                return false;
        }
    }
    
    public function addStrTime($time1, $time2) {
        
        $minutes1 = $this->minutesFromDateTime ($time1);
        $minutes2 = $this->minutesFromDateTime ($time2);

        return $this->timeFromMinutes($minutes1 + $minutes2);
    }
    
    public function timeFromMinutes($minutes) {
        $hoursPart = (int)($minutes / 60);
        $minutesPart = (int)($minutes % 60);
        
        $hoursStr = $hoursPart;
        $minutesStr = $minutesPart;
        
        if ($hoursPart < 10) {
            $hoursStr = '0' . $hoursStr;
        }
        
        if ($minutesPart < 10) {
            $minutesStr = '0' . $minutesStr;
        }
        
        return $hoursStr . ':' . $minutesStr;
    }
    
    public function minutesFromDateTime($date) {
        
        if (($date != NULL) && (strpos($date, ":") !== false)) {
            $timeParts = explode(":", $date);
            return ((int)$timeParts[0] * 60) + (int)$timeParts[1];
        } else {
            return 0;
        }
    }
}

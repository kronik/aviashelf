<?php
class Cli_NotificationsControllerController extends Kwf_Controller_Action {
    public function indexAction() {
        
        ini_set('memory_limit', "768M");
        set_time_limit(600);
        
        $tomorrow = new DateTime('NOW');
        $tomorrow->add( new DateInterval('P1D') );
        
        $flightPlanModel = Kwf_Model_Abstract::getInstance('Flightplans');
        $flightPlanSelect = $flightPlanModel->select()->where('`planDate` = ?', $tomorrow->format('Y-m-d'));
        
        $lastFlightPlan = $flightPlanModel->getRow($flightPlanSelect);
        
        if ($lastFlightPlan == NULL) {
            echo "Plan not found\n";
            exit;
        }

        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('planId', $lastFlightPlan->id);
        $flights = $flightsModel->getRows($flightsSelect);
        
        if (count($flights) == 0) {
            echo "Flights not found\n";
            exit;
        }
        
        $users = Kwf_Model_Abstract::getInstance('Employees');

        foreach ($flights as $flightRow) {

            $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
            $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $flightRow->id)->order('id');
            $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
            
            foreach ($flightMembers as $flightMember) {
                
                $userSelect = $users->select()->whereEquals('id', $flightMember->employeeId);
                $employee = $users->getRow($userSelect);
                
                if ($employee == NULL) {
                    continue;
                }

                /*
                if (($this->isContain('КВС', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE)) {
                    
                    if ($employee->userId != NULL) {
                        
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
                } */
                
                echo "Sent flight notification to: " . (string)$employee . "\n";
                
                $this->sendFlightMessage($flightMember->employeeId, $flightRow);
            }
        }
        
        echo "Done\n";
        exit;
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    public function sendFlightMessage ($employeeId, $flightRow) {
        
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
        
//        $mail->addTo('dmitry.klimkin@gmail.com');
        $mail->setFrom('puls@aviashelf.com', 'Авиашельф Пульс');
        $mail->setSubject('ПЗ: ' . $flightRow->number);
        
        if ($needToSend > 0) {
            try {
                $mail->send();
            } catch (Exception $e) {
            }
        }
    }
}
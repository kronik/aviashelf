<?php
class Cli_AutochecksControllerController extends Kwf_Controller_Action {
    public function indexAction() {
        
        ini_set('memory_limit', "768M");
        set_time_limit(600);
        
        $setsModel = Kwf_Model_Abstract::getInstance('Flightset');
        $setsSelect = $setsModel->select();

        $accessesModel = Kwf_Model_Abstract::getInstance('Flightaccesses');
        $accessesSelect = $accessesModel->select();

        $docsModel = Kwf_Model_Abstract::getInstance('Documents');
        $docsSelect = $docsModel->select();

        $rows = $setsModel->getRows($setsSelect);
        
        $todayLimit = new DateTime('NOW');
        $dateLimit = new DateTime('NOW');
        $dateLimit->sub( new DateInterval('P30D') );
        
        foreach ($rows as $row) {
            $startDate = new DateTime($row->setStartDate);
            $endDate = new DateTime($row->setEndDate);

            $description = 'Заход от ' . $startDate->format('d-m-Y') . ' по ' . $endDate->format('d-m-Y') . ' на ' . $row->wsTypeName . ' (' . $row->setMeteoTypeName . ' / ' . $row->setTypeName . ' / ' . $row->setName . ')';
            
            if ($endDate >= $todayLimit) {
                $this->sendMessage($row->employeeId, 'Проверка по заходам', $description, false);
            } else if ($endDate >= $dateLimit) {
                $this->sendMessage($row->employeeId, 'Проверка по заходам', $description, true);
            }
        }

        $rows = $accessesModel->getRows($accessesSelect);
        
        foreach ($rows as $row) {
            $startDate = new DateTime($row->accessDate);
            $endDate = new DateTime($row->accessEndDate);
            
            $description = 'Летная проверка от ' . $startDate->format('d-m-Y') . ' по ' . $endDate->format('d-m-Y') . ' на ' . $row->wsTypeName . ' (' . $row->accessTypeName . ' / ' . $row->accessName . ')';
            
            if ($endDate >= $todayLimit) {
                $this->sendMessage($row->employeeId, 'Летная проверка', $description, false);
            } else if ($endDate >= $dateLimit) {
                $this->sendMessage($row->employeeId, 'Летная проверка', $description, true);
            }
        }

        $rows = $docsModel->getRows($docsSelect);
        
        foreach ($rows as $row) {
            
            $startDate = new DateTime($row->startDate);
            $endDate = new DateTime($row->endDate);
            
            $description = 'Периодическая подготовка от ' . $startDate->format('d-m-Y') . ' по ' . $endDate->format('d-m-Y') . ' на ' . $row->typeName . ' (' . $row->number . ' / ' . $row->accessName . ')';
            
            if ($endDate >= $todayLimit) {
                $this->sendMessage($row->ownerId, 'Периодическая подготовка', $description, false);
            } else if ($endDate >= $dateLimit) {
                $this->sendMessage($row->ownerId, 'Периодическая подготовка', $description, true);
            }
        }

        echo "Done\n";
        
        exit;
    }
    
    public function sendMessage (int $employeeId, string $checkTypeName, string $description, bool $isWarning) {
        
        if ($employeeId <= 0) {
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
        
        $mail = new Kwf_Mail_Template($isWarning ? 'CheckWarningTemplate' : 'CheckFailedTemplate');
        $mail->fullname = (string)$employeeRow;
        $mail->checkname = $checkTypeName;
        $mail->checkdescription = $description;
        $mail->addTo($userRow->email);
        $mail->setFrom('admin@aviashelf.com', 'Авиашельф Пульс');
        $mail->send();
        
        //TODO: send SMS to $employeeRow->privatePhone
    }
}
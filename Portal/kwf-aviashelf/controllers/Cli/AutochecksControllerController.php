<?php
class Cli_AutochecksControllerController extends Kwf_Controller_Action {
    public function indexAction() {
        
        ini_set('memory_limit', "768M");
        set_time_limit(600);
        
        $setsModel = Kwf_Model_Abstract::getInstance('Flightset');
        $setsSelect = $setsModel->select()->where(new Kwf_Model_Select_Expr_Sql('setsCount > 0 AND finished = 0'));

        $accessesModel = Kwf_Model_Abstract::getInstance('Flightaccesses');
        $accessesSelect = $accessesModel->select()->where(new Kwf_Model_Select_Expr_Sql('finished = 0'));

        $docsModel = Kwf_Model_Abstract::getInstance('Documents');
        $docsSelect = $docsModel->select()->where(new Kwf_Model_Select_Expr_Sql('ownerName <> NULL AND isDocument = 0'));

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
            
            $description = 'Периодическая подготовка от ' . $startDate->format('d-m-Y') . ' по ' . $endDate->format('d-m-Y') . ' на ' . $row->typeName . ' (' . $row->number . ' / ' . $row->comment . ')';
            
            if ($endDate >= $todayLimit) {
                $this->sendMessage($row->ownerId, 'Периодическая подготовка', $description, false);
            } else if ($endDate >= $dateLimit) {
                $this->sendMessage($row->ownerId, 'Периодическая подготовка', $description, true);
            }
        }

        echo "Done\n";
        
        exit;
    }
    
    public function sendMessage ($employeeId, $checkTypeName, $description, $isWarning) {
        
        if ($employeeId == NULL) {
            return;
        }
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('id', $employeeId);

        $employeeRow = $employeesModel->getRow($employeesSelect);

        if (($employeeRow == NULL) || ($employeeRow->userId == NULL) ||
            ($employeeRow->userId <= 0) || ($employeeRow->isOOO == true)) {
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

        $mail = new Kwf_Mail_Template($isWarning ? 'CheckWarningTemplate' : 'CheckFailedTemplate');
        $mail->fullname = (string)$employeeRow;
        $mail->checkname = $checkTypeName;
        $mail->checkdescription = $description;
        
        if ($userRow->email != NULL) {
            $mail->addTo($userRow->email);
            $needToSend ++;
        }
        
        if ($phoneEmail != NULL) {
            $mail->addTo($phoneEmail);
            $needToSend ++;
        }
        
        //$mail->addTo('dmitry.klimkin@gmail.com');
        $mail->setFrom('notify@aviashelf.com', 'Авиашельф Пульс');
        $mail->setSubject($checkTypeName);
        
        if ($needToSend > 0) {
            $mail->send();
        }
        
//        echo "Message sent!\n";
                                
        if ($isWarning == FALSE) {
            $employeeRow->isAllowed = 0;
            $employeeRow->save();
        }
    }
}
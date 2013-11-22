<?php
class TrainingresultController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingResults';
    protected $_permissions = array('add');
    protected $_paging = 0;

    protected function _initFields()
    {        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()
        ->where(new Kwf_Model_Select_Expr_Sql("userId > 0 AND visible = 1"));
        
        $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(200)
        ->setShowNoSelection(true)
        ->setAllowBlank(false);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('TrainingGroups');
        $m2 = Kwf_Model_Abstract::getInstance('Trainings');
        $m3 = Kwf_Model_Abstract::getInstance('Employees');
        
        $row->trainingGroupId = $this->_getParam('groupId');
        
        $s = $m1->select()->whereEquals('id', $row->trainingGroupId);
        $prow = $m1->getRow($s);
        
        $row->trainingGroupName = (string)$prow;
        
        if ($prow->isTrial == true) {
            
            if ($this->isContain('Самоподготовка', $row->comment) == false) {
                $row->comment = $row->comment . ' (Самоподготовка)';
            }
        }
        
        $row->trainingId = $prow->trainingId;
        
        $s = $m2->select()->whereEquals('id', $row->trainingId);
        $prow = $m2->getRow($s);
        
        $row->trainingName = (string)$prow;
        
        $s = $m3->select()->whereEquals('id', $row->employeeId);
        $prow = $m3->getRow($s);
        
        $row->employeeName = (string)$prow;
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
    }
    
    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $this->createQuestionsSet($row);
    }
    
    protected function addQuestion($resultRow, $question, $seqNumber)
    {
        $m = Kwf_Model_Abstract::getInstance('TrainingContentQuestions');
        
        $row = $m->createRow();
        
        $row->resultId = $resultRow->id;
        $row->number = $seqNumber;
        $row->question = $question->question;
        $row->picture_id = $question->picture_id;
        
        $row->save();
        
        $answersModel = Kwf_Model_Abstract::getInstance('TrainingAnswers');
        $answersSelect = $answersModel->select()->whereEquals('questionId', $question->id);

        $answers = $answersModel->getRows($answersSelect);
        
        $correctScore = 0;

        foreach ($answers as $answer)
        {
            $this->addAnswer($row, $answer);
            
            if ($answer->isCorrect)
            {
                $correctScore += 1;
            }
        }
        
        return $correctScore;
    }
    
    protected function addAnswer($questionContentRow, $answerRow)
    {
        $m = Kwf_Model_Abstract::getInstance('TrainingContentAnswers');
        
        $row = $m->createRow();
        
        $row->contentQuestionId = $questionContentRow->id;
        $row->isSelected = false;
        $row->answer = $answerRow->answer;
        $row->isCorrect = $answerRow->isCorrect;
        
        $row->save();
    }
    
    protected function addTask($currentRow, $groupRow)
    {
        $users = Kwf_Model_Abstract::getInstance('Employees');
        $s = $users->select()->whereEquals('id', $currentRow->employeeId);
        $employee = $users->getRow($s);
        
        if ($employee->userId == NULL)
        {
            return;
        }
        
        $m = Kwf_Model_Abstract::getInstance('Tasks');
        
        $row = $m->createRow();
        
        $row->title = "Необходимо пройти тест";
        $row->description = "Необходимо пройти тест в группе: " . $groupRow->title;
        $row->startDate = $groupRow->startDate;
        $row->endDate = $groupRow->endDate;
        $row->userId = $employee->userId;
        
        $row->save();
        
        $currentRow->taskId = $row->id;
    }
    
    protected function createQuestionsSet(Kwf_Model_Row_Interface $row)
    {
        ini_set('memory_limit', "768M");
        set_time_limit(600);
        
        $questionsModel = Kwf_Model_Abstract::getInstance('TrainingQuestions');
        $questionsSelect = $questionsModel->select()->whereEquals('trainingId', $row->trainingId);

        $groupModel = Kwf_Model_Abstract::getInstance('TrainingGroups');
        $groupSelect = $groupModel->select()->whereEquals('id', $row->trainingGroupId);

        $groupRow = $groupModel->getRow($groupSelect);

        $questions = $questionsModel->getRows($questionsSelect);

        $count = 0;
        $correctScore = 0;
        
        $selectedQuestions = array();
        
        if (($groupRow->questions > count($questions)) || ($groupRow->questions == 0) || ($groupRow->questions == NULL)) {
            $groupRow->questions = count($questions);
            $groupRow->save();
        }
        
        if ($groupRow->questions < count($questions)) {
            do {
                $nextQuestionIdx = rand(0, count($questions) - 1);
                
                if (in_array($nextQuestionIdx, $selectedQuestions)) {
                    continue;
                }
     
                $question = $questions[$nextQuestionIdx];
                
                $count += 1;
                array_push($selectedQuestions, $nextQuestionIdx);
                
                $correctScore += $this->addQuestion($row, $question, $count);
            } while ((count($selectedQuestions) < $groupRow->questions) && (count($questions) > $groupRow->questions));
        } else {
            for ($index = 0; $index < count($questions) - 1; $index++) {
                $correctScore += $this->addQuestion($row, $questions[$index], $index + 1);
            }
        }
        
        $this->addTask($row, $groupRow);
        $this->sendMessage($row->employeeId, $groupRow);

        $row->totalScore = $correctScore;
        $row->save();
    }
    
    public function sendMessage ($employeeId, $groupRow) {
        
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
        
        $mail = new Kwf_Mail_Template('NewTrainingTemplate');
        
        $mail->fullname = (string)$employeeRow;
        $mail->training = $groupRow->trainingName;
        $mail->trainingdescription = "Курс в группе: " . $groupRow->title . ' c ' . $groupRow->startDate . ' по ' . $groupRow->endDate;
        
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
        $mail->setSubject('Курс: ' . $groupRow->trainingName);
        
        if ($needToSend > 0) {
            $mail->send();
        }
    }
}

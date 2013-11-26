<?php
    require_once 'FormEx.php';

class GrouppersonController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_modelName = 'GroupPersons';
    protected $_permissions = array('add', 'xls', 'save');
    protected $_paging = 0;
    protected $_buttons = array ('xls');

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
        $m3 = Kwf_Model_Abstract::getInstance('Employees');
        
        $s = $m1->select()->whereEquals('id', $row->trainingGroupId);
        $prow = $m1->getRow($s);
        
        $row->trainingGroupName = (string)$prow;
        
        if ($prow->isTrial == true) {
            
            if ($this->isContain('Самоподготовка', $row->comment) == false) {
                $row->comment = $row->comment . ' (Самоподготовка)';
            }
        }
                
        $s = $m3->select()->whereEquals('id', $row->employeeId);
        $prow = $m3->getRow($s);
        
        $row->employeeName = (string)$prow;
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    public function jsonDeleteAction()
    {
        // TODO: Add for valid delete operation
        
        
        
//        $row = $this->_form->getRow();
//        
//        if ($row->currentScore != NULL && $row->currentScore > 0) {
//            throw new Kwf_Exception_Client('Нельзя удалить сотрудника, который уже прошел тест.');
//        }
        
        parent::jsonDeleteAction();
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->trainingGroupId = $this->_getParam('groupId');
        
        $resultsModel = Kwf_Model_Abstract::getInstance('GroupPersons');
        $resultsSelect = $resultsModel->select()->whereEquals('trainingGroupId', $row->trainingGroupId)->whereEquals('employeeId', $row->employeeId);

        $prow = $resultsModel->getRow($resultsSelect);
        
        if ($prow != NULL) {
            throw new Kwf_Exception_Client('Этот сотрудник уже включен в группу.');
        }

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $this->createQuestionsSet($row);
    }
    
    protected function addQuestion($resultRow, $question, $topicId, $seqNumber)
    {
        $m = Kwf_Model_Abstract::getInstance('TrainingContentQuestions');
        
        $questionRow = $m->createRow();
        
        $questionRow->resultId = $resultRow->id;
        $questionRow->number = $seqNumber;
        $questionRow->question = $question->question;
        $questionRow->picture_id = $question->picture_id;
        $questionRow->topicId = $topicId;
        
        $questionRow->save();
                
        $answersModel = Kwf_Model_Abstract::getInstance('TrainingAnswers');
        $answersSelect = $answersModel->select()->whereEquals('questionId', $question->id);

        $answers = $answersModel->getRows($answersSelect);
        
        $correctScore = 0;

        foreach ($answers as $answer)
        {
            $this->addAnswer($questionRow, $answer);
            
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
        
        $newAnswerRow = $m->createRow();
        
        $newAnswerRow->contentQuestionId = $questionContentRow->id;
        $newAnswerRow->isSelected = false;
        $newAnswerRow->answer = $answerRow->answer;
        $newAnswerRow->isCorrect = $answerRow->isCorrect;
        
        $newAnswerRow->save();
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
        
        $groupModel = Kwf_Model_Abstract::getInstance('TrainingGroups');
        $groupSelect = $groupModel->select()->whereEquals('id', $row->trainingGroupId);
        $groupRow = $groupModel->getRow($groupSelect);

        $groupTopicsModel = Kwf_Model_Abstract::getInstance('GroupTopics');
        $groupTopicsSelect = $groupTopicsModel->select()->whereEquals('groupId', $row->trainingGroupId);
        $groupTopicRows = $groupTopicsModel->getRows($groupTopicsSelect);

        $topicsModel = Kwf_Model_Abstract::getInstance('Trainings');
        $questionsModel = Kwf_Model_Abstract::getInstance('TrainingQuestions');
        
        foreach ($groupTopicRows as $groupTopicRow) {
        
            $correctScore = 0;
            $count = 0;

            $topicsSelect = $topicsModel->select()->whereEquals('id', $groupTopicRow->topicId);
            $topicsRow = $topicsModel->getRow($topicsSelect);
            
            $questionsSelect = $questionsModel->select()->whereEquals('trainingId', $groupTopicRow->topicId);
            $questions = $questionsModel->getRows($questionsSelect);
            
            if (($groupTopicRow->questions > count($questions)) || ($groupTopicRow->questions == 0) ||
                ($groupTopicRow->questions == NULL)) {
                
                $groupTopicRow->questions = count($questions);
                $groupTopicRow->save();
            }
            
            $personResults = Kwf_Model_Abstract::getInstance('PersonResults');
            
            $resultRow = $personResults->createRow();
            
            $resultRow->groupPersonId = $row->id;
            $resultRow->trainingId = $topicsRow->id;
            $resultRow->trainingName = $topicsRow->title;
            $resultRow->trainingGroupId = $groupRow->id;
            $resultRow->trainingGroupName = $groupRow->title;
            $resultRow->employeeId = $row->employeeId;
            $resultRow->employeeName = $row->employeeName;
            $resultRow->currentScore = 0;
            $resultRow->totalScore = 0;
            $resultRow->startDate = $groupRow->startDate;
            $resultRow->endDate = $groupRow->endDate;
            $resultRow->isTrial = $groupRow->isTrial;
            
            $resultRow->save();
            
            $selectedQuestions = array();
            
            if ($groupTopicRow->questions < count($questions)) {
                do {
                    $nextQuestionIdx = rand(0, count($questions) - 1);
                    
                    if (in_array($nextQuestionIdx, $selectedQuestions)) {
                        continue;
                    }
         
                    $question = $questions[$nextQuestionIdx];
                    
                    $count += 1;
                    array_push($selectedQuestions, $nextQuestionIdx);
                    
                    $correctScore += $this->addQuestion($resultRow, $question, $topicsRow->id, $count);
                } while ((count($selectedQuestions) < $groupTopicRow->questions) && (count($questions) > $groupTopicRow->questions));
            } else {
                for ($index = 0; $index < count($questions); $index++) {
                    $count += 1;
                    $correctScore += $this->addQuestion($resultRow, $questions[$index], $topicsRow->id, $count);
                }
            }
            
            $resultRow->totalScore = $correctScore;
            $resultRow->save();
        }
        
//        $this->addTask($row, $groupRow);
//        $this->sendMessage($row->employeeId, $groupRow);
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
        $mail->training = $groupRow->title;
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
        $mail->setSubject('Курс в группе: ' . $groupRow->title);
        
        if ($needToSend > 0) {
            $mail->send();
        }
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();
        
        $this->_progressBar = new Zend_ProgressBar(new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                                                   0, 100);
        $reporter = new Reporter ();
        
        $xls = PHPExcel_IOFactory::load("./templates/training_result_template.xls");
        
        $xls->setActiveSheetIndex(0);
        $firstSheet = $xls->getActiveSheet();
        
        $reporter->exportTrainingResultsToXls($xls, $firstSheet, $row, $this->_progressBar);
        
        $this->_progressBar->finish();
        
        return $xls;
    }
}

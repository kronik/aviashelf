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
        
        $row->trainingId = $prow->trainingId;
        
        $s = $m2->select()->whereEquals('id', $row->trainingId);
        $prow = $m2->getRow($s);
        
        $row->trainingName = (string)$prow;
        
        $s = $m3->select()->whereEquals('id', $row->employeeId);
        $prow = $m3->getRow($s);
        
        $row->employeeName = (string)$prow;
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
                
        do
        {            
            $nextQuestionIdx = rand(0, count($questions) - 1);
            
            if (in_array($nextQuestionIdx, $selectedQuestions))
            {
                continue;
            }
 
            $question = $questions[$nextQuestionIdx];
            
            $count += 1;
            array_push($selectedQuestions, $nextQuestionIdx);
            
            $correctScore += $this->addQuestion($row, $question, $count);
        }while ((count($selectedQuestions) < $groupRow->questions) && (count($questions) > $groupRow->questions));
        
        $this->addTask($row, $groupRow);
        
        $row->totalScore = $correctScore;
        $row->save();
    }
}

<?php
    require_once 'GridEx.php';

class MyanswersController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'TrainingContentAnswers';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('save');
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_columns->add(new Kwf_Grid_Column_Checkbox('isSelected', trlKwf('Correct')))->setWidth(100)
        ->setEditor(new Kwf_Form_Field_Checkbox());
        $this->_columns->add(new Kwf_Grid_Column('answer', trlKwf('Answer')))->setWidth(1500);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['contentQuestionId = ?'] = $this->_getParam('questionId');
        
        return $ret;
    }
    
    protected function _afterSave(Kwf_Model_Row_Interface $row, $submitRow)
    {
        $question = $row->getParentRow('TrainingContentQuestion');
        $result = $question->getParentRow('PersonResult');
        $groupPerson = $result->getParentRow('GroupPerson');
        
        $questionsModel = Kwf_Model_Abstract::getInstance('TrainingContentQuestions');
        $questionsSelect = $questionsModel->select()->whereEquals('resultId', $result->id)->whereNotEquals('topicId', 0);
        
        $questions = $questionsModel->getRows($questionsSelect);
        
        $groupModel = Kwf_Model_Abstract::getInstance('GroupTopics');
        $groupSelect = $groupModel->select()->whereEquals('groupId', $result->trainingGroupId)->whereEquals('topicId', $result->trainingId);
        
        $groupInfo = $groupModel->getRow($groupSelect);
        
        $totalScore = 0;
        $numberOfPassedQuestions = 0;
        
        foreach ($questions as $question)
        {
            $answersModel = Kwf_Model_Abstract::getInstance('TrainingContentAnswers');
            $answersSelect = $answersModel->select()->whereEquals('contentQuestionId', $question->id);
            
            $answers = $answersModel->getRows($answersSelect);
            $questionIsAnswered = 0;

            foreach ($answers as $answer)
            {
                if ($answer->isSelected)
                {
                    if ($answer->isCorrect)
                    {
                        $totalScore += 1;
                    }
                    else
                    {
                        $totalScore -= 1;
                    }
                    $questionIsAnswered += 1;
                }
                else
                {
                    if ($answer->isCorrect)
                    {
                        $totalScore -= 1;
                    }
                }
            }
            
            if ($questionIsAnswered > 0)
            {
                $numberOfPassedQuestions += 1;
            }
        }
        
        if ($numberOfPassedQuestions == count($questions))
        {
            $scoreInPercents = ($totalScore * 100.0) / $result->totalScore;
            
            $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');

            if ($groupInfo->isDifGrade == false)
            {
                if ($scoreInPercents >= 80)
                {
                    $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Оценки' AND value = 'зачет'"));
                    $gradeRow = $typeModel->getRow($typeSelect);
                    
                    if ($gradeRow != NULL)
                    {
                        $result->gradeId = $gradeRow->id;
                    }
                    
                    $result->gradeName = 'зачет';

                } else {
                    $result->gradeName = 'незачет';
                }
            }
            else
            {
                if ($scoreInPercents >= 90)
                {
                    $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Оценки' AND value = 'пять'"));
                    $gradeRow = $typeModel->getRow($typeSelect);
                    
                    if ($gradeRow != NULL)
                    {
                        $result->gradeId = $gradeRow->id;
                    }
                    $result->gradeName = 'пять';
                }
                else if ($scoreInPercents >= 80)
                {
                    $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Оценки' AND value = 'четыре'"));
                    $gradeRow = $typeModel->getRow($typeSelect);
                    
                    if ($gradeRow != NULL)
                    {
                        $result->gradeId = $gradeRow->id;
                    }
                    
                    $result->gradeName = 'четыре';

                }
                else if ($scoreInPercents >= 75)
                {
                    $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Оценки' AND value = 'три'"));
                    $gradeRow = $typeModel->getRow($typeSelect);
                    
                    if ($gradeRow != NULL)
                    {
                        $result->gradeId = $gradeRow->id;
                    }
                    
                    $result->gradeName = 'три';
                }
                else
                {
                    $result->gradeId = 0;
                    $result->gradeName = 'два';
                }
            }
            
            $today = new DateTime('NOW');

            $result->recordDate = $today->format('Y-m-d');
            $result->currentScore = $totalScore;
            $result->save();

//
//            $task = $result->getParentRow('Task');
//            
//            if ($task != NULL)
//            {
//                $task->status = 1;
//                $task->save();
//            }
            
            if ($result->gradeId != 0)
            {
                $trainingsModel = Kwf_Model_Abstract::getInstance('Trainings');
                $trainingsSelect = $trainingsModel->select()->whereEquals('id', $result->trainingId);
                $training = $trainingsModel->getRow($trainingsSelect);
                
                $typeSelect = NULL;
                
                if (($training->docTypeId != 0) && ($result->isTrial == false))
                {
                    $typeSelect = $typeModel->select()->whereEquals('id', $training->docTypeId); 
                }

                if ($typeSelect != NULL)
                {
                    $typeRow = $typeModel->getRow($typeSelect);
                    
                    $m = Kwf_Model_Abstract::getInstance('Documents');
                    
                    $docRow = $m->createRow();
                    
                    $docRow->typeId = $typeRow->id;
                    $docRow->typeName = $typeRow->value;
                    $docRow->gradeId = $result->gradeId;
                    $docRow->gradeName = $result->gradeName;
                    $docRow->gradeVisible = 1;
                    $docRow->comment = $training->title . ': ' . $result->trainingGroupName;
                    $docRow->companyId = 0;
                    $docRow->startDate = $today->format('Y-m-d');
                    $docRow->ownerId = $result->employeeId;
                    $docRow->ownerName = $result->employeeName;

                    $docRow->save();
                }
            }
        }
        
        if ($numberOfPassedQuestions == count($questions)) {
            //throw new Kwf_Exception_Client('Тест окончен.');

            //Kwf_Util_Redirect::redirect('/myresults');
        }
    }
}

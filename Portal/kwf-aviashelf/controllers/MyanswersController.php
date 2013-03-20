<?php
class MyanswersController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'TrainingContentAnswers';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('save');
    
    protected function _initColumns()
    {
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
        $question = $row->getParentRow('TrainingContentQuestions');
        $result = $question->getParentRow('TrainingResult');
        $group = $result->getParentRow('TrainingGroup');
        
        $questionsModel = Kwf_Model_Abstract::getInstance('TrainingContentQuestions');
        $questionsSelect = $questionsModel->select()->whereEquals('resultId', $result->id);
        
        $questions = $questionsModel->getRows($questionsSelect);
        
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
                    $questionIsAnswered += 1;
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

            if ($group->isDifGrade == 0)
            {
                if ($scoreInPercents >= 51)
                {
                    $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Оценки' AND value = 'зачет'"));
                    $gradeRow = $typeModel->getRow($typeSelect);
                    
                    if ($gradeRow != NULL)
                    {
                        $result->gradeId = $gradeRow->id;
                        $result->gradeName = $gradeRow->value;
                    }
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
                        $result->gradeName = $gradeRow->value;
                    }
                }
                else if ($scoreInPercents >= 75)
                {
                    $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Оценки' AND value = 'четыре'"));
                    $gradeRow = $typeModel->getRow($typeSelect);
                    
                    if ($gradeRow != NULL)
                    {
                        $result->gradeId = $gradeRow->id;
                        $result->gradeName = $gradeRow->value;
                    }
                }
                else if ($scoreInPercents >= 51)
                {
                    $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Оценки' AND value = 'три'"));
                    $gradeRow = $typeModel->getRow($typeSelect);
                    
                    if ($gradeRow != NULL)
                    {
                        $result->gradeId = $gradeRow->id;
                        $result->gradeName = $gradeRow->value;
                    }
                }
                else
                {
                    $result->gradeId = 0;
                    $result->gradeName = trlKwf('Too bad');
                }
            }
            
            $task = $result->getParentRow('Task');
            
            if ($task != NULL)
            {
                $task->status = 1;
                $task->save();
            }
            
            if ($result->gradeId != 0)
            {
                $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Типы документов' AND value = 'Сертификат о прохождении теста'"));
                $typeRow = $typeModel->getRow($typeSelect);

                $m = Kwf_Model_Abstract::getInstance('Documents');
                
                $row = $m->createRow();
                
                $row->typeId = $typeRow->id;
                $row->typeName = $typeRow->value;
                $row->gradeId = $result->gradeId;
                $row->gradeName = $result->gradeName;
                $row->gradeVisible = 1;
                $row->comment = $result->trainingName . ' : ' . $result->trainingGroupName;
                $row->companyId = 0;
                $row->startDate = = date('d-m-Y H:i:s');
                
                $row->save();
            }
        }
                
        $result->currentScore = $totalScore;
        $result->save();
    }
}

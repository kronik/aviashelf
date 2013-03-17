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
        
        $questionsModel = Kwf_Model_Abstract::getInstance('TrainingContentQuestions');
        $questionsSelect = $questionsModel->select()->whereEquals('resultId', $result->id);
        
        $questions = $questionsModel->getRows($questionsSelect);
        
        $totalScore = 0;
        
        foreach ($questions as $question)
        {
            $answersModel = Kwf_Model_Abstract::getInstance('TrainingContentAnswers');
            $answersSelect = $answersModel->select()->whereEquals('contentQuestionId', $question->id);
            
            $answers = $answersModel->getRows($answersSelect);

            foreach ($answers as $answer)
            {
                if ($answer->isCorrect && $answer->isSelected)
                {
                    $totalScore += 1;
                }
            }
        }
        
        $result->currentScore = $totalScore;
        $result->save();
        
        // TODO: Implement setting the grade 
    }
}

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
}

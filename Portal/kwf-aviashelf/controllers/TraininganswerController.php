<?php
class TraininganswerController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingAnswers';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_HtmlEditor('answer', trlKwf('Text')))
        ->setWidth(650)
        ->setHeight(300)
        ->setMaxLength(3000)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('isCorrect', trlKwf('Correct')));
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->questionId = $this->_getParam('questionId');
    }
}

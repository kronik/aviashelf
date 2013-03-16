<?php
class MyquestionController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingContentQuestions';
    protected $_permissions = array();
    protected $_paging = 0;

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_ImageViewer('picture_id', trlKwf('Image'), 'Picture'));
        
        $this->_form->add(new Kwf_Form_Field_ShowField('question', trlKwf('Text')))
        ->setWidth(650)
        ->setHeight(300);
    }
}

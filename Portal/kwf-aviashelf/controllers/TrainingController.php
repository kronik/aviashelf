<?php
class TrainingController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Trainings';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
        ->setWidth(650)
        ->setMaxLength(100)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(650)
        ->setMaxLength(300)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('description', trlKwf('Description')))
        ->setWidth(650)
        ->setHeight(70)
        ->setMaxLength(1000)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_HtmlEditor('body', trlKwf('Text')))
        ->setWidth(650)
        ->setHeight(300)
        ->setMaxLength(65000)
        ->setAllowBlank(false);
    }
}

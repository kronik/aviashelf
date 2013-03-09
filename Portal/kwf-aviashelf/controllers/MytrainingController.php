<?php
class MytrainingController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Trainings';
    protected $_permissions = array();
    protected $_buttons = array();

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_ShowField('number', trlKwf('Number')))
        ->setWidth(650);
        
        $this->_form->add(new Kwf_Form_Field_ShowField('title', trlKwf('Title')))
        ->setWidth(650);
        
        $this->_form->add(new Kwf_Form_Field_ShowField('description', trlKwf('Description')))
        ->setWidth(650)
        ->setHeight(70);
        
        $this->_form->add(new Kwf_Form_Field_ShowField('body', trlKwf('Training')))
        ->setWidth(650)
        ->setHeight(300);
    }    
}

<?php
class MygroupController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingGroups';
    protected $_permissions = array();
    protected $_buttons = array();

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_ShowField('number', trlKwf('Number')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_ShowField('title', trlKwf('Title')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_ShowField('startDate', trlKwf('Start Date')));
        
        $this->_form->add(new Kwf_Form_Field_ShowField('endDate', trlKwf('End Date')));
        
//        $this->_form->add(new Kwf_Form_Field_ShowField('questions', trlKwf('Questions in session')))
//        ->setWidth(400);
    }
}

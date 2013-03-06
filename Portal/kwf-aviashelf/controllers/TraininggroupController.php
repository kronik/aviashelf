<?php
class TraininggroupController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingGroups';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_DateField('startDate', trlKwf('Start Date')))
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_DateField('endDate', trlKwf('End Date')))
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('questions', trlKwf('Questions in session')))
        ->setWidth(400)
        ->setAllowBlank(false);
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->trainingId = $this->_getParam('trainingId');
    }
}

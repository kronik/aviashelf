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
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('isDifGrade', trlKwf('Grade')));
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $m = Kwf_Model_Abstract::getInstance('Trainings');

        $row->trainingId = $this->_getParam('trainingId');
        
        $s = $m->select()->whereEquals('id', $row->trainingId);
        $prow = $m->getRow($s);
        
        $row->trainingName = (string)$prow;
    }
}

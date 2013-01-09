<?php
class CheckresultController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Checkresults';
    protected $_buttons = array();

    protected function _initFields()
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('visible', '1');
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select();
        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_TextField('checkDate', trlKwf('Date')))
        ->setWidth(400);

        $this->_form->add(new Kwf_Form_Field_TextField('employeeName', trlKwf('Employee')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_TextField('typeName', trlKwf('Type')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('description', trlKwf('Description')))
        ->setHeight(90)
        ->setWidth(400);
    }
}

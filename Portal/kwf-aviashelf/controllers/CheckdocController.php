<?php
class CheckdocController extends CheckController
{
    protected function _initFields()
    {
        $docTypeModel = Kwf_Model_Abstract::getInstance('Flightchecks');
        $docTypeSelect = $docTypeModel->select()->order('title');
        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Document type')))
        ->setValues($docTypeModel)
        ->setSelect($docTypeSelect)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Select('field', trlKwf('Field')))
        ->setValues(array('startDate' => trlKwf('Doc Start Date'), 'endDate' => trlKwf('Doc End Date')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('value', trlKwf('Days before')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('description', trlKwf('Description')))
        ->setWidth(400)
        ->setHeight(80);
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row = parent::updateReferences($row);
        $row->checkType = 'doc';
        $row->daysInPeriod = $row->value;
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $row = parent::updateReferences($row);
        $row->checkType = 'doc';
        $row->daysInPeriod = $row->value;
    }
}

<?php
class ChecktrainingController extends CheckController
{
    protected function _initFields()
    {
        $flightsModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $flightsSelect = $flightsModel->select()->whereEquals('name', 'Типы налета');
        
        $flightTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $flightTypeSelect = $flightTypeModel->select()->whereEquals('name', 'Типы полетов');
        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Flight')))
        ->setValues($flightsModel)
        ->setSelect($flightsSelect)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Select('subTypeId', trlKwf('Flight')))
        ->setValues($flightTypeModel)
        ->setSelect($flightTypeSelect)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Select('field', trlKwf('Check')))
        ->setValues(array('startDate' => trlKwf('Doc Start Date'), 'endDate' => trlKwf('Doc Start Date')))
        ->setAllowBlank(false)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('value', trlKwf('Value')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('daysInPeriod', trlKwf('Days in period')))
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_TextField('description', trlKwf('Description')))
        ->setWidth(400)
        ->setHeight(80);
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row = parent::updateReferences($row);
        $row->checkType = 'training';
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $row = parent::updateReferences($row);
        $row->checkType = 'training';
    }
}

<?php
class CheckController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Checks';
    protected $_permissions = array('save', 'add', 'delete');
    protected $_paging = 0;
    protected $_buttons = array('save');

    protected function _initFields()
    {                
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('visible', '1');
        
        $flightsModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $flightsSelect = $flightsModel->select()->whereEquals('name', 'Типы налета');
        
        $flightTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $flightTypeSelect = $flightTypeModel->select()->whereEquals('name', 'Типы полетов');
        
        $this->_form->add(new Kwf_Form_Field_Select('checkType', trlKwf('Type')))
        ->setValues(array('doc' => trlKwf('Document'), 'flight' => trlKwf('Flight'), 'training' => trlKwf('Training')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
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
        
        $this->_form->add(new Kwf_Form_Field_Select('field', trlKwf('Field')))
        ->setValues(array('startDate' => trlKwf('Start Date'), 'endDate' => trlKwf('End Date'), 'gradeName' => trlKwf('Grade')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('sign', trlKwf('Sign')))
        ->setValues(array('<' => trlKwf('<'), '<=' => trlKwf('<='), '=' => trlKwf('='), '>' => trlKwf('>'), '>=' => trlKwf('>=')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('value', trlKwf('Value')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('daysInPeriod', trlKwf('Days in period')))
        ->setWidth(400);

        $this->_form->add(new Kwf_Form_Field_Select('ownerId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');

        if ($row->typeId != NULL)
        {
            $s = $m1->select()->whereEquals('id', $row->typeId);
            $prow = $m1->getRow($s);
            $row->typeName = $prow->value;
        }
        
        if ($row->subTypeId != NULL)
        {
            $s = $m1->select()->whereEquals('id', $row->subTypeId);
            $prow = $m1->getRow($s);
            $row->subtypeName = $prow->value;
        }
        
        if ($row->ownerId != NULL)
        {
            $s = $m2->select()->whereEquals('id', $row->ownerId);
            $prow = $m2->getRow($s);
            $row->ownerName = $prow->lastname . ' ' . $prow->firstname . ' ' . $prow->middlename;
        }
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        
        if ($row->typeId != NULL)
        {
            $s = $m1->select()->whereEquals('id', $row->typeId);
            $prow = $m1->getRow($s);
            $row->typeName = $prow->value;
        }
        
        if ($row->subTypeId != NULL)
        {
            $s = $m1->select()->whereEquals('id', $row->subTypeId);
            $prow = $m1->getRow($s);
            $row->subtypeName = $prow->value;
        }
        
        if ($row->ownerId != NULL)
        {
            $s = $m2->select()->whereEquals('id', $row->ownerId);
            $prow = $m2->getRow($s);
            $row->ownerName = $prow->lastname . ' ' . $prow->firstname . ' ' . $prow->middlename;
        }
    }
}

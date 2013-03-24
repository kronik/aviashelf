<?php
class FlightfullresultController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightresults';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('visible', '1');
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Типы налета');
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Type')))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('ownerId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TimeField('flightTime', trlKwf('Time')))->setIncrement(5);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('showInTotal', trlKwf('Show in total')));
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->flightId = $this->_getParam('flightId');

        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        $m3 = Kwf_Model_Abstract::getInstance('Flights');

        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        $row->typeName = $prow->value;
        
        $s = $m3->select()->whereEquals('id', $this->_getParam('flightId'));
        $prow = $m3->getRow($s);
        
        $row->flightDate = $prow->flightStartDate;
        $row->planeId = $prow->planeId;
        $row->planeName = $prow->planeName;
        
        $s = $m2->select()->whereEquals('id', $row->ownerId);
        $prow = $m2->getRow($s);
        
        $row->ownerName = (string)$prow;
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        $m3 = Kwf_Model_Abstract::getInstance('Flights');
        
        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        $row->typeName = $prow->value;
        
        $s = $m3->select()->whereEquals('id', $this->_getParam('flightId'));
        $prow = $m3->getRow($s);
        
        $row->flightDate = $prow->flightStartDate;
        $row->planeId = $prow->planeId;
        $row->planeName = $prow->planeName;
        
        $s = $m2->select()->whereEquals('id', $row->ownerId);
        $prow = $m2->getRow($s);
        
        $row->ownerName = (string)$prow;
    }
}

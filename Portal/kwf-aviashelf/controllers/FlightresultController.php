<?php
class FlightresultController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightresults';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $wstypesModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypesSelect = $wstypesModel->select();
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Типы налета');
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Type')))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('planeId', trlKwf('WsType')))
        ->setValues($wstypesModel)
        ->setSelect($wstypesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_DateField('flightDate', trlKwf('Date')));
        
        $this->_form->add(new Kwf_Form_Field_TextField('flightTime', trlKwf('Time')))->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('flightsCount', 'Кол-во полетов'))->setWidth(400);

        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('showInTotal', trlKwf('Show in total')));
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Wstypes');
        $m3 = Kwf_Model_Abstract::getInstance('Employees');
        
        $s = $m3->select()->whereEquals('id', $this->_getParam('ownerId'));
        $prow = $m3->getRow($s);
        
        $row->ownerName = (string)$prow;
        
        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        $row->typeName = $prow->value;
        
        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->Name;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->ownerId = $this->_getParam('ownerId');

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
}

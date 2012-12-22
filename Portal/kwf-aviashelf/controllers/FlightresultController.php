<?php
class FlightresultController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightresults';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $wstypesModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypesSelect = $wstypesModel->select()->whereEquals('Hidden', '0');
        
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
        $this->_form->add(new Kwf_Form_Field_TextField('flightTime', trlKwf('Time')))->setWidth(73);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Wstypes');

        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        
        $row->ownerId = $this->_getParam('ownerId');
        $row->typeName = $prow->value;

        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->Name;
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Wstypes');

        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        
        $row->typeName = $prow->value;
        
        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->Name;
    }
}

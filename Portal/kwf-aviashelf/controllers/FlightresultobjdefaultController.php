<?php
class FlightresultobjdefaultController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightresultdefaults';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $positionModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $positionSelect = $positionModel->select()->whereEquals('name', 'Цель полета')->order('value');
        
        $this->_form->setLabelAlign('top');

        $this->_form->add(new Kwf_Form_Field_Select('positionId', 'Цель полета'))
        ->setValues($positionModel)
        ->setSelect($positionSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Типы налета');
        
        $this->_form->add(new Kwf_Form_Field_Select('resultId', 'Тип налета'))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('inTotal', trlKwf('Show in total')));
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row) {
    
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $s = $m1->select()->whereEquals('id', $row->positionId);
        $prow = $m1->getRow($s);
        $row->positionName = $prow->value;
                                                    
        $s = $m2->select()->whereEquals('id', $row->resultId);
        $prow = $m2->getRow($s);
        $row->resultName = $prow->value;
        
        $row->typeName = 'objective';
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

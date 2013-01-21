<?php
class FlightaccessController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightaccesses';
    protected $_permissions = array('save', 'add', 'delete');
    protected $_paging = 0;

    protected function _initFields()
    {
        $docsModel = Kwf_Model_Abstract::getInstance('Documents');
        $docsSelect = $docsModel->select()->whereEquals('ownerId', $this->_getParam('employeeId'))->order('value');
        
        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select()->whereEquals('Hidden', '0');
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Метеоминимумы')->order('value');
                
        $this->_form->add(new Kwf_Form_Field_Select('docId', trlKwf('Document')))
        ->setValues($docsModel)
        ->setSelect($docsSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('wsTypeId', trlKwf('WsType')))
        ->setValues($wstypeModel)
        ->setSelect($wstypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('accessId', 'Метеоминимум'))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
                
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);        
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        $m3 = Kwf_Model_Abstract::getInstance('Documents');

        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select()->whereEquals('id', $row->wsTypeId);
        $prow = $wstypeModel->getRow($wstypeSelect);
        $row->wsTypeName = $prow->Name;

        $s = $m1->select()->whereEquals('id', $row->accessId);
        $prow = $m1->getRow($s);
        $row->accessName = $prow->value;
        
        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        $row->employeeName = (string)$prow;
        
        $s = $m3->select()->whereEquals('id', $row->docId);
        $prow = $m3->getRow($s);
        $row->docName = (string)$prow;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->employeeId = $this->_getParam('employeeId');

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

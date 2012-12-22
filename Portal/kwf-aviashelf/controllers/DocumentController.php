<?php
class DocumentController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Documents';
    protected $_permissions = array('save', 'add');
    #protected $_buttons = array('save');
    protected $_paging = 0;

    protected function _initFields()
    {
        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->whereEquals('Hidden', '0');
        
        $docTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $docTypeSelect = $docTypeModel->select()->whereEquals('name', 'Типы документов');

        $this->_form->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
            ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Type')))
        ->setValues($docTypeModel)
        ->setSelect($docTypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_DateField('startDate', trlKwf('Doc Start Date')));
        $this->_form->add(new Kwf_Form_Field_DateField('endDate', trlKwf('Doc End Date')));

        $this->_form->add(new Kwf_Form_Field_Select('companyId', trlKwf('Spec Doc company')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_File('Picture', trlKwf('Photo')))
        ->setAllowOnlyImages(true);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $m = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $s = $m->select() //returns a Kwf_Model_Select object use new Kwf_Model_Select() alternatively
        ->whereEquals('id', $row->typeId);
        $prow = $m->getRow($s);
        
        $row->ownerId = $this->_getParam('ownerId');
        $row->typeName = $prow->value;
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $m = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $s = $m->select() //returns a Kwf_Model_Select object use new Kwf_Model_Select() alternatively
        ->whereEquals('id', $row->typeId);
        $prow = $m->getRow($s);
        
        $row->typeName = $prow->value;
    }
}

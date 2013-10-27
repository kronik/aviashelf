<?php
class TrainingController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Trainings';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
        ->setWidth(650)
        ->setMaxLength(100)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('type', trlKwf('Type')))
        ->setValues(array('Ми-8' => trlKwf('Ми-8'), 'Ми-8МТВ' => trlKwf('Ми-8МТВ'), 'Другое' => trlKwf('Questionary')))
        ->setWidth(650)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(650)
        ->setMaxLength(300)
        ->setAllowBlank(false);
        
        $docTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $docTypeSelect = $docTypeModel->select()->whereEquals('name', 'Типы проверок');
        
        $this->_form->add(new Kwf_Form_Field_Select('docTypeId', 'Тип проверки'))
        ->setValues($docTypeModel)
        ->setSelect($docTypeSelect)
        ->setWidth(650)
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('description', trlKwf('Description')))
        ->setWidth(650)
        ->setHeight(70)
        ->setMaxLength(1000)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_HtmlEditor('body', trlKwf('Text')))
        ->setWidth(650)
        ->setHeight(300)
        ->setMaxLength(65000)
        ->setAllowBlank(true);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m = Kwf_Model_Abstract::getInstance('Linkdata');
        
        if ($row->docTypeId != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->docTypeId);
            $prow = $m->getRow($s);
            
            $row->docTypeName = $prow->value;
        }
        else
        {
            $row->docTypeId = 0;
            $row->docTypeName = '';
        }
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

<?php
class TrainingController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Trainings';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $trainingTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $trainingTypeSelect = $trainingTypeModel->select()->whereEquals('name', 'Типы курсов');

        $this->_form->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
        ->setWidth(650)
        ->setMaxLength(100)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Type')))
        ->setValues($trainingTypeModel)
        ->setSelect($trainingTypeSelect)
        ->setWidth(650)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(650)
        ->setMaxLength(300)
        ->setAllowBlank(false);
        
        $docTypeModel = Kwf_Model_Abstract::getInstance('Flightchecks');
        $docTypeSelect = $docTypeModel->select()->order('title');
        
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
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Flightchecks');

        if ($row->docTypeId != NULL)
        {
            $s = $m2->select()->whereEquals('id', $row->docTypeId);
            $prow = $m2->getRow($s);
            
            $row->docTypeName = $prow->title;
            
            $s = $m1->select()->whereEquals('id', $row->typeId);
            $prow = $m1->getRow($s);

            $row->type = $prow->value;
        }
        else
        {
            $row->docTypeId = 0;
            $row->docTypeName = '';
            
            $row->typeId = 0;
            $row->type = '';
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

<?php
class StaffdocumentController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Documents';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $tabs = $this->_form->add(new Kwf_Form_Container_Tabs());
        $tabs->setActiveTab(0);
        
        // **** General Info
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('General Info'));
                
        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select();
        
        $docTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $docTypeSelect = $docTypeModel->select()->whereEquals('name', 'Типы документов');
        
        $docGradeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $docGradeSelect = $docGradeModel->select()->whereEquals('name', 'Оценки');
        
        $tab->fields->add(new Kwf_Form_Field_Select('typeId', trlKwf('Type')))
        ->setValues($docTypeModel)
        ->setSelect($docTypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_DateField('startDate', trlKwf('Doc Start Date')))
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_DateField('endDate', trlKwf('Doc End Date')))
        ->setAllowBlank(true);

        $tab->fields->add(new Kwf_Form_Field_Select('companyId', trlKwf('Spec Doc company')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Grade'));
        $fs->setCheckboxToggle(true);
        $fs->setCheckboxName('gradeVisible');        

        $gradeSelect = new Kwf_Form_Field_Select('gradeId', trlKwf('Grade'));
        $gradeSelect->setValues($docGradeModel);
        $gradeSelect->setSelect($docGradeSelect);
        $gradeSelect->setWidth(360);
        $gradeSelect->setAllowBlank(true);
        
        $fs->fields->add($gradeSelect);
        $tab->fields->add($fs);
        
        $tab->fields->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('File'));
        
        $tab->fields->add(new Kwf_Form_Field_File('Picture', trlKwf('File')))
        ->setShowPreview(false)
        ->setAllowOnlyImages(true);
        
        $tab->fields->add(new Kwf_Form_Field_ImageViewer('picture_id', trlKwf('Image'), 'Picture'));
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $m = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $s = $m->select()->whereEquals('id', $row->typeId);
        $prow = $m->getRow($s);
        
        $row->ownerId = $this->_getParam('ownerId');
        $row->typeName = $prow->value;
        
        if ($row->gradeId != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->gradeId);
            $prow = $m->getRow($s);
            $row->gradeName = $prow->value;
        }
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $m = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $s = $m->select()->whereEquals('id', $row->typeId);
        $prow = $m->getRow($s);
        
        $row->typeName = $prow->value;
        
        if ($row->gradeId != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->gradeId);
            $prow = $m->getRow($s);
            $row->gradeName = $prow->value;
        }
    }
}

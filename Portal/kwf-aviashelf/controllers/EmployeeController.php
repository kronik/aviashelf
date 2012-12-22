<?php
class EmployeeController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Employees';

    protected function _initFields()
    {
        $tabs = $this->_form->add(new Kwf_Form_Container_Tabs());
        $tabs->setActiveTab(0);

        // **** General Info
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('General Info'));

        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Personal data'));

        $tab->fields->add($fs);

        $fs->fields->add(new Kwf_Form_Field_File('Picture', trlKwf('Photo')))
        ->setAllowOnlyImages(true);
        
        $fs->fields->add(new Kwf_Form_Field_TextField('firstname', trlKwf('Firstname')))
            ->setAllowBlank(false)
            ->setWidth(400);
        $fs->fields->add(new Kwf_Form_Field_TextField('lastname', trlKwf('Lastname')))
            ->setWidth(400)
            ->setAllowBlank(false);
        $fs->fields->add(new Kwf_Form_Field_TextField('middlename', trlKwf('Middlename')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs->fields->add(new Kwf_Form_Field_TextField('firstnameEn', trlKwf('Firstname En')))
        ->setAllowBlank(false)
        ->setWidth(400);
        $fs->fields->add(new Kwf_Form_Field_TextField('lastnameEn', trlKwf('Lastname En')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs->fields->add(new Kwf_Form_Field_Select('sex', trlKwf('Sex')))
            ->setValues(array('male' => trlKwf('Male'), 'female' => trlKwf('Female')))
            ->setWidth(90)
            ->setAllowBlank(false);
        $fs->fields->add(new Kwf_Form_Field_DateField('birthDate', trlKwf('Birthdate')));
        $fs->fields->add(new Kwf_Form_Field_TextField('birthPlace', trlKwf('Birthplace')))
        ->setWidth(400);
        
        $fs->fields->add(new Kwf_Form_Field_TextField('privateAddress', trlKwf('Address')))
        ->setWidth(400);
        $fs->fields->add(new Kwf_Form_Field_TextField('privatePhone', trlKwf('Phone')))
        ->setWidth(400);
        
        $fs->fields->add(new Kwf_Form_Field_TextField('INN', trlKwf('INN')))
        ->setWidth(400);
        
        $fs->fields->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $fs->fields->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Active')));
    
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Specialization'));
        
        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Specialization'));
        
        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->whereEquals('Hidden', '0');
        
        $fs->fields->add(new Kwf_Form_Field_Select('currentCompanyId', trlKwf('Current company')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $linkModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $linkSelect = $linkModel->select()->whereEquals('name', 'Подразделения');
        
        $fs->fields->add(new Kwf_Form_Field_Select('subCompanyId', trlKwf('Subcompany')))
        ->setValues($linkModel)
        ->setSelect($linkSelect)
        ->setWidth(400);
        
        $fs->fields->add(new Kwf_Form_Field_TextField('companyRegNumber', trlKwf('Company Number')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs->fields->add(new Kwf_Form_Field_TextField('orderNumber', trlKwf('Order Number')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs->fields->add(new Kwf_Form_Field_DateField('orderDate', trlKwf('Order Date')));
        
        $fs->fields->add(new Kwf_Form_Field_Checkbox('isAllowed', trlKwf('Allowed')));
        
        $fs->fields->add(new Kwf_Form_Field_NumberField('classNumber', trlKwf('Class')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs->fields->add(new Kwf_Form_Field_TextField('classDocNumber', trlKwf('Class Doc Number')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs->fields->add(new Kwf_Form_Field_DateField('classDocDate', trlKwf('Class Doc Date')));
        
        $fs->fields->add(new Kwf_Form_Field_Select('classCompanyId', trlKwf('Class company')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs->fields->add(new Kwf_Form_Field_NumberField('totalTime', trlKwf('Total Time')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $fs->fields->add(new Kwf_Form_Field_DateField('totalTimeDate', trlKwf('Total Time Date')));
        
        $specModel = Kwf_Model_Abstract::getInstance('Specialities');
        $specSelect = $specModel->select()->whereEquals('Hidden', '0');
        
        $fs->fields->add(new Kwf_Form_Field_Select('specId', trlKwf('Speciality')))
        ->setValues($specModel)
        ->setSelect($specSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $subSpecModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $subSpecSelect = $subSpecModel->select()->whereEquals('name', 'Должности');
        
        $fs->fields->add(new Kwf_Form_Field_Select('positionId', trlKwf('Spec Position')))
        ->setValues($subSpecModel)
        ->setSelect($subSpecSelect)
        ->setWidth(400);
        
        $fs->fields->add(new Kwf_Form_Field_Checkbox('isLeader', trlKwf('Leader')));
        
        $fs->fields->add(new Kwf_Form_Field_TextField('failsDocNumber', trlKwf('Fails Doc Number')))
        ->setWidth(400);
        
        
        // TODO: Replace with select dropdown
        $fs->fields->add(new Kwf_Form_Field_TextField('specTypeId', trlKwf('SpecType')))
        ->setWidth(400);

        
        $tab->fields->add($fs);        
    }
}

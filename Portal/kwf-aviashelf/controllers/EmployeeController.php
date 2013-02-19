<?php
class EmployeeController extends Kwf_Controller_Action_Auto_Form
{
    #protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Employees';

    protected function _initFields()
    {
        $tabs = $this->_form->add(new Kwf_Form_Container_Tabs());
        $tabs->setBorder(true);
        $tabs->setActiveTab(0);

        // **** General Info
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Personal data'));

        $tab->fields->add(new Kwf_Form_Field_ImageViewer('picture_id', trlKwf('Photo'), 'Picture'));

        $tab->fields->add(new Kwf_Form_Field_File('Picture', trlKwf('File')))
        ->setShowPreview(false)
        ->setAllowOnlyImages(true);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('firstname', trlKwf('Firstname')))
            ->setAllowBlank(false)
            ->setWidth(400);
        $tab->fields->add(new Kwf_Form_Field_TextField('lastname', trlKwf('Lastname')))
            ->setWidth(400)
            ->setAllowBlank(false);
        $tab->fields->add(new Kwf_Form_Field_TextField('middlename', trlKwf('Middlename')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('firstnameEn', trlKwf('Firstname En')))
        ->setAllowBlank(false)
        ->setWidth(400);
        $tab->fields->add(new Kwf_Form_Field_TextField('lastnameEn', trlKwf('Lastname En')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_Select('sex', trlKwf('Sex')))
            ->setValues(array('male' => trlKwf('Male'), 'female' => trlKwf('Female')))
            ->setWidth(90)
            ->setAllowBlank(false);
        $tab->fields->add(new Kwf_Form_Field_DateField('birthDate', trlKwf('Birthdate')));
        $tab->fields->add(new Kwf_Form_Field_TextField('birthPlace', trlKwf('Birthplace')))
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('registerAddress', trlKwf('Reg Address')))
        ->setWidth(400);
        $tab->fields->add(new Kwf_Form_Field_TextField('privatePhone', trlKwf('Phone')))
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('INN', trlKwf('INN')))
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Active')));
    
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Specialization'));
        $tab->setLabelAlign('top');

        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->whereEquals('Hidden', '0');
        
        $tab->fields->add(new Kwf_Form_Field_Select('currentCompanyId', trlKwf('Current company')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $linkModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $linkSelect = $linkModel->select()->whereEquals('name', 'Подразделения');
        
        $tab->fields->add(new Kwf_Form_Field_Select('subCompanyId', trlKwf('Subcompany')))
        ->setValues($linkModel)
        ->setSelect($linkSelect)
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('companyRegNumber', trlKwf('Company Number')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('orderNumber', trlKwf('Order Number')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_DateField('orderDate', trlKwf('Order Date')));
        
        $tab->fields->add(new Kwf_Form_Field_Checkbox('isAllowed', trlKwf('Allowed')));

        $tab->fields->add(new Kwf_Form_Field_TextField('totalTimeStr', trlKwf('Total Time')))
        ->setWidth(400)
        ->setAllowBlank(true);
        
        $tab->fields->add(new Kwf_Form_Field_DateField('totalTimeDate', trlKwf('Total Time Date')));
        
        $specModel = Kwf_Model_Abstract::getInstance('Specialities');
        $specSelect = $specModel->select()->whereEquals('Hidden', '0');
        
        $tab->fields->add(new Kwf_Form_Field_Select('specId', trlKwf('Speciality')))
        ->setValues($specModel)
        ->setSelect($specSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $subSpecModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $subSpecSelect = $subSpecModel->select()->whereEquals('name', 'Должности');
        
        $tab->fields->add(new Kwf_Form_Field_Select('positionId', trlKwf('Spec Position')))
        ->setValues($subSpecModel)
        ->setSelect($subSpecSelect)
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_Checkbox('isLeader', trlKwf('Leader')));
        
        $tab->fields->add(new Kwf_Form_Field_TextField('failsDocNumber', trlKwf('Fails Doc Number')))
        ->setWidth(400);
        
        $specDocModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $specDocSelect = $specDocModel->select()->whereEquals('name', 'Свидетельства специалиста');
        
        $tab->fields->add(new Kwf_Form_Field_Select('specTypeId', trlKwf('SpecType')))
        ->setValues($specDocModel)
        ->setSelect($specDocSelect)
        ->setWidth(400);
        
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Onboard positions'));
        
        $positionsModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $positionsSelect = $positionsModel->select()->whereEquals('name', 'Позиции на борту')->order('value');
        
        $multifields = new Kwf_Form_Field_MultiFields('EmployeeFlightRoles');
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Kwf_Form_Field_Select('groupId', trlKwf('Position')))
        ->setValues($positionsModel)
        ->setSelect($positionsSelect)
        ->setAllowBlank(false);
        $tab->fields->add($multifields);

    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->visible = 1;
    }
}

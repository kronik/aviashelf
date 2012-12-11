<?php
class CompanyController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Companies';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_paging = 0;

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('Name', trlKwf('Title')))
        ->setWidth(300)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('NameEn', trlKwf('English name')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('FullName', trlKwf('Full name')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('INN', trlKwf('INN')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('Address', trlKwf('Address')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('Phone', trlKwf('Phone')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('Fax', trlKwf('Fax')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('EMail', trlKwf('Email')))
        ->setWidth(300);
        
        $model = Kwf_Model_Abstract::getInstance('Countries');
        $select = $model->select()->whereEquals('Hidden', '0');
        
        $this->_form->add(new Kwf_Form_Field_Select('CountryId', trlKwf('Country')))
        ->setValues($model)
        ->setSelect($select)
        ->setWidth(300);
    }
}

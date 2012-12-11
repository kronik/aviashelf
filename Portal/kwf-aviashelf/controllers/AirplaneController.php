<?php
class AirplaneController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Airplanes';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_paging = 0;

    protected function _initFields()
    {
        $typeModel = Kwf_Model_Abstract::getInstance('WsTypes');
        $typeSelect = $typeModel->select()->whereEquals('Hidden', '0');
        
        $this->_form->add(new Kwf_Form_Field_Select('twsId', trlKwf('WsType')))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(600)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('State', trlKwf('Char Title')))
        ->setWidth(600)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('Number', trlKwf('Number Title')))
        ->setWidth(600)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('NBort', trlKwf('Bort Title')))
        ->setWidth(600);
        $this->_form->add(new Kwf_Form_Field_TextField('Mass', trlKwf('Weight')))
        ->setWidth(600)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('Center', trlKwf('Center Point')))
        ->setWidth(600)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('LotsNumber', trlKwf('Seats Number')))
        ->setWidth(600)
        ->setAllowBlank(false);
        
        $countryModel = Kwf_Model_Abstract::getInstance('Countries');
        $countrySelect = $countryModel->select()->whereEquals('Hidden', '0');
        
        $this->_form->add(new Kwf_Form_Field_Select('CountryId', trlKwf('Country')))
        ->setValues($countryModel)
        ->setSelect($countrySelect)
        ->setWidth(600)
        ->setAllowBlank(false);
        
        $ownerModel = Kwf_Model_Abstract::getInstance('Companies');
        $ownerSelect = $ownerModel->select()->whereEquals('Hidden', '0');
        
        $this->_form->add(new Kwf_Form_Field_Select('OwnerId', trlKwf('Owner')))
        ->setValues($ownerModel)
        ->setSelect($ownerSelect)
        ->setWidth(600)
        ->setAllowBlank(false);
        
        $linkModel = Kwf_Model_Abstract::getInstance('LinkData');
        $linkSelect = $linkModel->select()->whereEquals('name', 'Подразделения');
        
        $this->_form->add(new Kwf_Form_Field_Select('LinkId', trlKwf('Subcompany')))
        ->setValues($linkModel)
        ->setSelect($linkSelect)
        ->setWidth(600);
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {        
        $row->NBort = $this->_getParam('State').'-'.$this->_getParam('Number');
    }
}

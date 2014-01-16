<?php
class MyflightController extends FlightController
{
    protected $_permissions = array('xls');
    protected $_buttons = array ('xls');
    
    protected function _initFields()
    {
        $tabs = $this->_form->add(new Kwf_Form_Container_Tabs());
        $tabs->setBorder(true);
        $tabs->setActiveTab(0);
        
        // **** General Info
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('General Info'));
        
        $tab->fields->add(new Kwf_Form_Field_ShowField('number', 'Номер'))
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('requestNumber', trlKwf('Task number')))
        ->setWidth(400);
        
        $companyModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $companySelect = $companyModel->select()->whereEquals('name', 'Компании для ПЗ')->order('name');
        
        $tab->fields->add(new Kwf_Form_Field_Select('subCompanyId', trlKwf('Customer')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('flightStartTime', trlKwf('Start Time')))->setIncrement(5);
        
        $airplanesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $airplanesSelect = $airplanesModel->select();
        
        $tab->fields->add(new Kwf_Form_Field_Select('planeId', trlKwf('Airplane')))
        ->setValues($airplanesModel)
        ->setSelect($airplanesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $groupModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $groupSelect = $groupModel->select()->whereEquals('name', 'Тип экипажа');
        
        $tab->fields->add(new Kwf_Form_Field_Select('groupId', trlKwf('Group type')))
        ->setValues($groupModel)
        ->setSelect($groupSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TextArea('comments', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_Checkbox('status', trlKwf('Done')));
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row) {
        throw new Kwf_Exception_Client('ПЗ закрыто для изменений.');   
    }
}

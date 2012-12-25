<?php
class FlightController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Flights';

    protected function _initFields()
    {
        $tabs = $this->_form->add(new Kwf_Form_Container_Tabs());
        $tabs->setActiveTab(0);

        // **** General Info
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Flight'));
        $tab->setLabelAlign('top');
        
        $tab->fields->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
            ->setAllowBlank(false)
            ->setWidth(400);
        
        $linkModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $linkSelect = $linkModel->select()->whereEquals('name', 'Подразделения');
        
        $tab->fields->add(new Kwf_Form_Field_Select('subCompanyId', trlKwf('Subcompany')))
        ->setValues($linkModel)
        ->setSelect($linkSelect)
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_DateField('flightStartDate', trlKwf('Start Date')));
        $tab->fields->add(new Kwf_Form_Field_TimeField('flightStartTime', trlKwf('Start Time')))->setIncrement(10);

        $tab->fields->add(new Kwf_Form_Field_DateField('flightEndDate', trlKwf('End Date')));
        $tab->fields->add(new Kwf_Form_Field_TimeField('flightTime', trlKwf('Flight Time')))->setIncrement(10);
        $tab->fields->add(new Kwf_Form_Field_TimeField('flightWorkTime', trlKwf('Flight work time')))->setIncrement(10);

        $tab->fields->add(new Kwf_Form_Field_NumberField('flightCount', trlKwf('Flight count')))
        ->setWidth(400);

        $airplanesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $airplanesSelect = $airplanesModel->select()->whereEquals('Hidden', '0');
        
        $tab->fields->add(new Kwf_Form_Field_Select('planeId', trlKwf('Airplane')))
        ->setValues($airplanesModel)
        ->setSelect($airplanesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $objModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $objSelect = $objModel->select()->whereEquals('name', 'Цели');
        
        $tab->fields->add(new Kwf_Form_Field_Select('objectiveId', trlKwf('Objective')))
        ->setValues($objModel)
        ->setSelect($objSelect)
        ->setWidth(400);
        
        $routeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $routeSelect = $routeModel->select()->whereEquals('name', 'Маршруты');
        
        $tab->fields->add(new Kwf_Form_Field_Select('routeId', trlKwf('Route')))
        ->setValues($routeModel)
        ->setSelect($routeSelect)
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_TextArea('results', trlKwf('Flight Result Comment')))
        ->setHeight(100)
        ->setWidth(400);
        
        $tab->fields->add(new Kwf_Form_Field_Checkbox('status', trlKwf('Done')));
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Airplanes');
        
        $s = $m1->select()->whereEquals('id', $row->subCompanyId);
        $prow = $m1->getRow($s);
        $row->subCompanyName = $prow->value;
        
        $s = $m1->select()->whereEquals('id', $row->objectiveId);
        $prow = $m1->getRow($s);
        $row->objectiveName = $prow->value;
        
        $s = $m1->select()->whereEquals('id', $row->routeId);
        $prow = $m1->getRow($s);
        $row->routeName = $prow->value;
        
        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->NBort;
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Airplanes');
        
        $s = $m1->select()->whereEquals('id', $row->subCompanyId);
        $prow = $m1->getRow($s);
        $row->subCompanyName = $prow->value;
        
        $s = $m1->select()->whereEquals('id', $row->objectiveId);
        $prow = $m1->getRow($s);
        $row->objectiveName = $prow->value;
        
        $s = $m1->select()->whereEquals('id', $row->routeId);
        $prow = $m1->getRow($s);
        $row->routeName = $prow->value;
        
        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->NBort;
    }
}

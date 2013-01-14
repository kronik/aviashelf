<?php
class FlightController extends Kwf_Controller_Action_Auto_Form
{
    #protected $_buttons = array('save');
    protected $_permissions = array('save', 'add', 'xls');
    protected $_modelName = 'Flights';
    #protected $title = trlKwf('Flight');
    protected $_buttons = array ('xls');

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
            ->setAllowBlank(false)
            ->setWidth(400);
        
        $linkModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $linkSelect = $linkModel->select()->whereEquals('name', 'Подразделения');
        
        $this->_form->add(new Kwf_Form_Field_Select('subCompanyId', trlKwf('Subcompany')))
        ->setValues($linkModel)
        ->setSelect($linkSelect)
        ->setWidth(400);
        
        #$this->_form->add(new Kwf_Form_Field_DateField('flightStartDate', trlKwf('Start Date')));
        $this->_form->add(new Kwf_Form_Field_TimeField('flightStartTime', trlKwf('Start Time')))->setIncrement(5);

        $airplanesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $airplanesSelect = $airplanesModel->select()->whereEquals('Hidden', '0');
        
        $this->_form->add(new Kwf_Form_Field_Select('planeId', trlKwf('Airplane')))
        ->setValues($airplanesModel)
        ->setSelect($airplanesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $objModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $objSelect = $objModel->select()->whereEquals('name', 'Цели');
        
        $this->_form->add(new Kwf_Form_Field_Select('objectiveId', trlKwf('Objective')))
        ->setValues($objModel)
        ->setSelect($objSelect)
        ->setWidth(400);
        
        $routeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $routeSelect = $routeModel->select()->whereEquals('name', 'Маршруты');
        
        $this->_form->add(new Kwf_Form_Field_Select('routeId', trlKwf('Route')))
        ->setValues($routeModel)
        ->setSelect($routeSelect)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comments', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
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
        $row->planId = $this->_getParam('planId');
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
        
        $flightPlansModel = Kwf_Model_Abstract::getInstance('Flightplans');
        $flightPlansSelect = $flightPlansModel->select()->whereEquals('id', $this->_getParam('planId'));
        $prow = $flightPlansModel->getRow($flightPlansSelect);
        
        $row->flightStartDate = $prow->planDate;
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $xls->getProperties()->setCreator("Vivid Planet Software GmbH");
        $xls->getProperties()->setLastModifiedBy("Vivid Planet Software GmbH");
        $xls->getProperties()->setTitle("KWF Excel Export");
        $xls->getProperties()->setSubject("KWF Excel Export");
        $xls->getProperties()->setDescription("KWF Excel Export");
        $xls->getProperties()->setKeywords("KWF Excel Export");
        $xls->getProperties()->setCategory("KWF Excel Export");
    }
}

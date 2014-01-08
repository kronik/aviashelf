<?php

require_once 'FormEx.php';

class SimpleflightController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_permissions = array('xls');
    protected $_modelName = 'Flights';
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
        ->setReadOnly(true)
        ->setWidth(400);

        $tab->fields->add(new Kwf_Form_Field_TextField('requestNumber', trlKwf('Task number')))
        ->setReadOnly(true)
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
        
//        $objModel = Kwf_Model_Abstract::getInstance('Linkdata');
//        $objSelect = $objModel->select()->whereEquals('name', 'Цели');
//        
//        $tab->fields->add(new Kwf_Form_Field_Select('objectiveId', trlKwf('Objective')))
//        ->setValues($objModel)
//        ->setSelect($objSelect)
//        ->setWidth(400);

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

        $tab = $tabs->add();
        $tab->setTitle('Цели');
        
        $objectivesModel = Kwf_Model_Abstract::getInstance('Objectives');
        $objectivesSelect = $objectivesModel->select()->whereEquals('name', 'Цель полета')->order('value');
        
        $multifields = new Kwf_Form_Field_MultiFields('FlightObjectives');
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Kwf_Form_Field_Select('objectiveId', 'Цель'))
        ->setValues($objectivesModel)
        ->setSelect($objectivesSelect)
        ->setAllowBlank(false);
        $tab->fields->add($multifields);

        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Landpoints'));
        
        $landpointsModel = Kwf_Model_Abstract::getInstance('Landpoints');
        $landpointsSelect = $landpointsModel->select()->order('listPosition');
        
        $multifields = new Kwf_Form_Field_MultiFields('FlightLandpoints');
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Kwf_Form_Field_Select('landpointId', trlKwf('Destination')))
        ->setValues($landpointsModel)
        ->setSelect($landpointsSelect)
        ->setAllowBlank(false);
        $tab->fields->add($multifields);
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();
        
        if ($row->isPrinted == false) {
            $row->isPrinted = true;
            
            $db = Zend_Registry::get('db');
            
            $stmt = $db->query("CALL getNextId(0, @nextId)", array(25));
            
            while ($stmt->nextRowset()) { };
            
            $stmt = $db->query("SELECT @nextId");
            $rows = $stmt->fetchAll();
            $row->number = $rows[0]["@nextId"];
            
            if (strlen($row->number) == 1)
            {
                $row->number = '000' . $row->number;
            }
            else if (strlen($row->number) == 2)
            {
                $row->number = '00' . $row->number;
            }
            else if (strlen($row->number) == 3)
            {
                $row->number = '0' . $row->number;
            }
        }
        
        $row->save();
                
        $this->_progressBar = new Zend_ProgressBar(new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                                                   0, 100);

        $reporter = new Reporter ();
        
        $xls = PHPExcel_IOFactory::load("./templates/flight_task_template.xls");
        
        $xls->setActiveSheetIndex(0);
        $firstSheet = $xls->getActiveSheet();

        $reporter->exportFlightTaskToXls($xls, $firstSheet, $row, $this->_progressBar);
        
        $this->_progressBar->finish();
        
        return $xls;
    }
}

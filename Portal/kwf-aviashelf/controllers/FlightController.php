<?php

require_once 'FormEx.php';

//class FlightController extends Kwf_Controller_Action_Auto_Form
class FlightController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_permissions = array('save', 'add', 'xls');
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
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $companyModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $companySelect = $companyModel->select()->whereEquals('id', $row->subCompanyId);
        
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Airplanes');
        
        $prow = $companyModel->getRow($companySelect);
        $row->subCompanyName = $prow->value;
        
//        $s = $m1->select()->whereEquals('id', $row->objectiveId);
//        $prow = $m1->getRow($s);
//        $row->objectiveName = $prow->value;
        
        $row->routeId = 0;
        $row->routeName = '';
        
        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->NBort;
        $row->planId = $this->_getParam('planId');
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    protected function insertNewRow($positionId, $positionName, $mainCrew)
    {
        $row = $this->_form->getRow();

        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        
        $newRow = $flightGroupsModel->createRow();
        
        $newRow->positionId = $positionId;
        $newRow->positionName = $positionName;
        $newRow->employeeId = 0;
        $newRow->employeeName = '';
        $newRow->flightId = $row->id;
        $newRow->mainCrew = $mainCrew;
        
        $newRow->save();
    }
    
    protected function createNewTask()
    {
        // TODO: Assign new task for every member in crew
    }
    
    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $flightLandpointsModel = Kwf_Model_Abstract::getInstance('FlightLandpoints');
        $flightLandpointsSelect = $flightLandpointsModel->select()->whereEquals('flightId', $row->id)->order('pos');
        $flightLandpoints = $flightLandpointsModel->getRows($flightLandpointsSelect);
        
        $landpointsModel = Kwf_Model_Abstract::getInstance('Landpoints');

        $row->routeName = '';

        foreach ($flightLandpoints as $flightLandpoint) {
            $landpointsSelect = $landpointsModel->select()->whereEquals('id', $flightLandpoint->landpointId);
            $landpoint = $landpointsModel->getRow($landpointsSelect);
            
            $row->routeName = $row->routeName . $landpoint->name . '. ';
        }
                
        if (strlen($row->routeName) < 2)
        {
            $row->routeName = 'Обеспечение ПСО/АСР';
        }
        
        $flightObjectiveSelect = new Kwf_Model_Select();
        $flightObjectiveSelect->whereEquals('flightId', $row->id)->order('pos');

        $objectivesModel = Kwf_Model_Abstract::getInstance('Objectives');
        $objectivesSelect = $objectivesModel->select()->where(new Kwf_Model_Select_Expr_Child_Contains('FlightObjectives', $flightObjectiveSelect));
        
        $objectives = $objectivesModel->getRows($objectivesSelect);
        $row->objectiveName = '';
        
        foreach ($objectives as $objective)
        {
            $row->objectiveName = $row->objectiveName . $objective->value . '. ';
        }
        
        $row->save();
    }
    
    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $row = $this->_form->getRow();

        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Позиции на борту' AND value = 'КВС'"));
        $kwsRow = $typeModel->getRow($typeSelect);

        $this->insertNewRow($kwsRow->id, $kwsRow->value, TRUE);

        $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Позиции на борту' AND value = 'Второй пилот'"));
        $secondRow = $typeModel->getRow($typeSelect);
        
        $this->insertNewRow($secondRow->id, $secondRow->value, TRUE);
        
        $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Позиции на борту' AND value = 'Бортмеханик'"));
        $techRow = $typeModel->getRow($typeSelect);
        
        $this->insertNewRow($techRow->id, $techRow->value, TRUE);
        
        $groupModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $groupSelect = $groupModel->select()->whereEquals('id', $row->groupId);
        $groupRow = $groupModel->getRow($groupSelect);

        if ($this->isContain('спасатель', $groupRow->value))
        {
            $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Дополнительные позиции' AND value = 'Спасатель'"));
            $posRow = $typeModel->getRow($typeSelect);
            
            $this->insertNewRow($posRow->id, $posRow->value, FALSE);
        }
        
        if ($this->isContain('проверяющий', $groupRow->value))
        {
            $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Позиции на борту' AND value = 'Проверяющий'"));
            $posRow = $typeModel->getRow($typeSelect);
            
            $this->insertNewRow($posRow->id, $posRow->value, TRUE);
        }
        
        if ($this->isContain('авиатехник', $groupRow->value))
        {
            $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Дополнительные позиции' AND value = 'Авиатехник'"));
            $posRow = $typeModel->getRow($typeSelect);
            
            $this->insertNewRow($posRow->id, $posRow->value, FALSE);
            
            $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Дополнительные позиции' AND value = 'Техник АиРЭО'"));
            $posRow = $typeModel->getRow($typeSelect);
            
            $this->insertNewRow($posRow->id, $posRow->value, FALSE);
        }
        
        if ($this->isContain('тренируемы', $groupRow->value))
        {
            $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Позиции на борту' AND value like 'Тренируемы%'"));
            $posRow = $typeModel->getRow($typeSelect);
            
            $this->insertNewRow($posRow->id, $posRow->value, TRUE);
        }
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
        
        $flightPlansModel = Kwf_Model_Abstract::getInstance('Flightplans');
        $flightPlansSelect = $flightPlansModel->select()->whereEquals('id', $this->_getParam('planId'));
        $prow = $flightPlansModel->getRow($flightPlansSelect);
        
        $row->flightStartDate = $prow->planDate;
        $row->status = 0;
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
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
        
        #$xls->setActiveSheetIndex();
        
        $xls->setActiveSheetIndex(0);
        $firstSheet = $xls->getActiveSheet();
        
//        $sheetId = 0;
//        $firstSheet = $xls->createSheet($sheetId);

        $reporter->exportFlightTaskToXls($xls, $firstSheet, $row, $this->_progressBar);
        
        $this->_progressBar->finish();
        
        return $xls;
    }
}

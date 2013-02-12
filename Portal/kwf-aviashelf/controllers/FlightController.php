<?php
class FlightController extends Kwf_Controller_Action_Auto_Form
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

        $tab->fields->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
            ->setAllowBlank(false)
            ->setWidth(400);
        
        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->whereEquals('Hidden', '0')->order('Name');
        
        $tab->fields->add(new Kwf_Form_Field_Select('subCompanyId', trlKwf('Customer')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('flightStartTime', trlKwf('Start Time')))->setIncrement(5);

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
        
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('Landpoints'));
        
        $landpointsModel = Kwf_Model_Abstract::getInstance('Landpoints');
        $landpointsSelect = $landpointsModel->select()->order('description');
        
        $multifields = new Kwf_Form_Field_MultiFields('FlightLandpoints');
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Kwf_Form_Field_Select('landpointId', trlKwf('Airport')))
        ->setValues($landpointsModel)
        ->setSelect($landpointsSelect)
        ->setAllowBlank(false);
        $tab->fields->add($multifields);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->whereEquals('id', $row->subCompanyId);
        
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Airplanes');
        
        $prow = $companyModel->getRow($companySelect);
        $row->subCompanyName = $prow->Name;
        
        $s = $m1->select()->whereEquals('id', $row->objectiveId);
        $prow = $m1->getRow($s);
        $row->objectiveName = $prow->value;
        
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
    
    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $flightLandpointSelect = new Kwf_Model_Select();
        $flightLandpointSelect->whereEquals('flightId', $row->id)->order('pos');

        $landpointsModel = Kwf_Model_Abstract::getInstance('Landpoints');
        $landpointsSelect = $landpointsModel->select()->where(new Kwf_Model_Select_Expr_Child_Contains('FlightLandpoints', $flightLandpointSelect));
        
        $landpoints = $landpointsModel->getRows($landpointsSelect);
        $row->routeName = '';
        
        foreach ($landpoints as $landpoint)
        {
            $row->routeName = $row->routeName . $landpoint->name . '. ';
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
            $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Позиции на борту' AND value = 'Спасатель'"));
            $posRow = $typeModel->getRow($typeSelect);
            
            $this->insertNewRow($posRow->id, $posRow->value, FALSE);
        }
        
        if ($this->isContain('проверяющий', $groupRow->value))
        {
            $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Позиции на борту' AND value = 'Проверяющий'"));
            $posRow = $typeModel->getRow($typeSelect);
            
            $this->insertNewRow($posRow->id, $posRow->value, FALSE);
        }
        
        if ($this->isContain('тренируемы', $groupRow->value))
        {
            $typeSelect = $typeModel->select()->where(new Kwf_Model_Select_Expr_Sql("name = 'Позиции на борту' AND value like 'Тренируемы%'"));
            $posRow = $typeModel->getRow($typeSelect);
            
            $this->insertNewRow($posRow->id, $posRow->value, FALSE);
        }
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
    
    protected function russianDate($rawDate)
    {
        $date = explode("-", $rawDate);
        
        switch ($date[1])
        {
            case 1: $m='января'; break;
            case 2: $m='февраля'; break;
            case 3: $m='марта'; break;
            case 4: $m='апреля'; break;
            case 5: $m='мая'; break;
            case 6: $m='июня'; break;
            case 7: $m='июля'; break;
            case 8: $m='августа'; break;
            case 9: $m='сентября'; break;
            case 10: $m='октября'; break;
            case 11: $m='ноября'; break;
            case 12: $m='декабря'; break;
        }
        return $date[0] . ' ' . $m . ' ' . $date[2];
    }
    
    protected function extractLandPoints($rawRoute)
    {
        $route = explode(". ", $rawRoute);
        $points = array();
        $keys = array();

        $landPoint = '';
        
        foreach ($route as $point)
        {
            $point = trim($point, " ");
            $key = strtoupper($point);
            
            if (in_array($key, $keys) == false)
            {
                array_push($points, $point);
                array_push($keys, $key);
            }
        }
        
        foreach ($points as $point)
        {
            $landPoint = $landPoint . '. ' . $point;
        }
        
        return $landPoint;
    }
    
    protected function _getColumnLetterByIndex($idx)
    {
        $letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
                         'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $maxLetterIndex = count($letters) - 1;
        if ($idx > $maxLetterIndex) {
            return $letters[floor(($idx) / count($letters))-1].$letters[($idx) % count($letters)];
        } else {
            return $letters[$idx];
        }
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();
        
        foreach($firstSheet->getRowDimensions() as $rd)
        {
            $rd->setRowHeight(-1);
        }
        
        $xls->getProperties()->setCreator(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setLastModifiedBy(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setTitle("План полетов");
        $xls->getProperties()->setSubject("План полетов");
        $xls->getProperties()->setDescription("План полетов на сегодня");
        $xls->getProperties()->setKeywords("");
        $xls->getProperties()->setCategory("");
        
        $firstSheet->getPageSetup()->setFitToPage(true);
        $firstSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $firstSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        
        $pageMargins = $firstSheet->getPageMargins();
        
        $margin = 0.42;
        
        $pageMargins->setTop($margin);
        $pageMargins->setBottom($margin);
        $pageMargins->setLeft($margin);
        $pageMargins->setRight($margin);
        
        $styleThinBlackBorderOutline = array(
                                             'borders' => array(
                                                                'outline' => array(
                                                                                   'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                                                   'color' => array('argb' => 'FF000000'),
                                                                                   ),
                                                                ),
                                             );
        $totalLeftColumns = 28;
        
        for ($i = 0; $i <= $totalLeftColumns-1; $i++)
        {
            $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($i))->setWidth('1.5pt');
        }
        
        $tableColumnt = $this->_getColumnLetterByIndex($totalLeftColumns - 1);
        $tableHeaderColumnt = $this->_getColumnLetterByIndex($totalLeftColumns);
        
        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($totalLeftColumns + 1))->setWidth('1pt');
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($totalLeftColumns + 1) . '1:' . $this->_getColumnLetterByIndex($totalLeftColumns + 1) . '39');

        $firstSheet->getStyle('A1:' . $tableColumnt . '13')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells('A1:' . $tableColumnt . '13');
        
        $firstSheet->getStyle($tableHeaderColumnt . '1:' . $tableHeaderColumnt . '13')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells($tableHeaderColumnt . '1:' . $tableHeaderColumnt . '13');
        $firstSheet->setCellValue($tableHeaderColumnt . '1', 'Предполётный медосмотр');
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getFont()->setBold(true);
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setTextRotation(-90);

        $firstSheet->getStyle('A14:' . $tableColumnt . '26')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells('A14:' . $tableColumnt . '26');
        
        $firstSheet->getStyle($tableHeaderColumnt . '14:' . $tableHeaderColumnt . '26')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells($tableHeaderColumnt . '14:' . $tableHeaderColumnt . '26');
        
        $firstSheet->setCellValue($tableHeaderColumnt . '14', 'Спецконроль в аэропортах');
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getFont()->setBold(true);
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setTextRotation(-90);

        $firstSheet->getStyle('A27:' . $tableColumnt . '39')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells('A27:B39');
        
        $firstSheet->getStyle($tableHeaderColumnt . '27:' . $tableHeaderColumnt . '39')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells($tableHeaderColumnt . '27:' . $tableHeaderColumnt . '39');

        $firstSheet->setCellValue($tableHeaderColumnt . '27', 'Результаты послеполётного разбора');
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getFont()->setBold(true);
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setTextRotation(-90);

        
        for ($i = 2; $i <= $totalLeftColumns-1; $i++)
        {
            $col = $this->_getColumnLetterByIndex($i);
            $firstSheet->mergeCells($col . '27:' . $col . '39');
        }
        
        $rightColumn = $totalLeftColumns + 2;
        $rowNumber = 1;

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setSize(10);
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, trlKwf('Дальневосточное межрегиональное территориальное управление'));
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $rowNumber += 1;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setSize(10);
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, trlKwf('воздушного транспорта ФАВТ'));
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $rowNumber += 1;

        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn))->setWidth('20pt');

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . ($rowNumber + 3));
        
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath('./images/doc_logo.png');
        $objDrawing->setCoordinates($this->_getColumnLetterByIndex($rightColumn) . $rowNumber);
        $objDrawing->setWidth('380px');
        $objDrawing->setWorksheet($firstSheet);
        
        $rowNumber += 4;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber);

        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, trlKwf('ЗАДАНИЕ НА ПОЛЁТ #'));
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);

        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('planId', $row->planId)->order(array('subCompanyId', 'flightStartTime'));
        
        $flights = $flightsModel->getRows($flightsSelect);
        $flightSequenceNumber = 1;
        $lastSubcompanyId = 0;
        
        foreach ($flights as $flight)
        {
            if ($lastSubcompanyId != $flight->subCompanyId)
            {
                $flightSequenceNumber = 0;
            }
            
            $lastSubcompanyId = $flight->subCompanyId;
            $flightSequenceNumber += 1;
            
            if ($flight->id == $row->id)
            {
                break;
            }
        }
              
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 2) . $rowNumber, $flightSequenceNumber . ' / ' . $row->flightStartDate);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 2) . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber, 'ЮШ ' . $row->number);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber)->getFont()->setBold(true);

        $rowNumber += 1;

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;

        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $row->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $typeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $typeSelect = $typeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $typeModel->getRow($typeSelect);

        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'Командиру вертолёта:');
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 2) . $rowNumber, $planeType->Name);
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber, $row->planeName);
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 2) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 3) . $rowNumber);

        $rowNumber += 1;

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'СОСТАВ ЭКИПАЖА');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $rowNumber += 1;

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'Должность');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, 'ФИО');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $row->id)->whereEquals('mainCrew', TRUE)->order('id');
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
        
        $rowNumber += 1;

        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $subSpecModel = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $kwsId = 0;

        foreach ($flightMembers as $flightMember)
        {
            $employeesSelect = $employeesModel->select()->whereEquals('id', $flightMember->employeeId);
            $employeeRow = $employeesModel->getRow($employeesSelect);
            
            $subSpecSelect = $subSpecModel->select()->whereEquals('id', $employeeRow->positionId);
            $subSpecRow = $subSpecModel->getRow($subSpecSelect);
  
            $position = $flightMember->positionName;
            
            if ($position == 'КВС')
            {
                $kwsId = $flightMember->employeeId;
            }

            $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, $position);
            $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, (string)$employeeRow);
            $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
            
            $rowNumber += 1;
        }
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;

        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'Дата вылета: ');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 2) . $rowNumber);
        
        $flightDate = new DateTime ($row->flightStartDate);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, $this->russianDate($flightDate->format('d-m-Y')));
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber)->getFont()->setBold(true);
                
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 3) . $rowNumber, 'Время: ');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 3) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $flightStartTime = new DateTime($row->flightStartTime);
        $flightStartTime = $flightStartTime->format("H:i");
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber, $flightStartTime);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber)->getFont()->setBold(true);
        
        $rowNumber += 1;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);

//        $rowNumber += 1;
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn) . ($rowNumber + 1));
//        
//        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'Маршрут полёта');
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//
//        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, $row->routeName);
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . ($rowNumber + 1) . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . ($rowNumber + 1));

        $rowNumber += 2;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn) . ($rowNumber + 1));

        $objectiveModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $objectiveSelect = $objectiveModel->select()->whereEquals('id', $row->objectiveId);
        $objective = $objectiveModel->getRow($objectiveSelect);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'Цель полёта');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, $objective->desc);
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . ($rowNumber + 1) . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . ($rowNumber + 1));

        $rowNumber += 2;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn) . ($rowNumber + 1));

        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'Пункты посадки');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, $this->extractLandPoints($row->routeName));
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . ($rowNumber + 1) . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . ($rowNumber + 1));

        $rowNumber += 2;

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;
        
        
        
        
        
        
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'В ПОЛЕТНОЕ ЗАДАНИЕ ВКЛЮЧИТЬ');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $rowNumber += 1;
        
        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $row->id)->whereEquals('mainCrew', FALSE)->order('id');
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
                
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $subSpecModel = Kwf_Model_Abstract::getInstance('Linkdata');
        
        foreach ($flightMembers as $flightMember)
        {
            $employeesSelect = $employeesModel->select()->whereEquals('id', $flightMember->employeeId);
            $employeeRow = $employeesModel->getRow($employeesSelect);
            
            $subSpecSelect = $subSpecModel->select()->whereEquals('id', $employeeRow->positionId);
            $subSpecRow = $subSpecModel->getRow($subSpecSelect);
            
            $position = $flightMember->positionName;
            
            if ($position == 'По специальности')
            {
                $position = $subSpecRow->value;
            }
                        
            $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, $position);
            $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, (string)$employeeRow);
            $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
            
            $rowNumber += 1;
        }
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;

        
        
        
        
        
        
        
        
        
        
        
        
        
        

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'ЭКИПАЖ ДОПУЩЕН ПО МЕТЕОМИНИМУМУ');
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $rowNumber += 1;

        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;

        $accessesModel = Kwf_Model_Abstract::getInstance('Flightaccesses');        
        $accessesSelect = $accessesModel->select()->where(new Kwf_Model_Select_Expr_Sql("`employeeId` = " . $kwsId . " AND `wsTypeId` = " . $planeType->id));
        $accesses = $accessesModel->getRows($accessesSelect);
                
        foreach ($accesses as $access)
        {
            $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
            $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, $access->accessName);
            $rowNumber += 1;
        }
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;

        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . 1 . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . 39)->applyFromArray($styleThinBlackBorderOutline);

    }
}

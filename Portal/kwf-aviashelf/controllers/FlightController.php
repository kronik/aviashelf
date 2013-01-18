<?php
class FlightController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add', 'xls');
    protected $_modelName = 'Flights';
    protected $_buttons = array ('xls');

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('number', trlKwf('Number')))
            ->setAllowBlank(false)
            ->setWidth(400);
        
        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->whereEquals('Hidden', '0')->order('Name');
        
        $this->_form->add(new Kwf_Form_Field_Select('subCompanyId', trlKwf('Customer')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400);
        
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
        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->whereEquals('id', $row->subCompanyId);
        
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Airplanes');
        
        $prow = $companyModel->getRow($companySelect);
        $row->subCompanyName = $prow->Name;
        
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
        $route = explode("-", $rawRoute);
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
            $landPoint = $landPoint . ' ' . $point;
        }
        
        return $landPoint;
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
        
        $firstSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $firstSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        
        $firstSheet->mergeCells('A1:F1');
        $firstSheet->mergeCells('A2:F2');
        $firstSheet->mergeCells('A3:F3');

        $firstSheet->getColumnDimension('A')->setWidth('20pt');

        $firstSheet->setCellValue('A2', trlKwf('Дальневосточное межрегиональное территориальное управление'));
        $firstSheet->getStyle('A2')->getFont()->setBold(true);
        $firstSheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $firstSheet->setCellValue('A3', trlKwf('воздушного транспорта ФАВТ'));
        $firstSheet->getStyle('A3')->getFont()->setBold(true);
        $firstSheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $firstSheet->mergeCells('A4:F4');
        $firstSheet->mergeCells('A5:F8');
        
        $firstSheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath('./images/doc_logo.png');
        $objDrawing->setCoordinates('A5');
        $objDrawing->setWidth('450px');
        $objDrawing->setWorksheet($firstSheet);
        
        $firstSheet->mergeCells('A9:F9');

        $firstSheet->mergeCells('A10:B10');

        $firstSheet->setCellValue('A10', trlKwf('ЗАДАНИЕ НА ПОЛЁТ #'));
        $firstSheet->getStyle('A10')->getFont()->setBold(true);

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
      
        $firstSheet->mergeCells('E10:F10');
        
        $firstSheet->setCellValue('C10', $flightSequenceNumber . ' / ' . $row->flightStartDate);
        $firstSheet->getStyle('C10')->getFont()->setBold(true);
        
        $firstSheet->setCellValue('E10', $row->number);
        $firstSheet->getStyle('E10')->getFont()->setBold(true);

        $firstSheet->mergeCells('A11:F11');
        
        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $row->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $typeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $typeSelect = $typeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $typeModel->getRow($typeSelect);

        $firstSheet->setCellValue('A12', 'Командиру вертолёта:');
        $firstSheet->setCellValue('C12', $planeType->Name);
        $firstSheet->setCellValue('E12', $row->planeName);
        
        $firstSheet->mergeCells('E12:F12');
        $firstSheet->mergeCells('C12:D12');
        
        $firstSheet->mergeCells('A13:F13');
        $firstSheet->mergeCells('A14:F14');
        
        $firstSheet->setCellValue('A14', 'СОСТАВ ЭКИПАЖА');
        $firstSheet->getStyle('A14')->getFont()->setBold(true);
        $firstSheet->getStyle('A14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $firstSheet->mergeCells('A15:B15');
        $firstSheet->mergeCells('C15:F15');
        
        $firstSheet->setCellValue('A15', 'Должность');
        $firstSheet->getStyle('A15')->getFont()->setBold(true);
        $firstSheet->getStyle('A15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $firstSheet->setCellValue('C15', 'ФИО');
        $firstSheet->getStyle('C15')->getFont()->setBold(true);
        $firstSheet->getStyle('C15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $row->id)->order('id');
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
        
        $rowNumber = 16;
        
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

            $firstSheet->setCellValue('A' . $rowNumber, $position);
            $firstSheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);

            $firstSheet->setCellValue('C' . $rowNumber, (string)$employeeRow);
            $firstSheet->mergeCells('C' . $rowNumber . ':F' . $rowNumber);
            
            $rowNumber += 1;
        }
        
        $firstSheet->mergeCells('A' . $rowNumber . ':F' . $rowNumber);
        $rowNumber += 1;

        $firstSheet->setCellValue('A' . $rowNumber, 'Дата вылета: ');
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $firstSheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);        
        $firstSheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber);
        
        $flightDate = new DateTime ($row->flightStartDate);
        
        $firstSheet->setCellValue('C' . $rowNumber, $this->russianDate($flightDate->format('d-m-Y')));
        $firstSheet->getStyle('C' . $rowNumber)->getFont()->setBold(true);
                
        $firstSheet->setCellValue('E' . $rowNumber, 'Время: ');
        $firstSheet->getStyle('E' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $flightStartTime = new DateTime($row->flightStartTime);
        $flightStartTime = $flightStartTime->format("H:i");
        
        $firstSheet->setCellValue('F' . $rowNumber, $flightStartTime);
        $firstSheet->getStyle('F' . $rowNumber)->getFont()->setBold(true);
        
        $rowNumber += 1;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':F' . $rowNumber);

        $rowNumber += 1;
        $firstSheet->mergeCells('A' . $rowNumber . ':B' . ($rowNumber + 1));
        
        $firstSheet->setCellValue('A' . $rowNumber, 'Маршрут полёта');
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $firstSheet->setCellValue('C' . $rowNumber, $row->routeName);
        $firstSheet->mergeCells('C' . $rowNumber . ':F' . $rowNumber);
        $firstSheet->mergeCells('C' . ($rowNumber + 1) . ':F' . ($rowNumber + 1));

        $rowNumber += 2;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':B' . ($rowNumber + 1));

        $objectiveModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $objectiveSelect = $objectiveModel->select()->whereEquals('id', $row->objectiveId);
        $objective = $objectiveModel->getRow($objectiveSelect);
        
        $firstSheet->setCellValue('A' . $rowNumber, 'Цель полёта');
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $firstSheet->setCellValue('C' . $rowNumber, $objective->desc);
        $firstSheet->mergeCells('C' . $rowNumber . ':F' . $rowNumber);
        $firstSheet->mergeCells('C' . ($rowNumber + 1) . ':F' . ($rowNumber + 1));

        $rowNumber += 2;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':B' . ($rowNumber + 1));

        $firstSheet->setCellValue('A' . $rowNumber, 'Пункты посадки');
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $firstSheet->setCellValue('C' . $rowNumber, $this->extractLandPoints($row->routeName));
        $firstSheet->mergeCells('C' . $rowNumber . ':F' . $rowNumber);
        $firstSheet->mergeCells('C' . ($rowNumber + 1) . ':F' . ($rowNumber + 1));

        $rowNumber += 2;

        $firstSheet->mergeCells('A' . $rowNumber . ':F' . $rowNumber);
        $rowNumber += 1;

        $firstSheet->mergeCells('A' . $rowNumber . ':F' . $rowNumber);
        $firstSheet->setCellValue('A' . $rowNumber, 'ЭКИПАЖ ДОПУЩЕН ПО МЕТЕОМИНИМУМУ');
        $firstSheet->getStyle('A' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
    }
}

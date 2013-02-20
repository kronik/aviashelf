<?php
class FlightplanController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add', 'xls');
    protected $_modelName = 'Flightplans';
    protected $_buttons = array ('xls');

    protected function _initFields()
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('visible', '1');
        
        $this->_form->add(new Kwf_Form_Field_DateField('planDate', trlKwf('Date')))->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Responsible')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {        
        if ($row->employeeId != NULL)
        {
            $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
            $employeesSelect = $employeesModel->select()->whereEquals('id', $row->employeeId);
            
            $prow = $employeesModel->getRow($employeesSelect);
            $row->employeeName = (string)$prow;
        }
        
        return $row;
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
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
    
    protected function extractLandPoints($rawRoute, $points, $keys)
    {
        $route = explode("-", $rawRoute);
        #$points = array();
        #$keys = array();
        
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
        
        return;
    }
    
    protected function getFormattedDate($date)
    {
        if ($date == NULL)
        {
            return '';
        }

        $newDate = new DateTime ($date);
        return $newDate->format('d-m-Y');
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();

        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('id', $row->employeeId);
        
        $employeeRow = $employeesModel->getRow($employeesSelect);

        $xls->getProperties()->setCreator(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setLastModifiedBy(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setTitle("План полетов");
        $xls->getProperties()->setSubject("План полетов");
        $xls->getProperties()->setDescription("План полетов на сегодня");
        $xls->getProperties()->setKeywords("");
        $xls->getProperties()->setCategory("");
        
        $xls->getDefaultStyle()->getFont()->setSize(7);
        
        $firstSheet->getPageSetup()->setFitToPage(true);
        $firstSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $firstSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $pageMargins = $firstSheet->getPageMargins();
        
        $margin = 0.4;
        
        $pageMargins->setTop($margin);
        $pageMargins->setBottom($margin);
        $pageMargins->setLeft($margin);
        $pageMargins->setRight($margin);
        
        $firstSheet->mergeCells('A1:M1');
        $firstSheet->mergeCells('A3:M3');
        $firstSheet->mergeCells('A5:M5');
                
        $firstSheet->setCellValue('B2', trlKwf('Date') . ': ' . $this->getFormattedDate($row->planDate));
        
        $firstSheet->setCellValue('K2', trlKwf('Responsible') . ': ');
        $firstSheet->setCellValue('M2', (string)$employeeRow);
        
        $firstSheet->setCellValue('A4', trlKwf('Daily flights plan'));
        $firstSheet->getStyle('A4')->getFont()->setSize(12);
        $firstSheet->getStyle('A4')->getFont()->setBold(true);
        $firstSheet->getStyle('A4')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
        
        $firstSheet->mergeCells('A4:M4');

        $firstSheet->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
        
        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('planId', $row->id)->order(array('subCompanyId', 'flightStartTime'));
        
        $flights = $flightsModel->getRows($flightsSelect);
        $flightSequenceNumber = 1;
        $lastSubcompanyId = 0;
        
        $firstSheet->getColumnDimension('A')->setWidth('3pt');
        $firstSheet->getColumnDimension('C')->setWidth('7pt');
        $firstSheet->getColumnDimension('D')->setWidth('10pt');
        $firstSheet->getColumnDimension('E')->setWidth('40pt');
        $firstSheet->getColumnDimension('F')->setWidth('15pt');
        $firstSheet->getColumnDimension('G')->setWidth('15pt');
        $firstSheet->getColumnDimension('H')->setWidth('15pt');
        $firstSheet->getColumnDimension('I')->setWidth('15pt');
        $firstSheet->getColumnDimension('J')->setWidth('15pt');
        $firstSheet->getColumnDimension('M')->setWidth('15pt');

        $firstSheet->setCellValue('A6', trlKwf('#'));
        $firstSheet->setCellValue('B6', trlKwf('Customer'));
        $firstSheet->setCellValue('C6', trlKwf('Time'));
        $firstSheet->setCellValue('D6', trlKwf('WS Number'));
        $firstSheet->setCellValue('E6', trlKwf('Route'));
        $firstSheet->setCellValue('F6', trlKwf('KWS'));
        $firstSheet->setCellValue('G6', trlKwf('Instructor (check)'));
        $firstSheet->setCellValue('H6', trlKwf('Second pilot'));
        $firstSheet->setCellValue('I6', trlKwf('Technic'));
        $firstSheet->setCellValue('J6', trlKwf('Resquer'));
        $firstSheet->setCellValue('K6', trlKwf('Objective'));
        $firstSheet->setCellValue('L6', trlKwf('Task number'));
        $firstSheet->setCellValue('M6', trlKwf('Comment'));
        
        $firstSheet->getStyle('G6')->getAlignment()->setWrapText(true);
        
        $firstSheet->getStyle('A6')->getFont()->setBold(true);
        $firstSheet->getStyle('B6')->getFont()->setBold(true);
        $firstSheet->getStyle('C6')->getFont()->setBold(true);
        $firstSheet->getStyle('D6')->getFont()->setBold(true);
        $firstSheet->getStyle('E6')->getFont()->setBold(true);
        $firstSheet->getStyle('F6')->getFont()->setBold(true);
        $firstSheet->getStyle('G6')->getFont()->setBold(true);
        $firstSheet->getStyle('H6')->getFont()->setBold(true);
        $firstSheet->getStyle('I6')->getFont()->setBold(true);
        $firstSheet->getStyle('J6')->getFont()->setBold(true);
        $firstSheet->getStyle('K6')->getFont()->setBold(true);
        $firstSheet->getStyle('L6')->getFont()->setBold(true);
        $firstSheet->getStyle('M6')->getFont()->setBold(true);
        
        $firstSheet->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('B6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('C6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('D6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('E6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('F6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('G6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('H6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('I6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('J6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('K6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('L6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('M6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $firstSheet->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('J6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('L6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('M6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $firstSheet->getColumnDimension('B')->setAutoSize(true);

        $rowNumber = 7;
        
        foreach ($flights as $flight)
        {
            if ($lastSubcompanyId != $flight->subCompanyId)
            {
                if ($rowNumber != 7)
                {
                    $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
                    $rowNumber += 1;
                }
                $flightSequenceNumber = 0;
            }
            
            $lastSubcompanyId = $flight->subCompanyId;
            $flightSequenceNumber += 1;
            
            $flightStartTime = new DateTime($flight->flightStartTime);
            $flightStartTime = $flightStartTime->format("H:i");
            
            $plane = explode("-", $flight->planeName);
            
            $firstSheet->setCellValue('A' . $rowNumber, $flightSequenceNumber);
            $firstSheet->setCellValue('B' . $rowNumber, $flight->subCompanyName);
            $firstSheet->setCellValue('C' . $rowNumber, $flightStartTime);
            $firstSheet->setCellValue('D' . $rowNumber, $plane[1]);
            $firstSheet->setCellValue('E' . $rowNumber, $flight->routeName);
            $firstSheet->setCellValue('F' . $rowNumber, $flight->firstPilotName);
            $firstSheet->setCellValue('G' . $rowNumber, $flight->checkPilotName);
            $firstSheet->setCellValue('H' . $rowNumber, $flight->secondPilotName);
            $firstSheet->setCellValue('I' . $rowNumber, $flight->technicName);
            $firstSheet->setCellValue('J' . $rowNumber, $flight->resquerName);
            $firstSheet->setCellValue('K' . $rowNumber, $flight->objectiveName);
            $firstSheet->setCellValue('L' . $rowNumber, $flight->requestNumber);
            $firstSheet->setCellValue('M' . $rowNumber, $flight->comments);
            
            $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $firstSheet->getStyle('C' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            
            $rowNumber += 1;
        }
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $rowNumber += 1;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->setCellValue('A' . $rowNumber, 'Прокрутку ВС дежурящего по ПСО/АСР по ЭНЛ производить до первого вылета (1 раз в 2 дня).');

        $rowNumber += 1;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $rowNumber += 1;

        $firstSheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);
        $firstSheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber);

        $firstSheet->setCellValue('A' . $rowNumber, 'Базовый аэропорт');
        $firstSheet->setCellValue('C' . $rowNumber, 'Дежурный КВС');
        $firstSheet->setCellValue('E' . $rowNumber, 'Руководитель ПБ (СЭИК)');
        $firstSheet->setCellValue('F' . $rowNumber, 'Руководитель ПБ (ЭНЛ)');
        $firstSheet->setCellValue('G' . $rowNumber, 'Руководитель ЛС ИАС');
        $firstSheet->setCellValue('H' . $rowNumber, 'Диспетчер ПДС по ОП');
        $firstSheet->setCellValue('I' . $rowNumber, 'Дежурный по компании');
        
        $firstSheet->getStyle('A' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('C' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('E' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('F' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('G' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('C' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('E' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $firstSheet->getStyle('E' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setWrapText(true);
        
        $rowNumber += 1;

        $flighttracksModel = Kwf_Model_Abstract::getInstance('Flighttracks');
        $flighttracksSelect = $flighttracksModel->select()->whereEquals('planId', $row->id)->order('id');
        
        $flighttracks = $flighttracksModel->getRows($flighttracksSelect);

        foreach ($flighttracks as $flighttrack)
        {
            $firstSheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);
            $firstSheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber);

            $firstSheet->setCellValue('A' . $rowNumber, $flighttrack->airportName);
            $firstSheet->setCellValue('C' . $rowNumber, $flighttrack->employee1Name);
            $firstSheet->setCellValue('E' . $rowNumber, $flighttrack->employee2Name);
            $firstSheet->setCellValue('F' . $rowNumber, $flighttrack->employee3Name);
            $firstSheet->setCellValue('G' . $rowNumber, $flighttrack->employee4Name);
            $firstSheet->setCellValue('H' . $rowNumber, $flighttrack->employee5Name);
            $firstSheet->setCellValue('I' . $rowNumber, $flighttrack->employee6Name);
            
            $rowNumber += 1;
        }
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $rowNumber += 1;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('A' . $rowNumber)->getFont()->setBold(true);

        $firstSheet->setCellValue('A' . $rowNumber, 'Дополнительная информация');
        
        $rowNumber += 1;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . ($rowNumber + 3));
        $rowNumber += 4;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('A' . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->setCellValue('A' . $rowNumber, 'Диспетчерская сводка ПДО');
        
        $rowNumber += 1;

        $firstSheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);
        $firstSheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber);
        $firstSheet->mergeCells('K' . $rowNumber . ':M' . $rowNumber);

        $firstSheet->setCellValue('A' . $rowNumber, 'Клиент');
        $firstSheet->setCellValue('C' . $rowNumber, 'Номер ВС');
        $firstSheet->setCellValue('E' . $rowNumber, 'Место базирования');
        $firstSheet->setCellValue('F' . $rowNumber, 'Приоритет');
        $firstSheet->setCellValue('G' . $rowNumber, 'Состояние');
        $firstSheet->setCellValue('H' . $rowNumber, 'На дату');
        $firstSheet->setCellValue('I' . $rowNumber, 'Причина неисправности');
        $firstSheet->setCellValue('J' . $rowNumber, 'Дата ввода в строй');
        $firstSheet->setCellValue('K' . $rowNumber, 'Дополнительная информация');
        
        $firstSheet->getStyle('A' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('C' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('E' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('F' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('G' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('H' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('J' . $rowNumber)->getFont()->setBold(true);
        $firstSheet->getStyle('K' . $rowNumber)->getFont()->setBold(true);

        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('C' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('E' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setWrapText(true);
                
        $rowNumber += 1;

        $planerstatesModel = Kwf_Model_Abstract::getInstance('Planerstates');
        $planerstatesSelect = $planerstatesModel->select()->whereEquals('planId', $row->id)->order(array('typeId', 'id'));
        
        $planerstates = $planerstatesModel->getRows($planerstatesSelect);
        
        foreach ($planerstates as $planerstate)
        {
            $firstSheet->mergeCells('A' . $rowNumber . ':B' . $rowNumber);
            $firstSheet->mergeCells('C' . $rowNumber . ':D' . $rowNumber);
            $firstSheet->mergeCells('K' . $rowNumber . ':M' . $rowNumber);

            $firstSheet->setCellValue('A' . $rowNumber, $planerstate->typeName);
            $firstSheet->setCellValue('C' . $rowNumber, $planerstate->planeName);
            $firstSheet->setCellValue('E' . $rowNumber, $planerstate->landpointName);
            $firstSheet->setCellValue('F' . $rowNumber, $planerstate->priority);
            $firstSheet->setCellValue('G' . $rowNumber, $planerstate->statusName);
            $firstSheet->setCellValue('H' . $rowNumber, $this->getFormattedDate($planerstate->statusDate));
            $firstSheet->setCellValue('I' . $rowNumber, $planerstate->reason);
            $firstSheet->setCellValue('J' . $rowNumber, $this->getFormattedDate($planerstate->expectedDate));
            $firstSheet->setCellValue('K' . $rowNumber, $planerstate->comment);
            
            $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setWrapText(true);

            $rowNumber += 1;
        }
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $rowNumber += 1;
    }
}

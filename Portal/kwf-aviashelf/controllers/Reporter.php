<?php
class Reporter
{
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
    
//    protected function extractLandPoints($rawRoute, $points, $keys)
//    {
//        $route = explode("-", $rawRoute);
//        #$points = array();
//        #$keys = array();
//        
//        $landPoint = '';
//        
//        foreach ($route as $point)
//        {
//            $point = trim($point, " ");
//            $key = strtoupper($point);
//            
//            if (in_array($key, $keys) == false)
//            {
//                array_push($points, $point);
//                array_push($keys, $key);
//            }
//        }
//        
//        return;
//    }
    
    protected function getFormattedDate($date)
    {
        if ($date == NULL)
        {
            return '';
        }
        
        $newDate = new DateTime ($date);
        return $newDate->format('d-m-Y');
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
            if (strlen($point) > 2)
            {
                $landPoint = $landPoint . '. ' . $point;
            }
        }
        
        return $landPoint;
    }
    
    public function exportFlightPlanToXls($xls, $firstSheet, $row, $progressBar)
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('id', $row->employeeId);
        
        $employeeRow = $employeesModel->getRow($employeesSelect);
        
        $styleThinBlackBorderOutline = array(
                                             'borders' => array(
                                                                'outline' => array(
                                                                                   'style' => PHPExcel_Style_Border::BORDER_THIN,
                                                                                   'color' => array('argb' => 'FF000000'),
                                                                                   ),
                                                                ),
                                             );
        
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
        $firstSheet->setTitle('План полетов');
        
        $pageMargins = $firstSheet->getPageMargins();
        
        $margin = 0.4;
        
        $pageMargins->setTop($margin);
        $pageMargins->setBottom($margin);
        $pageMargins->setLeft($margin);
        $pageMargins->setRight($margin);
        
        $firstSheet->mergeCells('A1:M1');
        $firstSheet->mergeCells('A3:M3');
        
        $firstSheet->mergeCells('A2:I2');
        $firstSheet->mergeCells('J2:K2');
        $firstSheet->setCellValue('J2', 'План передал: ');
        $firstSheet->getStyle('J2')->getFont()->setBold(true);

        $firstSheet->mergeCells('L2:M2');
        $firstSheet->setCellValue('L2', (string)$employeeRow);
        $firstSheet->getStyle('L2')->getFont()->getColor()->applyFromArray(array('rgb' => 'FF2200'));

        $firstSheet->setCellValue('A4', trlKwf('Daily flights plan'));
        $firstSheet->getStyle('A4')->getFont()->setSize(12);
        $firstSheet->getStyle('A4')->getFont()->setBold(true);
        #$firstSheet->getStyle('A4')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $firstSheet->getStyle('A4')->getFont()->getColor()->applyFromArray(array('rgb' => 'FF2200'));
        $firstSheet->getStyle('A4:M4')->applyFromArray($styleThinBlackBorderOutline);

        $firstSheet->getStyle('A4')->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                               'startcolor' => array('rgb' => 'E0E0E0')
                               ));
        
        $firstSheet->mergeCells('A4:M4');
        $firstSheet->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
        
        $firstSheet->mergeCells('A5:E5');
        $firstSheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $firstSheet->getStyle('A5')->getFont()->setBold(true);
        $firstSheet->setCellValue('A5', 'На: ');

        $firstSheet->mergeCells('F5:M5');
        $firstSheet->getStyle('F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $firstSheet->getStyle('F5')->getFont()->setBold(true);
        $firstSheet->getStyle('F5')->getFont()->getColor()->applyFromArray(array('rgb' => 'FF2200'));

        $firstSheet->setCellValue('F5', $this->getFormattedDate($row->planDate));
        
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
        
        $firstSheet->setCellValue('A6', trlKwf('№'));
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
        
        $firstSheet->getStyle('A6:B6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('B6:C6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('C6:D6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('D6:E6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('E6:F6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('F6:G6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('G6:H6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('H6:I6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('I6:J6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('J6:K6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('K6:L6')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('L6:M6')->applyFromArray($styleThinBlackBorderOutline);

        $firstSheet->getStyle('A6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('B6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('C6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('D6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('E6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('F6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('G6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('H6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('I6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('J6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('K6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('L6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('M6')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        
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
        
        $progressBar->update(10);
        
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
            
            $firstSheet->getStyle('A' . $rowNumber)->getFont()->setBold(true);
            $firstSheet->getStyle('D' . $rowNumber)->getFont()->setBold(true);
            
            $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $firstSheet->getStyle('C' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);

            $firstSheet->getStyle('A' . $rowNumber .':B'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('B' . $rowNumber .':C'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('C' . $rowNumber .':D'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('D' . $rowNumber .':E'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('E' . $rowNumber .':F'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('F' . $rowNumber .':G'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('G' . $rowNumber .':H'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('H' . $rowNumber .':I'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('I' . $rowNumber .':J'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('J' . $rowNumber .':K'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('K' . $rowNumber .':L'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('L' . $rowNumber .':M'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);

            $rowNumber += 1;
        }
        
        $progressBar->update(60);
        
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
        
        $firstSheet->getStyle('A' . $rowNumber .':B'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('B' . $rowNumber .':C'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('C' . $rowNumber .':D'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('D' . $rowNumber .':E'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('E' . $rowNumber .':F'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('F' . $rowNumber .':G'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('G' . $rowNumber .':H'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('H' . $rowNumber .':I'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('I' . $rowNumber .':J'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('J' . $rowNumber .':K'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('K' . $rowNumber .':L'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('L' . $rowNumber .':M'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        
        $firstSheet->getStyle('A' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('C' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('E' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('F' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('G' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('H' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('I' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('J' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('K' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('L' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('M' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('C' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('E' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('C' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('E' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('M' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        
        $firstSheet->getStyle('E' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setWrapText(true);
        
        $rowNumber += 1;
        
        $flighttracksModel = Kwf_Model_Abstract::getInstance('Flighttracks');
        $flighttracksSelect = $flighttracksModel->select()->whereEquals('planId', $row->id)->order('id');
        
        $flighttracks = $flighttracksModel->getRows($flighttracksSelect);
        
        $progressBar->update(70);

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
            
            $firstSheet->getStyle('A' . $rowNumber .':B'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('B' . $rowNumber .':C'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('C' . $rowNumber .':D'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('D' . $rowNumber .':E'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('E' . $rowNumber .':F'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('F' . $rowNumber .':G'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('G' . $rowNumber .':H'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('H' . $rowNumber .':I'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('I' . $rowNumber .':J'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('J' . $rowNumber .':K'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('K' . $rowNumber .':L'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('L' . $rowNumber .':M'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            
            $rowNumber += 1;
        }
        
        $progressBar->update(80);

        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $rowNumber += 1;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('A' . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->getStyle('A' . $rowNumber)->getFont()->getColor()->applyFromArray(array('rgb' => '#0083CF'));
        
        $firstSheet->getStyle('A' . $rowNumber)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                               'startcolor' => array('rgb' => 'E0E0E0')
                               ));

        $firstSheet->setCellValue('A' . $rowNumber, 'Дополнительная информация');
        $firstSheet->getStyle('A' . $rowNumber .':M'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);

        $rowNumber += 1;
        
        $firstSheet->mergeCells('B' . $rowNumber . ':M' . ($rowNumber + 3));
        $firstSheet->mergeCells('A' . $rowNumber . ':A' . ($rowNumber + 3));

        $firstSheet->setCellValue('B' . $rowNumber, $row->comment);
        $firstSheet->getStyle('B' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('B' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $rowNumber += 4;
        
        $firstSheet->mergeCells('A' . $rowNumber . ':H' . $rowNumber);
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('A' . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->setCellValue('A' . $rowNumber, 'Диспетчерская сводка ПДО');
        $firstSheet->getStyle('A' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('A' . $rowNumber .':H'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('I' . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->setCellValue('I' . $rowNumber, 'Составил:');
        $firstSheet->getStyle('I' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('I' . $rowNumber .':I'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('J' . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->setCellValue('J' . $rowNumber, 'техник ПДО');
        $firstSheet->getStyle('J' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('J' . $rowNumber .':J'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);

        $firstSheet->mergeCells('K' . $rowNumber . ':M' . $rowNumber);
        $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('K' . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->setCellValue('K' . $rowNumber, $row->techName);
        $firstSheet->getStyle('K' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('K' . $rowNumber .':M'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('K' . $rowNumber)->getFont()->getColor()->applyFromArray(array('rgb' => 'FF2200'));
        
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
        
        $firstSheet->getStyle('A' . $rowNumber .':B'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('B' . $rowNumber .':C'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('C' . $rowNumber .':D'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('D' . $rowNumber .':E'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('E' . $rowNumber .':F'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('F' . $rowNumber .':G'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('G' . $rowNumber .':H'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('H' . $rowNumber .':I'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('I' . $rowNumber .':J'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('J' . $rowNumber .':K'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('K' . $rowNumber .':L'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->getStyle('L' . $rowNumber .':M'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
        
        $firstSheet->getStyle('A' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('C' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('E' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('F' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('G' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('H' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('I' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('J' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $firstSheet->getStyle('K' . $rowNumber)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        
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

        $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('C' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('E' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('M' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $firstSheet->getStyle('I' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setWrapText(true);
        
        $rowNumber += 1;
        
        $planerstatesModel = Kwf_Model_Abstract::getInstance('Planerstates');
        $planerstatesSelect = $planerstatesModel->select()->whereEquals('planId', $row->id)->order(array('typeId', 'id'));
        
        $planerstates = $planerstatesModel->getRows($planerstatesSelect);
        
        $progressBar->update(90);

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
            
            $firstSheet->getStyle('A' . $rowNumber .':B'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('B' . $rowNumber .':C'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('C' . $rowNumber .':D'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('D' . $rowNumber .':E'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('E' . $rowNumber .':F'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('F' . $rowNumber .':G'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('G' . $rowNumber .':H'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('H' . $rowNumber .':I'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('I' . $rowNumber .':J'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('J' . $rowNumber .':K'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('K' . $rowNumber .':L'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);
            $firstSheet->getStyle('L' . $rowNumber .':M'. $rowNumber)->applyFromArray($styleThinBlackBorderOutline);

            $firstSheet->getStyle('F' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $firstSheet->getStyle('G' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $firstSheet->getStyle('H' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $firstSheet->getStyle('J' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $firstSheet->getStyle('K' . $rowNumber)->getAlignment()->setWrapText(true);
            
            $rowNumber += 1;
        }
        
        $progressBar->update(100);

        $firstSheet->getStyle('A1:M'. ($rowNumber - 1))->applyFromArray($styleThinBlackBorderOutline);
        
        $firstSheet->mergeCells('A' . $rowNumber . ':M' . $rowNumber);
        $rowNumber += 1;
    }
    
    public function exportFlightTaskToXls($xls, $firstSheet, $row, $progressBar)
    {        
//        foreach($firstSheet->getRowDimensions() as $rd)
//        {
//            $rd->setRowHeight(-1);
//        }
        
        $xls->getProperties()->setCreator(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setLastModifiedBy(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setTitle("Полетное Задание");
        $xls->getProperties()->setSubject("Полетное Задание");
        $xls->getProperties()->setDescription("Полетное Задание на сегодня");
        $xls->getProperties()->setKeywords("");
        $xls->getProperties()->setCategory("");
        
        $progressBar->update(10);
        
//        $firstSheet->getPageSetup()->setFitToPage(true);
        $firstSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $firstSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $firstSheet->setTitle('Титульный лист');

        $pageMargins = $firstSheet->getPageMargins();
        
        $margin = 0.3 / 2.54;
        
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
//        $totalLeftColumns = 28;
//        
        for ($i = 0; $i <= 100; $i++)
        {
            $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($i))->setWidth('1.9pt');
        }
//
//        $tableColumn = $this->_getColumnLetterByIndex($totalLeftColumns - 1);
//        $tableHeaderColumnt = $this->_getColumnLetterByIndex($totalLeftColumns);
//        
//        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($totalLeftColumns + 1))->setWidth('10pt');
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($totalLeftColumns + 1) . '1:' . $this->_getColumnLetterByIndex($totalLeftColumns + 1) . '39');
//        
//        $firstSheet->getStyle('A1:' . $tableColumn . '13')->applyFromArray($styleThinBlackBorderOutline);
//        $firstSheet->mergeCells('A1:' . $tableColumn . '13');
//        
//        $firstSheet->getStyle($tableHeaderColumnt . '1:' . $tableHeaderColumnt . '13')->applyFromArray($styleThinBlackBorderOutline);
//        $firstSheet->mergeCells($tableHeaderColumnt . '1:' . $tableHeaderColumnt . '13');
//        $firstSheet->setCellValue($tableHeaderColumnt . '1', 'Предполётный медосмотр');
//        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setWrapText(true);
//        $firstSheet->getStyle($tableHeaderColumnt . '1')->getFont()->setBold(true);
//        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setTextRotation(-90);
//        
//        $firstSheet->getStyle('A14:' . $tableColumn . '26')->applyFromArray($styleThinBlackBorderOutline);
//        $firstSheet->mergeCells('A14:' . $tableColumn . '26');
//        
//        $firstSheet->getStyle($tableHeaderColumnt . '14:' . $tableHeaderColumnt . '26')->applyFromArray($styleThinBlackBorderOutline);
//        $firstSheet->mergeCells($tableHeaderColumnt . '14:' . $tableHeaderColumnt . '26');
//        
//        $firstSheet->setCellValue($tableHeaderColumnt . '14', 'Спецконроль в аэропортах');
//        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setWrapText(true);
//        $firstSheet->getStyle($tableHeaderColumnt . '14')->getFont()->setBold(true);
//        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setTextRotation(-90);
//        
//        $firstSheet->getStyle('A27:' . $tableColumn . '39')->applyFromArray($styleThinBlackBorderOutline);
//        $firstSheet->mergeCells('A27:B39');
//        
//        $firstSheet->getStyle($tableHeaderColumnt . '27:' . $tableHeaderColumnt . '39')->applyFromArray($styleThinBlackBorderOutline);
//        $firstSheet->mergeCells($tableHeaderColumnt . '27:' . $tableHeaderColumnt . '39');
//        
//        $firstSheet->setCellValue($tableHeaderColumnt . '27', 'Результаты послеполётного разбора');
//        $firstSheet->getStyle($tableHeaderColumnt . '27')->getFont()->setBold(true);
//        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setWrapText(true);
//        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setTextRotation(-90);
//        
//        $progressBar->update(10);
//        
//        for ($i = 2; $i <= $totalLeftColumns-1; $i++)
//        {
//            $col = $this->_getColumnLetterByIndex($i);
//            $firstSheet->mergeCells($col . '27:' . $col . '39');
//        }
//        
//        $rightColumn = $totalLeftColumns + 2;
//        $rowNumber = 1;
//        
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
//        $rowNumber += 1;
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
//        
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setSize(10);
//        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, trlKwf('Дальневосточное межрегиональное территориальное управление'));
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        
//        $rowNumber += 1;
//        
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
//        
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setSize(10);
//        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, trlKwf('воздушного транспорта ФАВТ'));
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        
//        $rowNumber += 1;
//        
//        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn))->setWidth('15pt');
//        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn + 1))->setWidth('15pt');
//        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn + 2))->setWidth('10pt');
//        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn + 3))->setWidth('10pt');
//        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn + 4))->setWidth('15pt');
//
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
//        $rowNumber += 1;
//        
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . ($rowNumber + 3));
//        
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        
//        $objDrawing = new PHPExcel_Worksheet_Drawing();
//        $objDrawing->setName('Logo');
//        $objDrawing->setDescription('Logo');
//        $objDrawing->setPath('./images/doc_logo.png');
//        $objDrawing->setCoordinates($this->_getColumnLetterByIndex($rightColumn) . $rowNumber);
//        $objDrawing->setWidth('360px');
//        $objDrawing->setOffsetX(50);
//        $objDrawing->setWorksheet($firstSheet);
//        
//        $rowNumber += 4;
//        
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
//        $rowNumber += 1;
//        
//        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 2) . $rowNumber);
//        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getFont()->setBold(true);
//        
//        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
//        $flightsSelect = $flightsModel->select()->whereEquals('planId', $row->planId)->order(array('subCompanyId', 'flightStartTime'));
//        
//        $flights = $flightsModel->getRows($flightsSelect);
//        $flightSequenceNumber = 1;
//        $lastSubcompanyId = 0;
//        
//        foreach ($flights as $flight)
//        {
//            if ($lastSubcompanyId != $flight->subCompanyId)
//            {
//                $flightSequenceNumber = 0;
//            }
//            
//            $lastSubcompanyId = $flight->subCompanyId;
//            $flightSequenceNumber += 1;
//            
//            if ($flight->id == $row->id)
//            {
//                break;
//            }
//        }
        
        $firstSheet->getStyle('AD1')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('AD1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('AD1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('AD1')->getAlignment()->setTextRotation(-90);

        $firstSheet->getStyle('AD14')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('AD14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('AD14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('AD14')->getAlignment()->setTextRotation(-90);

        $firstSheet->getStyle('AD26')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle('AD26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle('AD26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle('AD26')->getAlignment()->setTextRotation(-90);
        
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath('./images/doc_logo.png');
        $objDrawing->setCoordinates('AK5');
        $objDrawing->setWidth('360px');
        $objDrawing->setOffsetX(50);
        $objDrawing->setWorksheet($firstSheet);
        
        $progressBar->update(20);
        
        $firstSheet->getStyle('AW9')->getAlignment()->setWrapText(true);

        $firstSheet->getCell('AW9')->setValueExplicit($row->number, PHPExcel_Cell_DataType::TYPE_STRING);
        
        //$firstSheet->setCellValue('AW9', $row->number);
        $firstSheet->setCellValue('BI9', $row->requestNumber);
        
        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $row->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $typeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $typeSelect = $typeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $typeModel->getRow($typeSelect);
        
        $firstSheet->setCellValue('AW11', $planeType->Name);
        $firstSheet->setCellValue('BG11', $row->planeName);
        //$firstSheet->setCellValue('AJ12', $row->firstPilotName);

        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $row->id)->whereEquals('mainCrew', TRUE)->order('id');
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $subSpecModel = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $kwsId = 0;
        
        $progressBar->update(30);

        $rowNumber = 15;
        
        foreach ($flightMembers as $flightMember)
        {
            $employeesSelect = $employeesModel->select()->whereEquals('id', $flightMember->employeeId);
            $employeeRow = $employeesModel->getRow($employeesSelect);
            
            if ($employeeRow == NULL)
            {
                continue;
            }
            
            $subSpecSelect = $subSpecModel->select()->whereEquals('id', $employeeRow->positionId);
            $subSpecRow = $subSpecModel->getRow($subSpecSelect);
            
            $position = $flightMember->positionName;
            
            if (($position == 'КВС') && ($kwsId == 0))
            {
                $kwsId = $flightMember->employeeId;
            }
            
            if ($position == 'Инструктор') {
                $kwsId = $flightMember->employeeId;
            }
                    
            if ($position == 'Проверяющий') {
                $kwsId = $flightMember->employeeId;
            }

            $firstSheet->setCellValue('AJ' . $rowNumber, $position);
            $firstSheet->setCellValue('AS' . $rowNumber, (string)$employeeRow);

            $rowNumber += 1;
        }
        
        $progressBar->update(60);
        
        $flightDate = new DateTime ($row->flightStartDate);
        
        $flightStartTime = new DateTime($row->flightStartTime);
        $flightStartTime = $flightStartTime->format("H:i");
        
        $firstSheet->setCellValue('AS21', $this->russianDate($flightDate->format('d-m-Y')));
        $firstSheet->setCellValue('BJ21', $flightStartTime);
        
        $objectiveModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $objectiveSelect = $objectiveModel->select()->whereEquals('id', $row->objectiveId);
        $objective = $objectiveModel->getRow($objectiveSelect);
        
        $firstSheet->setCellValue('AS22', $row->routeName);
        $firstSheet->setCellValue('AS24', $objective->desc);
        $firstSheet->setCellValue('AS26', $row->routeName);

        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $row->id)->whereEquals('mainCrew', FALSE)->order('id');
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $subSpecModel = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $progressBar->update(70);

        $rowNumber = 28;

        foreach ($flightMembers as $flightMember)
        {
            $employeesSelect = $employeesModel->select()->whereEquals('id', $flightMember->employeeId);
            $employeeRow = $employeesModel->getRow($employeesSelect);
            
            $position = $flightMember->positionName;
            
            if ($position == 'По специальности')
            {
                if ($employeeRow->positionId == NULL) {
                    $position = '';
                } else {
                    $subSpecSelect = $subSpecModel->select()->whereEquals('id', $employeeRow->positionId);
                    $subSpecRow = $subSpecModel->getRow($subSpecSelect);

                    $position = $subSpecRow->value;
                }
            }
            
            if ($employeeRow != NULL)
            {
                $firstSheet->setCellValue('AJ' . $rowNumber, $position);
                $firstSheet->setCellValue('AS' . $rowNumber, (string)$employeeRow);
                
                $rowNumber += 1;
            }
        }
        
        $progressBar->update(90);
        
        $accessesModel = Kwf_Model_Abstract::getInstance('Flightaccesses');
        $accessesSelect = $accessesModel->select()->where(new Kwf_Model_Select_Expr_Sql("`employeeId` = " . $kwsId . " AND `wsTypeId` = " . $planeType->id . "  AND `finished` = 0"));
        $accesses = $accessesModel->getRows($accessesSelect);
        
        $accessStr = "";
        $lineCounter = 0;
        
        foreach ($accesses as $access)
        {
            $delimiter = "\n";
            
//            if ($lineCounter % 2 == 0) {
//                $delimiter = ", ";
//            }
            
            $accessStr = $accessStr . $access->accessName . $delimiter;
            
            $lineCounter++;
        }
        
        if (($accessStr == "") || (count($accesses) == 0)) {
            throw new Kwf_Exception_Client('Нет доступа по метеоминимумам.');
        }

        $firstSheet->setCellValue('AJ35', $accessStr);
        
        $progressBar->update(100);
    }
    
    public function exportFlightTaskToXlsOld($xls, $firstSheet, $row, $progressBar)
    {
        foreach($firstSheet->getRowDimensions() as $rd)
        {
            $rd->setRowHeight(-1);
        }
        
        $xls->getProperties()->setCreator(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setLastModifiedBy(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setTitle("Полетное Задание");
        $xls->getProperties()->setSubject("Полетное Задание");
        $xls->getProperties()->setDescription("Полетное Задание на сегодня");
        $xls->getProperties()->setKeywords("");
        $xls->getProperties()->setCategory("");
        
        $firstSheet->getPageSetup()->setFitToPage(true);
        $firstSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $firstSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $firstSheet->setTitle('Титульный лист');
        $pageMargins = $firstSheet->getPageMargins();
        
        $margin = 0.42;
        
        $pageMargins->setTop($margin * 2);
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
            $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($i))->setWidth('2.0pt');
        }
        
        $tableColumn = $this->_getColumnLetterByIndex($totalLeftColumns - 1);
        $tableHeaderColumnt = $this->_getColumnLetterByIndex($totalLeftColumns);
        
        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($totalLeftColumns + 1))->setWidth('10pt');
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($totalLeftColumns + 1) . '1:' . $this->_getColumnLetterByIndex($totalLeftColumns + 1) . '39');
        
        $firstSheet->getStyle('A1:' . $tableColumn . '13')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells('A1:' . $tableColumn . '13');
        
        $firstSheet->getStyle($tableHeaderColumnt . '1:' . $tableHeaderColumnt . '13')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells($tableHeaderColumnt . '1:' . $tableHeaderColumnt . '13');
        $firstSheet->setCellValue($tableHeaderColumnt . '1', 'Предполётный медосмотр');
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getFont()->setBold(true);
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '1')->getAlignment()->setTextRotation(-90);
        
        $firstSheet->getStyle('A14:' . $tableColumn . '26')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells('A14:' . $tableColumn . '26');
        
        $firstSheet->getStyle($tableHeaderColumnt . '14:' . $tableHeaderColumnt . '26')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells($tableHeaderColumnt . '14:' . $tableHeaderColumnt . '26');
        
        $firstSheet->setCellValue($tableHeaderColumnt . '14', 'Спецконроль в аэропортах');
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getFont()->setBold(true);
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '14')->getAlignment()->setTextRotation(-90);
        
        $firstSheet->getStyle('A27:' . $tableColumn . '39')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells('A27:B39');
        
        $firstSheet->getStyle($tableHeaderColumnt . '27:' . $tableHeaderColumnt . '39')->applyFromArray($styleThinBlackBorderOutline);
        $firstSheet->mergeCells($tableHeaderColumnt . '27:' . $tableHeaderColumnt . '39');
        
        $firstSheet->setCellValue($tableHeaderColumnt . '27', 'Результаты послеполётного разбора');
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getFont()->setBold(true);
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setWrapText(true);
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $firstSheet->getStyle($tableHeaderColumnt . '27')->getAlignment()->setTextRotation(-90);
        
        $progressBar->update(10);
        
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
        
        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn))->setWidth('15pt');
        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn + 1))->setWidth('15pt');
        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn + 2))->setWidth('10pt');
        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn + 3))->setWidth('10pt');
        $firstSheet->getColumnDimension($this->_getColumnLetterByIndex($rightColumn + 4))->setWidth('15pt');
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . ($rowNumber + 3));
        
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn) . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Logo');
        $objDrawing->setDescription('Logo');
        $objDrawing->setPath('./images/doc_logo.png');
        $objDrawing->setCoordinates($this->_getColumnLetterByIndex($rightColumn) . $rowNumber);
        $objDrawing->setWidth('360px');
        $objDrawing->setOffsetX(50);
        $objDrawing->setWorksheet($firstSheet);
        
        $rowNumber += 4;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 2) . $rowNumber);
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
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, trlKwf('ЗАДАНИЕ НА ПОЛЁТ №') . $row->number);
        
        #$firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 2) . $rowNumber, $flightSequenceNumber . ' / ' . $row->flightStartDate);
        #$firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, $row->number);
        #$firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber)->getFont()->setBold(true);
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 3) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 3) . $rowNumber, 'ЮШ ' . $row->requestNumber);
        $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 3) . $rowNumber)->getFont()->setBold(true);
        
        $rowNumber += 1;
        
        $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
        $rowNumber += 1;
        
        $planesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $planesSelect = $planesModel->select()->whereEquals('id', $row->planeId);
        $plane = $planesModel->getRow($planesSelect);
        
        $typeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $typeSelect = $typeModel->select()->whereEquals('id', $plane->twsId);
        $planeType = $typeModel->getRow($typeSelect);
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, 'Экипажу вертолёта:');
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
        
        $progressBar->update(50);
        
        foreach ($flightMembers as $flightMember)
        {
            $employeesSelect = $employeesModel->select()->whereEquals('id', $flightMember->employeeId);
            $employeeRow = $employeesModel->getRow($employeesSelect);
            
            if ($employeeRow == NULL)
            {
                continue;
            }
            
            $subSpecSelect = $subSpecModel->select()->whereEquals('id', $employeeRow->positionId);
            $subSpecRow = $subSpecModel->getRow($subSpecSelect);
            
            $position = $flightMember->positionName;
            
            if (($position == 'КВС') && ($kwsId == 0)) {
                $kwsId = $flightMember->employeeId;
            }

            if ($position == 'Инструктор') {
                $kwsId = $flightMember->employeeId;
            }

            if ($position == 'Проверяющий') {
                $kwsId = $flightMember->employeeId;
            }

            $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, $position);
            $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, (string)$employeeRow);
            
            $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber)->getFont()->setBold(true);
            
            $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
            
            $rowNumber += 1;
        }
        
        $progressBar->update(60);
        
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
        
        $rowNumber += 1;
        
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
        
        $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, $row->routeName);
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
        
        $progressBar->update(70);
        
        foreach ($flightMembers as $flightMember)
        {
            $employeesSelect = $employeesModel->select()->whereEquals('id', $flightMember->employeeId);
            $employeeRow = $employeesModel->getRow($employeesSelect);
            
            $position = $flightMember->positionName;
            
            if ($position == 'По специальности')
            {
                if ($employeeRow->positionId == NULL) {
                    $position = '';
                } else {
                    $subSpecSelect = $subSpecModel->select()->whereEquals('id', $employeeRow->positionId);
                    $subSpecRow = $subSpecModel->getRow($subSpecSelect);
                    
                    $position = $subSpecRow->value;
                }
            }
            
            if ($employeeRow != NULL)
            {
                $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn) . $rowNumber, $position);
                $firstSheet->setCellValue($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber, (string)$employeeRow);
                $firstSheet->getStyle($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber)->getFont()->setBold(true);
                
                $firstSheet->mergeCells($this->_getColumnLetterByIndex($rightColumn + 1) . $rowNumber . ':' . $this->_getColumnLetterByIndex($rightColumn + 4) . $rowNumber);
                
                $rowNumber += 1;
            }
        }
        
        $progressBar->update(80);
        
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
        $accessesSelect = $accessesModel->select()->where(new Kwf_Model_Select_Expr_Sql("`employeeId` = " . $kwsId . " AND `wsTypeId` = " . $planeType->id . "  AND `finished` = 0"));
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
        
        # Second page
        
        $xls->setActiveSheetIndex(1);
        $secondSheet = $xls->getActiveSheet();
        
        $secondSheet->getPageSetup()->setFitToPage(true);
        $secondSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $secondSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $secondSheet->setTitle('Отчет о полете');
        
        $pageMargins = $secondSheet->getPageMargins();
        
        $margin = 0.2;
        
        $pageMargins->setTop($margin);
        $pageMargins->setBottom($margin);
        $pageMargins->setLeft($margin);
        $pageMargins->setRight($margin);
        
        for ($column = 0; $column < 70; $column++) {
            $secondSheet->getColumnDimension($this->_getColumnLetterByIndex($column))->setWidth('2.5pt');
        }
        
        $secondSheet->getPageSetup()->setFitToPage(true);
        
        $xls->setActiveSheetIndex(0);
        
        $progressBar->update(100);
    }
    
    public function exportLastFlightPlanToXls()
    {
        ini_set('memory_limit', "768M");
        set_time_limit(600); // 10 minuten
        
        require_once Kwf_Config::getValue('externLibraryPath.phpexcel').'/PHPExcel.php';
        $xls = new PHPExcel();
        $xls->getProperties()->setCreator(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setLastModifiedBy(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setTitle("KWF Excel Export");
        $xls->getProperties()->setSubject("KWF Excel Export");
        $xls->getProperties()->setDescription("KWF Excel Export");
        $xls->getProperties()->setKeywords("KWF Excel Export");
        $xls->getProperties()->setCategory("KWF Excel Export");
        
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        
        //TODO: Need to load today flight plan row
        $this->exportFlightPlanToXls($xls, $sheet, $row);
        
        // write the file
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
        $downloadkey = uniqid();
        $objWriter->save('temp/'.$downloadkey.'.xls');
                
        return $downloadkey;
    }
    
    public function downloadXlsFileByKey($downloadkey)
    {
        if (!file_exists('temp/'.$downloadkey.'.xls')) {
            throw new Kwf_Exception('Wrong downloadkey submitted');
        }
        Kwf_Util_TempCleaner::clean();
        
        $file = array(
                      'contents' => file_get_contents('temp/'.$downloadkey.'.xls'),
                      'mimeType' => 'application/octet-stream',
                      'downloadFilename' => 'form_'.date('Ymd-Hi').'.xls'
                      );
        Kwf_Media_Output::output($file);
        $this->_helper->viewRenderer->setNoRender();
    }
}

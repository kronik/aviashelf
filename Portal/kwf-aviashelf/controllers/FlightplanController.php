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
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('id', $row->employeeId);
        
        $prow = $employeesModel->getRow($employeesSelect);
        $row->employeeName = (string)$prow;
        
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
        
        $firstSheet->mergeCells('A1:R1');
        $firstSheet->mergeCells('A3:R3');
        $firstSheet->mergeCells('A5:R5');

        $firstSheet->setCellValue('B2', trlKwf('Date') . ': ' . date('d-m-Y'));
        #$firstSheet->setCellValue('C2', date('d-m-Y'));
        $firstSheet->setCellValue('O2', trlKwf('Responsible') . ': ');
        $firstSheet->setCellValue('Q2', (string)$employeeRow);
        
        $firstSheet->mergeCells('B2:N2');
        $firstSheet->mergeCells('Q2:R2');
        $firstSheet->mergeCells('O2:P2');
        
        $firstSheet->setCellValue('A4', trlKwf('Daily flights plan'));
        $firstSheet->getStyle('A4')->getFont()->setSize(16);
        $firstSheet->getStyle('A4')->getFont()->setBold(true);
        $firstSheet->getStyle('A4')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
        
        $firstSheet->mergeCells('A4:R4');

        $firstSheet->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
        
        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('planId', $row->id)->order(array('subCompanyId', 'flightStartTime'));
        
        $flights = $flightsModel->getRows($flightsSelect);
        $flightSequenceNumber = 1;
        $lastSubcompanyId = 0;
        
        //$firstSheet->getColumnDimension('A')->setAutoSize(true);
        //$firstSheet->getColumnDimension('B')->setAutoSize(true);
        
        $firstSheet->getColumnDimension('A')->setWidth('7pt');
        $firstSheet->getColumnDimension('B')->setWidth('20pt');
        
        $firstSheet->setCellValue('A6', trlKwf('Number #'));
        $firstSheet->setCellValue('B6', trlKwf('Customer'));
        $firstSheet->setCellValue('C6', trlKwf('Time'));
        $firstSheet->setCellValue('D6', trlKwf('WS Number'));
        $firstSheet->setCellValue('E6', trlKwf('Route'));
        $firstSheet->setCellValue('K6', trlKwf('KWS'));
        $firstSheet->setCellValue('L6', trlKwf('Instructor'));
        $firstSheet->setCellValue('M6', trlKwf('Second pilot'));
        $firstSheet->setCellValue('N6', trlKwf('Technic'));
        $firstSheet->setCellValue('O6', trlKwf('Resquer'));
        $firstSheet->setCellValue('P6', trlKwf('Objective'));
        $firstSheet->setCellValue('Q6', trlKwf('Task number'));
        $firstSheet->setCellValue('R6', trlKwf('Comment'));
        
        $firstSheet->getStyle('A6')->getFont()->setBold(true);
        $firstSheet->getStyle('B6')->getFont()->setBold(true);
        $firstSheet->getStyle('C6')->getFont()->setBold(true);
        $firstSheet->getStyle('D6')->getFont()->setBold(true);
        $firstSheet->getStyle('E6')->getFont()->setBold(true);
        $firstSheet->getStyle('K6')->getFont()->setBold(true);
        $firstSheet->getStyle('L6')->getFont()->setBold(true);
        $firstSheet->getStyle('M6')->getFont()->setBold(true);
        $firstSheet->getStyle('N6')->getFont()->setBold(true);
        $firstSheet->getStyle('O6')->getFont()->setBold(true);
        $firstSheet->getStyle('P6')->getFont()->setBold(true);
        $firstSheet->getStyle('Q6')->getFont()->setBold(true);
        $firstSheet->getStyle('R6')->getFont()->setBold(true);
                
        $firstSheet->mergeCells('E6:J6');

        $rowNumber = 7;
        
        foreach ($flights as $flight)
        {
            if ($lastSubcompanyId != $flight->subCompanyId)
            {
                if ($rowNumber != 7)
                {
                    $firstSheet->mergeCells('A' . $rowNumber . ':R' . $rowNumber);
                    $rowNumber += 1;
                }
                $flightSequenceNumber = 0;
            }
            
            $lastSubcompanyId = $flight->subCompanyId;
            $flightSequenceNumber += 1;
            
            $flightStartTime = new DateTime($flight->flightStartTime);
            $flightStartTime = $flightStartTime->format("H:i");
            
            $firstSheet->setCellValue('A' . $rowNumber, $flightSequenceNumber);
            $firstSheet->setCellValue('B' . $rowNumber, $flight->subCompanyName);
            $firstSheet->setCellValue('C' . $rowNumber, $flightStartTime);
            $firstSheet->setCellValue('D' . $rowNumber, $flight->planeName);
            $firstSheet->setCellValue('E' . $rowNumber, $flight->routeName);
            $firstSheet->setCellValue('K' . $rowNumber, $flight->firstPilotName);
            $firstSheet->setCellValue('L' . $rowNumber, $flight->checkPilotName);
            $firstSheet->setCellValue('M' . $rowNumber, $flight->secondPilotName);
            $firstSheet->setCellValue('N' . $rowNumber, $flight->technicName);
            $firstSheet->setCellValue('O' . $rowNumber, $flight->resquerName);
            $firstSheet->setCellValue('P' . $rowNumber, $flight->objectiveName);
            $firstSheet->setCellValue('Q' . $rowNumber, $flight->number);
            $firstSheet->setCellValue('R' . $rowNumber, $flight->comments);
            
            $firstSheet->getStyle('A' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
            $firstSheet->getStyle('C' . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);

            $firstSheet->mergeCells('E' . $rowNumber . ':J' . $rowNumber);

            $rowNumber += 1;
        }
    }
}

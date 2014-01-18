<?php
class FlightgroupController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightgroups';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Позиции на борту')->order('value');
        
        $positions = new Kwf_Form_Field_Select('positionId', trlKwf('Position'));
        $positions->setValues($typeModel->getRows($typeSelect));
        $positions->setAllowBlank(false);
        $positions->setWidth(400);
        
        $employees = new Kwf_Form_Field_Select('employeeId', trlKwf('Employee'));
        $employees->setValues('/flightgroupsfilter/json-data');
        $employees->setAllowBlank(false);
        $employees->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_FilterField())
        ->setFilterColumn('positionId')
        ->setFilteredField($employees)
        ->setFilterField($positions)
        ->setWidth(400);
                
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);        
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        
        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('id', $row->flightId);
        
        $flightRow = $flightsModel->getRow($flightsSelect);
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'kws') {
            
            $flightDate = new DateTime ($flightRow->flightStartDate);
            
            $dateLimit = new DateTime('NOW');
            $dateLimit->sub( new DateInterval('P2D') );
            
            if ($flightDate < $dateLimit) {
                throw new Kwf_Exception_Client('ПЗ закрыто для изменений.');
            }
        }
        
        $s = $m1->select()->whereEquals('id', $row->positionId);
        $prow = $m1->getRow($s);
        $row->positionName = $prow->value;
                
        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        
        $row->employeeName = (string)$prow;
        
//
//        if ($row->mainCrew == TRUE)
//        {
//            $positionModel = Kwf_Model_Abstract::getInstance('Flightresultdefaults');
//            $positionSelect = $positionModel->select()->whereEquals('positionId', $row->positionId);
//
//            $positionRows = $positionModel->getRows($positionSelect);
//            
//            foreach ($positionRows as $positionRow) {
//                $this->addFlightResult($flightRow, $row, $positionRow->resultId);
//            }
//        }
    }
        
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->flightId = $this->_getParam('flightId');
        $row->mainCrew = TRUE;

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
    
        
    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $helper = new Helper();
        $helper->updateFlightResults ($row->flightId);
    }    
}

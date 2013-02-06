<?php
class FlightgroupController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightgroups';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        //$row = $this->_form->getRow();

//        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Позиции на борту')->order('value');
        
        $positions = $typeModel->getRows($typeSelect);
        $positionRecords = array();
        
        foreach ($positions as $position)
        {
            array_push($positionRecords, array('id'=>$position->id, 'value'=>$position->value));
        }
        
        $positions = new Kwf_Form_Field_Select('positionId', trlKwf('Position'));
        $positions->setValues($typeModel->getRows($typeSelect));
        //$positions->setSelect($typeSelect);
        //$positions->setSave(false);
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
        
//        if (($row != NULL) && ($row->positionId != NULL))
//        {
//            $groupModel = Kwf_Model_Abstract::getInstance('EmployeeFlightRoles');
//            $groupSelect = $groupModel->select()->whereEquals('groupId', $row->positionId);
//
//            $employeesSelect = $employeesModel->select()
//            ->where(new Kwf_Model_Select_Expr_Child_Contains('EmployeeFlightRoles', $groupSelect))
//            ->order('lastname');
//        }
//        else
//        {
//            $employeesSelect = $employeesModel->select()->order('lastname');
//        }
        
//        $this->_form->add(new Kwf_Form_Field_Select('positionId', trlKwf('Position')))
//        ->setValues($typeModel)
//        ->setSelect($typeSelect)
//        ->setWidth(400)
//        ->setAllowBlank(false);
        
//        $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
//        ->setValues($employeesModel)
//        ->setSelect($employeesSelect)
//        ->setWidth(400)
//        ->setAllowBlank(false);
        
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
        
        $s = $m1->select()->whereEquals('id', $row->positionId);
        $prow = $m1->getRow($s);
        $row->positionName = $prow->value;
        
        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        
        $row->employeeName = (string)$prow;
        
        $flightRow = $flightsModel->getRow($flightsSelect);

        if ($this->isContain(trlKwf('KWS'), $row->positionName))
        {
            $flightRow->firstPilotName = (string)$prow;
        }
        else if ($this->isContain(trlKwf('Second pilot'), $row->positionName))
        {
            $flightRow->secondPilotName = (string)$prow;
        }
        else if ($this->isContain(trlKwf('Technic'), $row->positionName))
        {
            $flightRow->technicName = (string)$prow;
        }
        else if ($this->isContain(trlKwf('Resquer'), $row->positionName))
        {
            $flightRow->resquerName = (string)$prow;
        }
        else if (($this->isContain(trlKwf('Instructor'), $row->positionName)) ||
                 ($this->isContain(trlKwf('Checker'), $row->positionName)))
        {
            $flightRow->checkPilotName = (string)$prow;
        }
        
        $flightRow->save();
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
}

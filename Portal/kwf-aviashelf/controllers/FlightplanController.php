<?php
class FlightplanController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add', 'xls');
    protected $_modelName = 'Flightplans';
    protected $_buttons = array ('xls');

    protected function _initFields()
    {
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'admin')
        {
            $this->_form->add(new Kwf_Form_Field_DateField('planDate', trlKwf('Date')))->setAllowBlank(false);

            $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
            $employeesSelect = $employeesModel->select()->whereEquals('visible', '1');
            
            $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Responsible')))
            ->setValues($employeesModel)
            ->setSelect($employeesSelect)
            ->setWidth(400)
            ->setShowNoSelection(true)
            ->setAllowBlank(true);
            
            $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Additional info')))
            ->setHeight(70)
            ->setWidth(400);            
        }
        else
        {
            $this->_form->add(new Kwf_Form_Field_ShowField('planDate', trlKwf('Date')))
            ->setWidth(400);

            $this->_form->add(new Kwf_Form_Field_ShowField('employeeName', trlKwf('Responsible')))
            ->setWidth(400);
            
            $this->_form->add(new Kwf_Form_Field_ShowField('comment', trlKwf('Additional info')))
            ->setHeight(70)
            ->setWidth(400);            
        }
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
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();

        $reporter = new Reporter ();
        $reporter->exportFlightPlanToXls($xls, $firstSheet, $row);
    }
}

<?php
class FlightplanController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Flightplans';

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
}

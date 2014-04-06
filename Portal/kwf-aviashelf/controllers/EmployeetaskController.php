<?php
class EmployeetaskController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Tasks';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;
    protected $_buttons = array('save');

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_DateField('startDate', trlKwf('Start Date')));
        $this->_form->add(new Kwf_Form_Field_DateField('endDate', trlKwf('End Date')));
        
        $this->_form->add(new Kwf_Form_Field_TextArea('description', trlKwf('Description')))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_File('Picture', trlKwf('File')))
        ->setShowPreview(true)
        ->setAllowOnlyImages(false);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('status', trlKwf('Done')));
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row) {
                
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('id', $this->_getParam('employeeId'));
        
        $employee = $employeesModel->getRow($employeesSelect);
        $userId = -1;
        
        if ($employee != NULL) {
            $userId = $employee->userId;
        }

        $row->userId = $userId;
        $row->status = 0;
    }
}

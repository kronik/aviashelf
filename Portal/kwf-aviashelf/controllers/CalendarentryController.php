<?php
class CalendarentryController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Calendar';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->where(new Kwf_Model_Select_Expr_Sql('visible = 1 AND groupType = 1'))->order('lastname');

        $statusModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $statusSelect = $statusModel->select()->whereEquals('name', 'Состояния сотрудника')->order('value');

        $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_DateField('startDate', trlKwf('Start Date')))->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_DateField('endDate', trlKwf('End Date')))->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('statusId', trlKwf('Type')))
        ->setValues($statusModel)
        ->setSelect($statusSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_TextArea('description', trlKwf('Description')))
        ->setHeight(70)
        ->setWidth(400);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m3 = Kwf_Model_Abstract::getInstance('Employees');
        
        $s = $m3->select()->whereEquals('id', $row->employeeId);
        $prow = $m3->getRow($s);
        
        $row->employeeName = (string)$prow;

        $s = $m1->select()->whereEquals('id', $row->statusId);
        $prow = $m1->getRow($s);
        
        if ($prow != NULL) {
            $row->statusName = $prow->value;
        } else {
            $row->statusName = '';
        }
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

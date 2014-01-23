<?php

require_once 'FormEx.php';

class EmployeeworksentryController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'EmployeeWorks';
    protected $_buttons = array ('');

    protected function _initFields()
    {
        $tabs = $this->_form->add(new Kwf_Form_Container_Tabs());
        $tabs->setBorder(true);
        $tabs->setActiveTab(0);

        $tab = $tabs->add();
        $tab->setTitle('Наработка');
        $tab->setLabelAlign('top');

        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->where(new Kwf_Model_Select_Expr_Sql('visible = 1 AND groupType = 1'))->order('lastname');
        
        $tab->fields->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
                
        $tab->fields->add(new Kwf_Form_Field_DateField('workDate', 'День'))
        ->setWidth(150)
        ->setAllowBlank(false);

        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Состояния сотрудника');
        
        $tab->fields->add(new Kwf_Form_Field_Select('typeId', 'Где находился'))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(150)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime1', 'Фактическая наработка'))
        ->setWidth(150)
        ->setIncrement(1);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime2', 'Фактический налет'))
        ->setWidth(150)
        ->setIncrement(1);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime3', 'Налет ночью'))
        ->setWidth(150)
        ->setIncrement(1);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime4', 'Наработка ночью'))
        ->setWidth(150)
        ->setIncrement(1);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime5', 'Другая наработка'))
        ->setWidth(150)
        ->setIncrement(1);
        
        $tab->fields->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);        
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row) {
        
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        
        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        $row->typeName = $prow->value;

        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        
        $row->employeeName = (string)$prow;
    }
    
    protected function _beforeDelete(Kwf_Model_Row_Interface $row) {
        $db = Zend_Registry::get('db');
        
        $db->delete('employeeWork', array('employeeWorkId = ?' => $row->id));
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->workId = $this->_getParam('workId');        
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

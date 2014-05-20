<?php

require_once 'FormEx.php';

class EmployeeworksentryController extends Kwf_Controller_Action_Auto_Form_Ex
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
        ->setWidth(300)
        ->setAllowBlank(false);
                
        $tab->fields->add(new Kwf_Form_Field_DateField('workDate', 'День'))
        ->setWidth(300)
        ->setAllowBlank(false);

        $typeModel = Kwf_Model_Abstract::getInstance('EmployeeWorkTypes');
        $typeSelect = $typeModel->select()->order('pos');
        $types = $typeModel->getRows($typeSelect);
        
        $records = array();
        
        foreach ($types as $type) {
            $records[$type->value] = $type->value;
        }
        
        $tab->fields->add(new Kwf_Form_Field_Select('typeName', 'Код'))
        ->setValues($records)
        ->setWidth(300)
        ->setAllowBlank(false);

        $tab->fields->add(new Kwf_Form_Field_Select('subTypeName', 'Дополнительный код'))
        ->setValues($records)
        ->setWidth(300)
        ->setAllowBlank(true);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime1', 'Отработано'))
        ->setWidth(300)
        ->setIncrement(1);
        
//        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime2', 'Фактический налет'))
//        ->setWidth(150)
//        ->setIncrement(1);
//        
//        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime3', 'Налет ночью'))
//        ->setWidth(150)
//        ->setIncrement(1);
//        
//        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime4', 'Наработка ночью'))
//        ->setWidth(150)
//        ->setIncrement(1);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime5', 'Другая наработка'))
        ->setWidth(300)
        ->setIncrement(1);


        $tab->fields->add(new Kwf_Form_Field_Select('timePerDay', 'Норма (ч)'))
        ->setValues(array('00:00:00' => '00:00',
                          '06:00:00' => '06:00',
                          '06:12:00' => '06:12',
                          '06:15:00' => '06:15',
                          '07:00:00' => '07:00',
                          '07:12:00' => '07:12',
                          '07:15:00' => '07:15',
                          '08:00:00' => '08:00'))
        ->setWidth(300)
        ->setDefaultValue('00:00:00')
        ->setAllowBlank(false);

        $tab->fields->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(300);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row) {
        
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        $typeModel = Kwf_Model_Abstract::getInstance('EmployeeWorkTypes');

        $s = $typeModel->select()->whereEquals('value', $row->typeName);
        $prow = $typeModel->getRow($s);
        
        $needTime = false;
        
        if ($prow != NULL) {
            $row->typeName = $prow->value;
            
            $row->timeInMinutes = 0;
            
            $needTime = $prow->needTime;
        }
        
        if ($row->subTypeName != NULL) {
            $s = $typeModel->select()->whereEquals('value', $row->subTypeName);
            $prow = $typeModel->getRow($s);

            if ($prow != NULL) {
                $row->subTypeName = $prow->value;
            }
        }

        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        
        $row->employeeName = (string)$prow;
        
        $totalMinutes = $this->minutesFromDateTime($row->timePerDay);
        $totalMinutes += $this->minutesFromDateTime($row->workTime1);
        $totalMinutes += $this->minutesFromDateTime($row->workTime2);
        $totalMinutes += $this->minutesFromDateTime($row->workTime3);
        $totalMinutes += $this->minutesFromDateTime($row->workTime4);
        $totalMinutes += $this->minutesFromDateTime($row->workTime5);
        
        $validateTime = true;
        
        if ((0 === mb_strpos($row->typeName, 'В')) && ($row->typeName != 'В')) {
            $validateTime = false;
        }
        
        if (($needTime == false) && ($totalMinutes > 0) && $validateTime) {
            throw new Kwf_Exception_Client('Указано время для типа <' . $row->typeName . '>');
        }

        if (($needTime == true) && ($totalMinutes == 0) && $validateTime) {
            throw new Kwf_Exception_Client('Не указано время для типа <' . $row->typeName . '>');
        }
        
        $row->autogenerated = false;
    }
    
    public function minutesFromDateTime($date) {

        if ($date == NULL || $date == '') {
            return 0;
        }
        
        $timeParts = explode(":", $date);
        return ((int)$timeParts[0] * 60) + (int)$timeParts[1];
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        if ($this->_getParam('workId') != NULL) {
            $row->workId = $this->_getParam('workId');
        }
        
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

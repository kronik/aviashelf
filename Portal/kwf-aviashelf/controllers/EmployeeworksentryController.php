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
        ->setWidth(300)
        ->setAllowBlank(false);
                
        $tab->fields->add(new Kwf_Form_Field_DateField('workDate', 'День'))
        ->setWidth(300)
        ->setAllowBlank(false);

        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Состояния сотрудника')->order('pos');
        
        $tab->fields->add(new Kwf_Form_Field_Select('typeId', 'Тип наработки'))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(300)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TimeField('workTime1', 'Фактическая наработка'))
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
        
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        
        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        $row->typeName = $prow->value;

        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        
        $row->employeeName = (string)$prow;
        
        $row->timeInMinutes = 0;
        
        $needTime = $this->needTimeForStatus($row->typeName);
        
        $totalMinutes = $this->minutesFromDateTime($row->timePerDay);
        $totalMinutes += $this->minutesFromDateTime($row->workTime1);
        $totalMinutes += $this->minutesFromDateTime($row->workTime2);
        $totalMinutes += $this->minutesFromDateTime($row->workTime3);
        $totalMinutes += $this->minutesFromDateTime($row->workTime4);
        $totalMinutes += $this->minutesFromDateTime($row->workTime5);
        
        if (($needTime == false) && ($totalMinutes > 0)) {
            throw new Kwf_Exception_Client('Указано время для типа <' . $row->typeName . '>');
        }

        if (($needTime == true) && ($totalMinutes == 0)) {
            throw new Kwf_Exception_Client('Не указано время для типа <' . $row->typeName . '>');
        }
    }
            
    public function minutesFromDateTime($date) {

        if ($date == NULL || $date == '') {
            return 0;
        }
        
        $timeParts = explode(":", $date);
        return ((int)$timeParts[0] * 60) + (int)$timeParts[1];
    }
    
    public function needTimeForStatus ($statusName) {
        switch (mb_strtoupper($statusName)) {
            case 'Я':
            case 'Н':
            case 'РВ':
            case 'С':
            case 'ВМ':
            case 'К':
            case 'ПК':
            case 'ПМ':
            case 'КСЭ':
            case 'У':
            case 'УВ':
            case 'ЛЧ':
            case 'НС':
            case 'НЗ':
                return true;
                break;
                
            default:
                return false;
        }
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

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
        ->setAllowBlank(true);

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
        
        if ($row->employeeId != NULL && $row->employeeId != 0) {
            $s = $m3->select()->whereEquals('id', $row->employeeId);
            $prow = $m3->getRow($s);
        
            $row->employeeName = (string)$prow;
        } else {
            $row->employeeId = 0;
            $row->employeeName = '';
        }

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
    
    protected function _afterInsert(Kwf_Model_Row_Interface $row) {
//        $this->updateWorkEntries($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _afterSave(Kwf_Model_Row_Interface $row) {
//        $this->updateWorkEntries($row);
    }
    
    protected function updateWorkEntries($calendarRecord) {
        
        ini_set('memory_limit', "768M");
        
        $whereStmt = '(workDate >= \'' . $calendarRecord->startDate . '\' AND workDate <= \'' . $calendarRecord->endDate .'\')';
        
        if ($calendarRecord->employeeId != NULL) {
            $whereStmt = $whereStmt . ' AND employeeId = ' . $calendarRecord->employeeId;
        }
        
        $employeeworksModel = Kwf_Model_Abstract::getInstance('EmployeeWorks');
        $employeeworksSelect = $employeeworksModel->select()->where(new Kwf_Model_Select_Expr_Sql($whereStmt));
        $employeeworks = $employeeworksModel->getRows($employeeworksSelect);
        
        if (count($employeeworks) == 0) {
            return;
        }
        
        $statusModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $statusSelect = $statusModel->select()->whereEquals('name', 'Состояния сотрудника')->whereEquals('value', 'Я');
        $workStatus = $statusModel->getRow($statusSelect);
        
        if ($workStatus == NULL) {
            throw new Kwf_Exception_Client('Нет состояния сотрудника <Явка>.');
        }
        
        $statusSelect = $statusModel->select()->whereEquals('name', 'Состояния сотрудника')->whereEquals('value', 'В');
        $holidayStatus = $statusModel->getRow($statusSelect);
        
        if ($holidayStatus == NULL) {
            throw new Kwf_Exception_Client('Нет состояния сотрудника <Выходной>.');
        }
                
        $startDate = new DateTime ($calendarRecord->startDate);
        $endDate = new DateTime ($calendarRecord->endDate);
        
        $helper = new Helper();
        
        foreach ($employeeworks as $employeework) {
            
            $startDate = new DateTime ($calendarRecord->startDate);
            
            while ($startDate <= $endDate) {
                
                $employeework->typeId = $calendarRecord->statusId;
                $employeework->typeName = $calendarRecord->statusName;
                
                $isWorkingDay = $helper->isWorkingDay($startDate);
                $isNextDayHoliday = $helper->isNextDayHoliday($startDate);
                
                $timeStr = $helper->timeForStatus($calendarRecord->statusName);
                
                if ($isWorkingDay && $isNextDayHoliday && ($timeStr != '00:00')) {
                    $employeework->timePerDay = '06:12';
                } else if ($isWorkingDay) {
                    $employeework->timePerDay = $timeStr;
                } else {
                    $employeework->timePerDay = '00:00';
                }
                
                $employeework->save();
                
                $startDate->add( new DateInterval('P1D') );
            }
        }
    }
}

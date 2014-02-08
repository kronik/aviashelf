<?php
    
require_once 'GridEx.php';

class EmployeeworksController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'EmployeeWorks';
    protected $_defaultOrder = array('field' => 'workDate', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'employeeName');
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = NULL;
    protected $_paging = 5000;
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');

        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_queryFields = array('employeeName', 'typeName');

        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'power')
        {
            if ($users->getAuthedUserRole() == 'power') {
                unset($this->_buttons ['delete']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                           'controllerUrl' => '/employeeworksentry',
                                           'width' => 450,
                                           'height' => 620
                                       );
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee'), 170))->setProperty('summaryType', 'totalTimeDescription');
        $this->_columns->add(new Kwf_Grid_Column('workDate', 'День', 50))->setRenderer('dateShrink');
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type'), 50))->setRenderer('typeHighlight');
        $this->_columns->add(new Kwf_Grid_Column('workTime1', 'Фактическая наработка', 130))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime1');
        $this->_columns->add(new Kwf_Grid_Column('workTime2', 'Фактический налет', 120))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime2');
        $this->_columns->add(new Kwf_Grid_Column('workTime3', 'Налет ночью', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime3');
        $this->_columns->add(new Kwf_Grid_Column('workTime4', 'Наработка ночью', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime4');
        $this->_columns->add(new Kwf_Grid_Column('workTime5', 'Другая наработка', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime5');
        $this->_columns->add(new Kwf_Grid_Column('timePerDay', 'Норма (ч)', 100));//->setRenderer('daysForTime')->setProperty('summaryType', 'totalDays');
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comments')))->setWidth(500);
    }
        
    protected function _getWhere()
    {
        $this->updateWorkEntries($this->_getParam('workId'));
        
        $ret = parent::_getWhere();
        $ret['workId = ?'] = $this->_getParam('workId');
        return $ret;
    }
    
    protected function updateWorkEntries($workId) {
        $employeeworksModel = Kwf_Model_Abstract::getInstance('EmployeeWorks');
        $employeeworksSelect = $employeeworksModel->select()->whereEquals('workId', $workId);
        $employeework = $employeeworksModel->getRows($employeeworksSelect);

        if (count($employeework) > 0) {
            return;
        }

        $worksModel = Kwf_Model_Abstract::getInstance('Works');
        $worksSelect = $worksModel->select()->whereEquals('id', $workId);
        $work = $worksModel->getRow($worksSelect);

        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->where(new Kwf_Model_Select_Expr_Sql('visible = 1 AND groupType = 1'))->order('lastname');
        $employees = $employeesModel->getRows($employeesSelect);
        
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

        $startDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);
        $endDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);
        $endDate->add( new DateInterval('P1M') );
        
        $calendarModel = Kwf_Model_Abstract::getInstance('Calendar');
        $calendarSelect = $calendarModel->select()->where(new Kwf_Model_Select_Expr_Sql('startDate <= ' . $endDate->format('Y-m-d') . ' OR endDate >= ' . $startDate->format('Y-m-d')));
        $calendar = $calendarModel->getRows($calendarSelect);
        
        $helper = new Helper();
        
        foreach ($employees as $employee) {

            $startDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);

            while ($startDate < $endDate) {
                
                $calendarRecord = $this->findCalendarRecordByEmployeeId($employee->id, $calendar, $startDate);
                
                $newRow = $employeeworksModel->createRow();
                
                $newRow->workId = $workId;

                $newRow->employeeId = $employee->id;
                $newRow->employeeName = (string)$employee;
                
                $newRow->workDate = $startDate->format('Y-m-d');
                
                $newRow->workTime1 = '00:00';
                $newRow->workTime2 = '00:00';
                $newRow->workTime3 = '00:00';
                $newRow->workTime4 = '00:00';
                $newRow->workTime5 = '00:00';
                
                $isWorkingDay = $helper->isWorkingDay($startDate);
                $isNextDayHoliday = $helper->isNextDayHoliday($startDate);
                
                if ($calendarRecord == NULL) {
                    if ($isWorkingDay) {
                        $newRow->typeId = $workStatus->id;
                        $newRow->typeName = $workStatus->value;
                    } else {
                        $newRow->typeId = $holidayStatus->id;
                        $newRow->typeName = $holidayStatus->value;
                    }
                } else {
                    $newRow->typeId = $calendarRecord->statusId;
                    $newRow->typeName = $calendarRecord->statusName;
                }
                
                if ($isWorkingDay && $isNextDayHoliday) {
                    $newRow->timePerDay = '06:12';
                } else if ($isWorkingDay) {
                    $newRow->timePerDay = '07:12';
                } else {
                    $newRow->timePerDay = '00:00';
                }

                $newRow->save();
                
                $startDate->add( new DateInterval('P1D') );
            }
        }
    }
    
    protected function findCalendarRecordByEmployeeId ($employeeId, $calendarRecords, $workDate) {
        foreach ($calendarRecords as $calendarRecord) {
            if (($calendarRecord->employeeId == $employeeId) || ($calendarRecord->employeeId == 0)) {
                
                $startDate = new DateTime($calendarRecord->startDate);
                $endDate = new DateTime($calendarRecord->endDate);
                $endDate->add( new DateInterval('P1D') );
        
                if (($startDate <= $workDate) && ($workDate <= $endDate)) {
                    return $calendarRecord;
                }
            }
        }
        
        return NULL;
    }
}

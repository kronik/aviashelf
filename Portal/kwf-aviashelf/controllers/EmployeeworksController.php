<?php
    
require_once 'GridEx.php';

class EmployeeworksController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'EmployeeWorks';
    protected $_defaultOrder = array('field' => 'workDate', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'employeeName');
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = NULL;
    protected $_paging = 3000;
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');

        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_queryFields = array('employeeName', 'typeName');

        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'power') {
            if ($users->getAuthedUserRole() == 'power') {
                unset($this->_buttons ['delete']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                           'controllerUrl' => '/employeeworksentry',
                                           'width' => 350,
                                           'height' => 560,
                                           'type' => 'WindowFormEx'
                                       );
        } else {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee'), 130))->setProperty('summaryType', 'totalTimeDescription');
        $this->_columns->add(new Kwf_Grid_Column('workDate', 'День', 50))->setRenderer('dateShrink');
        $this->_columns->add(new Kwf_Grid_Column('typeName', 'Код', 80))->setRenderer('typeHighlight');
        $this->_columns->add(new Kwf_Grid_Column('subTypeName', 'Код РВ', 50))->setRenderer('typeHighlight');
        $this->_columns->add(new Kwf_Grid_Column('workTime1', 'Отработано', 130))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime1');
        $this->_columns->add(new Kwf_Grid_Column('workTime2', 'Фактический налет', 120))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime2');
        $this->_columns->add(new Kwf_Grid_Column('workTime3', 'Налет ночью', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime3');
        $this->_columns->add(new Kwf_Grid_Column('workTime4', 'Ночное время', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime4');
        $this->_columns->add(new Kwf_Grid_Column('workTime5', 'Другая наработка', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime5');
        $this->_columns->add(new Kwf_Grid_Column('holidayWork', 'Выход. и празд.', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTimeHoliday');
        $this->_columns->add(new Kwf_Grid_Column('timePerDay', 'Норма (ч)', 70))->setRenderer('formatOvertime')->setProperty('summaryType', 'totalDays');
        
        $this->_columns->add(new Kwf_Grid_Column('totalOvertimeMinutes', 'Переработка (ч)', 100))
        ->setRenderer('totalOvertimeMinutesColorer')
        ->setProperty('summaryType', 'totalOvertimeMinutesSum');
        
        $this->_columns->add(new Kwf_Grid_Column('totalOvertimeDays', 'Переработка (д)', 100))
        ->setRenderer('totalOvertimeDaysColorer')
        ->setProperty('summaryType', 'totalOvertimeDaysSum');
        
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comments')))->setWidth(500);
        $this->_columns->add(new Kwf_Grid_Column('workMonthYear', 'Дата'))->setWidth(80);
    }
    
//    public function jsonDataAction()
//    {
//        $worksModel = Kwf_Model_Abstract::getInstance('Works');
//        $worksSelect = $worksModel->select()->whereEquals('id', $this->_getParam('workId'));
//        $work = $worksModel->getRow($worksSelect);
//        
//        $endDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);
//        $endDate->add( new DateInterval('P1M') );
//        $endDate->sub( new DateInterval('P1D') );
//        
//        $this->_paging = $endDate->format('d') * 10;
//        
//        parent::jsonDataAction();
//    }
    
    protected function _getWhere()
    {        
        $ret = parent::_getWhere();
        
        if ($this->_getParam('workId') != NULL) {
            $ret['workId = ?'] = $this->_getParam('workId');
            $this->_grouping = array('groupField' => 'employeeName');
        } else {
            
            $todayDate = new DateTime('NOW');
            $startDate = DateTime::createFromFormat('m-d-Y', $todayDate->format('m') . '-01-' . $todayDate->format('Y'));
            $startDate->sub( new DateInterval('P1D') );
            $startDate->sub( new DateInterval('P6M') );
            $startDate->sub( new DateInterval('P1D') );
            
            $ret['employeeId = ?'] = $this->_getParam('employeeId');
            $ret['workDate > ?'] = $startDate->format('Y-m-d');
            
            $this->_grouping = array('groupField' => 'workMonthYear');
            unset($this->_buttons ['delete']);
            unset($this->_buttons ['add']);
        }
        return $ret;
    }
}

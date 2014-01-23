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
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type'), 50));
        $this->_columns->add(new Kwf_Grid_Column('workTime1', 'Фактическая наработка', 130))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime1');
        $this->_columns->add(new Kwf_Grid_Column('workTime2', 'Фактический налет', 120))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime2');
        $this->_columns->add(new Kwf_Grid_Column('workTime3', 'Налет ночью', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime3');
        $this->_columns->add(new Kwf_Grid_Column('workTime4', 'Наработка ночью', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime4');
        $this->_columns->add(new Kwf_Grid_Column('workTime5', 'Другая наработка', 100))->setRenderer('dateClearEmpty')->setProperty('summaryType', 'totalTime5');
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
        $employeeworks = $employeeworksModel->getRows($employeeworksSelect);

        if (count($employeeworks) > 0) {
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
        $status = $statusModel->getRow($statusSelect);

        $startDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);
        $endDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);
        $endDate->add( new DateInterval('P1M') );
        
        foreach ($employees as $employee) {

            $startDate = DateTime::createFromFormat('m-d-Y', $work->month . '-01-' . $work->year);

            while ($startDate < $endDate) {
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
                
                $newRow->typeId = $status->id;
                $newRow->typeName = $status->value;

                $newRow->save();
                
                $startDate->add( new DateInterval('P1D') );
            }
        }
    }
}

<?php
class CalendarController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Calendar';
    protected $_defaultOrder = array('field' => 'startDate', 'direction' => 'DESC');
    protected $_paging = 1000;
    protected $_buttons = array('add', 'delete');
    protected $_grouping = array('groupField' => 'employeeName');

    public function indexAction()
    {
        $this->view->ext('Calendar');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
            unset($this->_buttons ['add']);
        }
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_queryFields = array('employeeName', 'statusName');

        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee'), 100));
        $this->_columns->add(new Kwf_Grid_Column_Date('startDate', trlKwf('Start Date'), 80));
        $this->_columns->add(new Kwf_Grid_Column_Date('endDate', trlKwf('End Date'), 90));
        $this->_columns->add(new Kwf_Grid_Column('statusName', trlKwf('Type'), 50));
    }
    
    protected function _getWhere() {
        $this->updateCalendar();
        
        $ret = parent::_getWhere();
        return $ret;
    }
    
    protected function updateCalendar() {
        
//        $today = new DateTime('NOW');
//        
//        $calendarModel = Kwf_Model_Abstract::getInstance('Calendar');
//        $calendarSelect = $calendarModel->select()->whereEquals('month', $today->format('m'))->whereEquals('year', $today->format('m'));
//        $calendar = $calendarModel->getRows($calendarSelect);
//        
//        if (count($calendar) > 0) {
//            return;
//        }
        
    }

    
//    protected function _getWhere()
//    {
//        $users = Kwf_Registry::get('userModel');
//        
//        $ret = parent::_getWhere();
//        
//        $ret['status = ?'] = 0;
//        $ret['userId = ?'] = $users->getAuthedUserId();
//        return $ret;
//    }
}

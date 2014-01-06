<?php
//require_once 'GridEx.php';
class MyflightsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flights';
    protected $_defaultOrder = array('field' => 'flightStartDate', 'direction' => 'DESC');
//    protected $_grouping = array('groupField' => 'flightStartDate');
    protected $_buttons = array('xls');
    protected $_editDialog = NULL;
    protected $_paging = 50;

    public function indexAction()
    {
        //parent::indexAction();
        $this->view->ext('Myflights');
    }
    
    protected function _initColumns()
    {
        //parent::_initColumns();
        $users = Kwf_Registry::get('userModel');

        $this->_filters = array('text' => array('type' => 'TextField'));
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' ||
            $users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
            
            if ($users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
                
                unset($this->_buttons ['delete']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                           'controllerUrl' => '/myflight',
                                           'width' => 550,
                                           'height' => 410,
                                           'type' => 'WindowFormEx'
                                       );
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('flightStartDate', trlKwf('Date'), 80));
        $this->_columns->add(new Kwf_Grid_Column('flightStartTime', trlKwf('Time'), 50))->setRenderer('flightTimeCorrect');
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number'), 70));
        $this->_columns->add(new Kwf_Grid_Column('requestNumber', trlKwf('Task number'), 70));
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('Bort'), 70));
        $this->_columns->add(new Kwf_Grid_Column('routeName', trlKwf('Route'), 150));
        $this->_columns->add(new Kwf_Grid_Column('firstPilotName', trlKwf('KWS'), 100));
//        $this->_columns->add(new Kwf_Grid_Column('checkPilotName', trlKwf('Instructor (check)'), 200));
//        $this->_columns->add(new Kwf_Grid_Column('secondPilotName', trlKwf('Second pilot'), 100));
//        $this->_columns->add(new Kwf_Grid_Column('technicName', trlKwf('Technic'), 100));
//        $this->_columns->add(new Kwf_Grid_Column('resquerName', trlKwf('Resquer'), 100));
        $this->_columns->add(new Kwf_Grid_Column('objectiveName', trlKwf('Objective'), 100));
        $this->_columns->add(new Kwf_Grid_Column('subCompanyName', trlKwf('Customer'), 100));
        $this->_columns->add(new Kwf_Grid_Column('comments', trlKwf('Comments')))->setWidth(500);
    }
    
    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        
        $users = Kwf_Registry::get('userModel');
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('userId', $users->getAuthedUserId());
        
        $employee = $employeesModel->getRow($employeesSelect);
        $employeeId = -1;
        
        if ($employee != NULL)
        {
            $employeeId = $employee->id;
        }
        
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Sql("employeeId = " . $employeeId . " AND Hidden = 0"));
        $ret->where(new Kwf_Model_Select_Expr_Child_Contains('Flightgroups', $s));//->order('flightStartTime');
        
        return $ret;
    }

}

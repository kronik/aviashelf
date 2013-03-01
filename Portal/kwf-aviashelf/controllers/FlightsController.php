<?php
class FlightsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flights';
    protected $_defaultOrder = array('field' => 'flightStartTime', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'subCompanyName');
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = NULL;

    public function indexAction()
    {
        $this->view->ext('Flights');
    }
    
    protected function _initColumns()
    {
        $users = Kwf_Registry::get('userModel');

        $this->_filters = array('text' => array('type' => 'TextField'));
        
        if ($users->getAuthedUserRole() == 'admin')
        {
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                           'controllerUrl' => '/flight',
                                           'width' => 550,
                                           'height' => 390
                                       );            
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('flightStartTime', trlKwf('Time'), 50))->setRenderer('flightTimeCorrect');
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number'), 70));
        $this->_columns->add(new Kwf_Grid_Column('requestNumber', trlKwf('Task number'), 70));
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('Bort'), 70));
        $this->_columns->add(new Kwf_Grid_Column('routeName', trlKwf('Route'), 150));
        $this->_columns->add(new Kwf_Grid_Column('firstPilotName', trlKwf('KWS'), 100));
        $this->_columns->add(new Kwf_Grid_Column('checkPilotName', trlKwf('Instructor (check)'), 200));
        $this->_columns->add(new Kwf_Grid_Column('secondPilotName', trlKwf('Second pilot'), 100));
        $this->_columns->add(new Kwf_Grid_Column('technicName', trlKwf('Technic'), 100));
        $this->_columns->add(new Kwf_Grid_Column('resquerName', trlKwf('Resquer'), 100));
        $this->_columns->add(new Kwf_Grid_Column('objectiveName', trlKwf('Objective'), 100));
        $this->_columns->add(new Kwf_Grid_Column('subCompanyName', trlKwf('Customer'), 100));
        $this->_columns->add(new Kwf_Grid_Column('comments', trlKwf('Comments')))->setWidth(500);
    }
    
//    protected function _fillTheXlsFile($xls, $firstSheet)
//    {
//        $flightPlansModel = Kwf_Model_Abstract::getInstance('Flightplans');
//        $flightPlansSelect = $flightPlansModel->select()->whereEquals('id', $this->_getParam('planId'));
//        $row = $flightPlansModel->getRow($flightPlansSelect);
//        
//        $reporter = new Reporter ();
//        $reporter->exportFlightPlanToXls($xls, $firstSheet, $row);
//    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['planId = ?'] = $this->_getParam('planId');
        return $ret;
    }
}

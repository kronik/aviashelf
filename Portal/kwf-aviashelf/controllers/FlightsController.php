<?php
class FlightsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flights';
    protected $_defaultOrder = array('field' => 'flightStartTime', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'subCompanyName');
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
                                   'controllerUrl' => '/flight',
                                   'width' => 550,
                                   'height' => 390
                                   );

    public function indexAction()
    {
        $this->view->ext('Flights');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number'), 70));
        $this->_columns->add(new Kwf_Grid_Column('flightStartTime', trlKwf('Time'), 50))->setRenderer('flightTimeCorrect');
        $this->_columns->add(new Kwf_Grid_Column('subCompanyName', trlKwf('Customer'), 100));
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('Bort'), 70));
        $this->_columns->add(new Kwf_Grid_Column('firstPilotName', trlKwf('KWS'), 100));
        $this->_columns->add(new Kwf_Grid_Column('secondPilotName', trlKwf('Second pilot'), 100));
        $this->_columns->add(new Kwf_Grid_Column('technicName', trlKwf('Technic'), 100));
        $this->_columns->add(new Kwf_Grid_Column('resquerName', trlKwf('Resquer'), 100));
        $this->_columns->add(new Kwf_Grid_Column('checkPilotName', trlKwf('Instructor (check)'), 200));
        $this->_columns->add(new Kwf_Grid_Column('comments', trlKwf('Comments')))->setWidth(500);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['planId = ?'] = $this->_getParam('planId');
        return $ret;
    }
}

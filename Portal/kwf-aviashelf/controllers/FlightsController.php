<?php
class FlightsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Flights';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 30;
    protected $_buttons = array('add');
    protected $_editDialog = array(
                                   'controllerUrl' => '/flight',
                                   'width' => 550,
                                   'height' => 340
                                   );

    public function indexAction()
    {
        $this->view->ext('Flights');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number'), 100));
        $this->_columns->add(new Kwf_Grid_Column('subCompanyName', trlKwf('Subcompany'), 200));
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('Bort'), 100));
        #$this->_columns->add(new Kwf_Grid_Column_Date('flightStartDate', trlKwf('Date'), 80));
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['planId = ?'] = $this->_getParam('planId');
        return $ret;
    }
}

<?php
    require_once 'GridEx.php';

class SimpleflightplansController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightplans';
    protected $_defaultOrder = array('field' => 'planDate', 'direction' => 'DESC');
    protected $_paging = 400;
    protected $_buttons = array();
    protected $_editDialog = array(
                                     'controllerUrl' => '/simpleflightplan',
                                     'width' => 550,
                                     'height' => 260,
                                     'type' => 'WindowFormEx'
                                   );

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Simpleflightplans');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('planDate', trlKwf('Date'), 100));
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Responsible'), 200));
    }
}

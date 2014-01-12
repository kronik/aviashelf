<?php
    require_once 'GridEx.php';

class FlightplansController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightplans';
    protected $_defaultOrder = array('field' => 'planDate', 'direction' => 'DESC');
    protected $_paging = 400;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
                                     'controllerUrl' => '/flightplan',
                                     'width' => 550,
                                     'height' => 260,
                                     'type' => 'WindowFormEx'
                                   );

    public function indexAction()
    {
        parent::indexAction();
        
        if ($users->getAuthedUserRole() != 'admin') {
            unset($this->_buttons ['delete']);
        }

        $this->view->ext('Flightplans');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');

        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' || $users->getAuthedUserRole() == 'power')
        {
            
            if ($users->getAuthedUserRole() != 'admin') {
                unset($this->_buttons ['delete']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('planDate', trlKwf('Date'), 100));//->setRenderer('dateCorrect');
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Responsible'), 200));
    }
}

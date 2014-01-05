<?php
    require_once 'GridEx.php';

class TrainingsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Trainings';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_grouping = array('groupField' => 'type');
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = array(
        'controllerUrl' => '/training',
        'width' => 800,
        'height' => 610
    );

    public function indexAction()
    {
        parent::indexAction();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }
        
        $this->view->ext('Trainings');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number')))->setWidth(50);
        $this->_columns->add(new Kwf_Grid_Column('type', trlKwf('Type')))->setWidth(60);
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title')))->setWidth(300);
    }
}

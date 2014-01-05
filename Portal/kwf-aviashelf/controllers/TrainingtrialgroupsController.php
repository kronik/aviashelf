<?php
    require_once 'GridEx.php';

class TrainingtrialgroupsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'TrainingGroups';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 100;
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = array(
        'controllerUrl' => '/trainingtrialgroup',
        'width' => 650,
        'height' => 350
    );

    public function indexAction()
    {
        parent::indexAction();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->view->ext('Trainingtrialgroups');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number')))->setWidth(160);
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('startDate', trlKwf('Start Date')))->setWidth(80)->setRenderer('taskCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('endDate', trlKwf('End Date')))->setWidth(90)->setRenderer('taskCheckDate');
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        
        $ret['isTrial = ?'] = true;

        return $ret;
    }
}

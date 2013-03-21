<?php
class TraininggroupsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'TrainingGroups';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 100;
    protected $_grouping = array('groupField' => 'trainingName');
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = array(
        'controllerUrl' => '/traininggroup',
        'width' => 550,
        'height' => 260
    );

    public function indexAction()
    {
        $this->view->ext('Traininggroups');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        if ($this->_getParam('trainingId') == NULL)
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number')))->setWidth(60);
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('startDate', trlKwf('Start Date')))->setWidth(80)->setRenderer('taskCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('endDate', trlKwf('End Date')))->setWidth(90)->setRenderer('taskCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('questions', trlKwf('Questions in session')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('trainingName', trlKwf('Training')))->setWidth(300);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();

        if ($this->_getParam('trainingId') != NULL)
        {
            $ret['trainingId = ?'] = $this->_getParam('trainingId');
        }
        return $ret;
    }
}

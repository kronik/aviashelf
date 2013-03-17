<?php
class TrainingresultsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'TrainingResults';
    protected $_defaultOrder = array('field' => 'employeeName', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
        'controllerUrl' => '/trainingresult',
        'width' => 350,
        'height' => 130
    );

    public function indexAction()
    {
        $this->view->ext('Trainingresults');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(200)->setRenderer('checkScore');
        $this->_columns->add(new Kwf_Grid_Column('currentScore', trlKwf('Score')))->setWidth(100)->setRenderer('highlightScore');
        $this->_columns->add(new Kwf_Grid_Column('totalScore', trlKwf('Total Score')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('gradeName', trlKwf('Grade')))->setWidth(100);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['trainingGroupId = ?'] = $this->_getParam('groupId');
        return $ret;
    }
}

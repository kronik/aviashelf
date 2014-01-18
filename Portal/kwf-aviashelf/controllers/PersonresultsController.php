<?php
    require_once 'GridEx.php';

class PersonresultsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'PersonResults';
    protected $_defaultOrder = array('field' => 'trainingName', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = array(
        'controllerUrl' => '/personresult',
        'width' => 350,
        'height' => 160,
        'type' => 'WindowFormEx'
    );

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Personresults');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));
        
//        $this->_columns->add(new Kwf_Grid_Column('trainingGroupName', 'Группа'))->setWidth(100);//->setRenderer('checkScore');
        $this->_columns->add(new Kwf_Grid_Column('trainingName', 'Дисциплина'))->setWidth(250)->setRenderer('checkResultScore');
        $this->_columns->add(new Kwf_Grid_Column('currentScore', trlKwf('Score')))->setWidth(80)->setRenderer('highlightScore');
        $this->_columns->add(new Kwf_Grid_Column('totalScore', trlKwf('Total Score')))->setWidth(80);
        $this->_columns->add(new Kwf_Grid_Column('gradeName', trlKwf('Grade')))->setWidth(100);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['groupPersonId = ?'] = $this->_getParam('groupPersonId');
        return $ret;
    }
}

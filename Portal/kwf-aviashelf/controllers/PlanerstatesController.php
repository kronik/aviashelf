<?php
    require_once 'GridEx.php';
class PlanerstatesController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Planerstates';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'typeName'); //group by company
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = NULL;

    public function indexAction()
    {
        parent::indexAction();
        $this->view->ext('Planerstates');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        $users = Kwf_Registry::get('userModel');

        $this->_filters = array('text' => array('type' => 'TextField'));
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' || $users->getAuthedUserRole() == 'power')
        {
            if ($users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
                
                unset($this->_buttons ['delete']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/planerstate',
                                       'width' => 550,
                                       'height' => 450
                                       );
            
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_columns->add(new Kwf_Grid_Column('responsibleName', 'Техник ПДО', 200));
        $this->_columns->add(new Kwf_Grid_Column('planeName', trlKwf('Bort'), 70));
        $this->_columns->add(new Kwf_Grid_Column('landpointName', trlKwf('Base point'), 130));
        $this->_columns->add(new Kwf_Grid_Column('priority', trlKwf('Priority'), 70));
        $this->_columns->add(new Kwf_Grid_Column('statusName', trlKwf('Status'), 70))->setRenderer('planerStateColorer');
        $this->_columns->add(new Kwf_Grid_Column('statusDate', trlKwf('Date'), 70));
        $this->_columns->add(new Kwf_Grid_Column('reason', trlKwf('Failure Reason'), 150));
        $this->_columns->add(new Kwf_Grid_Column('expectedDate', trlKwf('Expected date'), 100));
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Customer'), 200));
    }
        
    protected function _getWhere()
    {
        $helper = new Helper();
        
        $helper->transferStatesFromPreviousPlan($this->_getParam('planId'));
        
        $ret = parent::_getWhere();
        $ret['planId = ?'] = $this->_getParam('planId');
        return $ret;
    }
}

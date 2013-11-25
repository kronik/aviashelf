<?php
    require_once 'GridEx.php';

class GrouppersonsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'GroupPersons';
    protected $_defaultOrder = array('field' => 'employeeName', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
        'controllerUrl' => '/groupperson',
        'width' => 350,
        'height' => 160,
        'type' => 'WindowFormEx'
    );

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Grouppersons');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(325);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['trainingGroupId = ?'] = $this->_getParam('groupId');
        return $ret;
    }
}

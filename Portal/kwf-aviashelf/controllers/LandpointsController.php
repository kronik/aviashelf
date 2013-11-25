<?php
    require_once 'GridEx.php';

class LandpointsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Landpoints';
    protected $_defaultOrder = 'listPosition';
    protected $_buttons = array('add', 'delete');

    public function indexAction()
    {
        parent::indexAction();
        $this->view->ext('Landpoints');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Title'), 100));
        $this->_columns->add(new Kwf_Grid_Column('responsibleName', 'Владелец'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('phone', trlKwf('Phone')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('listPosition', '№ в списках'))->setWidth(100);
    }
}

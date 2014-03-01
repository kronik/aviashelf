<?php
    require_once 'GridEx.php';

class LinkdataController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Linkdata';
    protected $_defaultOrder = 'value';
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
        'controllerUrl' => '/linkdataentry',
        'width' => 600,
        'height' => 230
    );

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('pos', '№ в списках'))
        ->setWidth(80);
        $this->_columns->add(new Kwf_Grid_Column('value', trlKwf('Value')))
        ->setWidth(300);
        $this->_columns->add(new Kwf_Grid_Column('desc', trlKwf('Description')))
            ->setRenderer('nl2Br')
            ->setWidth(700);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['link_id = ?'] = $this->_getParam('link_id');
        return $ret;
    }
}

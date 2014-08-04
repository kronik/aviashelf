<?php
    require_once 'GridEx.php';

class FoldersController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Folders';
    protected $_defaultOrder = 'title';
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
                                   'controllerUrl' => '/folder',
                                   'width' => 550,
                                   'height' => 200
                                   );
    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Folders');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 400));
    }
}

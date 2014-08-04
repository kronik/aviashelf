<?php
    require_once 'GridEx.php';

class BooksController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Books';
    protected $_defaultOrder = 'title';
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
                                   'controllerUrl' => '/book',
                                   'width' => 550,
                                   'height' => 270
                                   );
    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Books');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 500));
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['folderId = ?'] = $this->_getParam('folderId');
        return $ret;
    }
}

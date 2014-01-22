<?php
    require_once 'GridEx.php';

class LinksController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Links';
    protected $_defaultOrder = 'name';
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
                                   'controllerUrl' => '/link',
                                   'width' => 450,
                                   'height' => 130
                                   );
    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Links');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Title'), 300));
        #->setEditor(new Kwf_Form_Field_TextField());
    }
}

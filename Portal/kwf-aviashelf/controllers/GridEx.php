<?php
abstract class Kwf_Controller_Action_Auto_Grid_Ex extends Kwf_Controller_Action_Auto_Grid {
    public function indexAction() {
        parent::indexAction();
    }
    
    protected function _initColumns() {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if (($users->getAuthedUserRole() == 'guest') ||
            ($users->getAuthedUserRole() == 'viewer') ||
            ($users->getAuthedUserRole() == 'user')) {
            unset($this->_buttons ['add']);
            unset($this->_buttons ['edit']);
        }
    }
}

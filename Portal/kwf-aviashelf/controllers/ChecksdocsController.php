<?php
class ChecksdocsController extends ChecksController
{    
    public function indexAction()
    {
        $this->view->ext('Checksdocs');
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['checkType = ?'] = 'doc';
        return $ret;
    }
}

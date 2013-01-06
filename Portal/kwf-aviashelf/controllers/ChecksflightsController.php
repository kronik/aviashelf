<?php
class ChecksflightsController extends ChecksController
{
    public function indexAction()
    {
        $this->view->ext('Checksflights');
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['checkType = ?'] = 'flight';
        return $ret;
    }
}

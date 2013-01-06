<?php
class CheckstrainingsController extends ChecksController
{
    public function indexAction()
    {
        $this->view->ext('Checkstrainings');
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['checkType = ?'] = 'training';
        return $ret;
    }
}

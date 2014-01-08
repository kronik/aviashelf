<?php
class SimplestaffgroupsController extends SimpleflightgroupsController
{    
    protected function _initColumns()
    {
        parent::_initColumns();
                
        $this->_columns->add(new Kwf_Grid_Column('positionName', trlKwf('Position')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['mainCrew = ?'] = FALSE;
        return $ret;
    }
}

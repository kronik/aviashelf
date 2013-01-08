<?php
class CheckController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Checks';
    protected $_permissions = array('save', 'add', 'delete');
    protected $_paging = 0;
    protected $_buttons = array('save');
    
    protected function updateDbViews()
    {
        $db = Zend_Registry::get('db');
        $docCheckSql = 'CREATE VIEW `documentсhecks` AS SELECT * FROM `documents` WHERE (Hidden = 0) ';
        $flightCheckSql = 'CREATE VIEW `flightсhecks` AS SELECT * FROM `documents` WHERE (Hidden = 0) ';
        $trainingCheckSql = 'CREATE VIEW `trainingсhecks` AS SELECT * FROM `documents` WHERE (Hidden = 0) ';

        $cheksModel = Kwf_Model_Abstract::getInstance('Checks');
        $cheksSelect = $cheksModel->select();
        
        $rows = $cheksModel->getRows($cheksSelect);
        
        foreach ($rows as $row)
        {
            if ($row->checkType == 'doc')
            {
                $docCheckSql = $docCheckSql . 'AND (typeId = ' . $row->typeId . ') ';
                
                if ($row->field == 'startDate')
                {
                    if ($row->value == NULL || $row->value == 0)
                    {
                        $docCheckSql = $docCheckSql . 'AND (' . $row->field . ' < CURDATE()) ';
                    }
                    else
                    {
                        $docCheckSql = $docCheckSql . 'AND (DATE_ADD(' . $row->field . ', INTERVAL ' . $row->value . ' DAY) > CURDATE()) ';
                    }
                }
                else if ($row->field == 'endDate')
                {
                    if ($row->value == NULL || $row->value == 0)
                    {
                        $docCheckSql = $docCheckSql . 'AND (' . $row->field . ' > CURDATE()) ';
                    }
                    else
                    {
                        $docCheckSql = $docCheckSql . 'AND (DATE_ADD(' . $row->field . ', INTERVAL ' . $row->value . ' DAY) < CURDATE()) ';
                    }
                }
            }
            else if ($row->checkType == 'flight')
            {
                if ($row->field == 'startDate')
                {
                }
            }
            else if ($row->checkType == 'training')
            {
            }
        }
        
        $docCheckSql = $docCheckSql . ';';
        
        #p($docCheckSql);

        $db->query('DROP VIEW IF EXISTS `documentсhecks`;');
        #$db->query('DROP VIEW IF EXISTS `flightсhecks`;');
        #$db->query('DROP VIEW IF EXISTS `trainingсhecks`;');
        
        $db->query($docCheckSql);
        #$db->query($flightCheckSql);
        #$db->query($trainingCheckSql);
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        #$this->updateDbViews();
    }
    
    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        #$this->updateDbViews();
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        
        if ($row->typeId != NULL)
        {
            $s = $m1->select()->whereEquals('id', $row->typeId);
            $prow = $m1->getRow($s);
            $row->typeName = $prow->value;
        }
        
        if ($row->subTypeId != NULL)
        {
            $s = $m1->select()->whereEquals('id', $row->subTypeId);
            $prow = $m1->getRow($s);
            $row->subtypeName = $prow->value;
        }
        
        return $row;
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['id = ?'] = $this->_getParam('id');
        return $ret;
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row = $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $row = $this->updateReferences($row);
    }
}

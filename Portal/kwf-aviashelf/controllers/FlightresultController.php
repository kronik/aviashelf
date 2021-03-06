<?php
    
require_once 'FormEx.php';

class FlightresultController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_modelName = 'Flightresults';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $wstypesModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypesSelect = $wstypesModel->select();
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Типы налета');
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Type')))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('planeId', trlKwf('WsType')))
        ->setValues($wstypesModel)
        ->setSelect($wstypesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_DateField('flightDate', trlKwf('Date')));
        
        $this->_form->add(new Kwf_Form_Field_TextField('flightTime', trlKwf('Time')))->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('flightsCount', 'Кол-во полетов'))->setWidth(400);

        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $users = Kwf_Registry::get('userModel');

        if ($users->getAuthedUserRole() == 'admin') {
            $this->_form->add(new Kwf_Form_Field_Checkbox('showInTotal', trlKwf('Show in total')));
        }
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('workOnHoliday', 'Код РВ'));
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    protected function updateWorkForResult ($result) {
        
        $helper = new Helper ();
        
        $resultDate = new DateTime ($result->flightDate);
        
        $worksModel = Kwf_Model_Abstract::getInstance('Works');
        $worksSelect = $worksModel->select()->whereEquals('month', $resultDate->format('m'))->whereEquals('year', $resultDate->format('Y'));
        $work = $worksModel->getRow($worksSelect);
        
        if ($work == NULL) {
            return;
        }
        
        $helper->updateWorkEntries($work->id, $result->ownerId, true);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Wstypes');
        $m3 = Kwf_Model_Abstract::getInstance('Employees');
        
        $s = $m3->select()->whereEquals('id', $this->_getParam('ownerId'));
        $prow = $m3->getRow($s);
        
        $row->ownerName = (string)$prow;
        
        $s = $m1->select()->whereEquals('id', $row->typeId);
        $prow = $m1->getRow($s);
        $row->typeName = $prow->value;
        
        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->Name;
        
        if ($this->isContain('Время работы', $row->typeName)) {
            $row->flightsCount = 0;
        }
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->ownerId = $this->_getParam('ownerId');
        
        if ($row->showInTotal == NULL) {
            $row->showInTotal = false;
        }

        $this->updateReferences($row);
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateWorkForResult($row);
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateWorkForResult($row);
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
}

<?php
class FlightaccessController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightaccesses';
    protected $_permissions = array('save', 'add', 'delete');
    protected $_paging = 0;

    protected function _initFields()
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = NULL;
        
        if ($this->_getParam('flightId') != NULL) {
            $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
            $flightsSelect = $flightsModel->select()->whereEquals('id', $this->_getParam('flightId'));
            $flight = $flightsModel->getRow($flightsSelect);
            
            $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
            $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $this->_getParam('flightId'))->whereEquals('mainCrew', TRUE);
            
            $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
            
            $memberIds = array();
            
            foreach ($flightMembers as $flightMember) {
                array_push($memberIds, $flightMember->employeeId);
            }
            
            if (count($memberIds) > 0) {
                $employeesSelect = $employeesModel->select()
                ->whereEquals('visible', 1)
                ->whereEquals('groupType', 1)
                ->where('id IN (?)', $memberIds)
                ->order('lastname');
            } else {
                $employeesSelect = $employeesModel->select()
                ->whereEquals('visible', 1)
                ->whereEquals('groupType', 1)
                ->order('lastname');
            }
                        
            $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
            ->setValues($employeesModel)
            ->setSelect($employeesSelect)
            ->setWidth(400)
            ->setAllowBlank(false);

        } else {
            
            if ($this->_getParam('employeeId') != NULL) {
                $employeesSelect = $employeesModel->select()
                ->whereEquals('visible', 1)
                ->whereEquals('groupType', 1)
                ->whereEquals('id', $this->_getParam('employeeId'))
                ->order('lastname');
            } else {
                $employeesSelect = $employeesModel->select()
                ->whereEquals('visible', 1)
                ->whereEquals('groupType', 1)
                ->order('lastname');
            }
        }
        
        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select();
        
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Метеоминимумы')->order('value');

        $accessTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $accessTypeSelect = $accessTypeModel->select()->whereEquals('name', 'Типы полетов')->order('value');
        
        $this->_form->add(new Kwf_Form_Field_Select('wsTypeId', trlKwf('WsType')))
        ->setValues($wstypeModel)
        ->setSelect($wstypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('accessId', 'Метеоминимум'))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(400)
        ->setAllowBlank(true);

        $this->_form->add(new Kwf_Form_Field_Select('accessTypeId', 'Тип проверки'))
        ->setValues($accessTypeModel)
        ->setSelect($accessTypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_Select('flightsCount', 'Кол-во полетов'))
        ->setValues(array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20'))
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('setsCount', 'Кол-во сп/подъемов'))
        ->setValues(array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20'))
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_DateField('accessDate', 'Дата допуска'))->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_DateField('accessEndDate', 'Дата окончания'))->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_TextField('docNumber', 'Номер приказа'))
        ->setWidth(400)
        ->setAllowBlank(true);

        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('finished', 'Выполнено'));
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');
        $m4 = Kwf_Model_Abstract::getInstance('Linkdata');
        $specModel = Kwf_Model_Abstract::getInstance('Specialities');

        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select()->whereEquals('id', $row->wsTypeId);
        $prow = $wstypeModel->getRow($wstypeSelect);
        $row->wsTypeName = $prow->Name;

        if ($row->accessId != NULL) {
            $s = $m1->select()->whereEquals('id', $row->accessId);
            $prow = $m1->getRow($s);
            $row->accessName = $prow->value;
        } else {
            $row->accessId = 0;
            $row->accessName = '';
        }
        
        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        $row->employeeName = (string)$prow;
        
        $specSelect = $specModel->select()->whereEquals('id', $prow->specId);
        $s = $m1->select()->whereEquals('id', $prow->subCompanyId);
        
        $prow = $specModel->getRow($specSelect);
        
        $row->speciality = (string)$prow;
        
        $prow = $m1->getRow($s);
        
        $row->department = $prow->value;
        
        $row->docId = 0;
        $row->docName = '';
        
        $s = $m4->select()->whereEquals('id', $row->accessTypeId);
        $prow = $m4->getRow($s);
        $row->accessTypeName = $prow->value;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        if ($this->_getParam('employeeId') != NULL) {
            $row->employeeId = $this->_getParam('employeeId');
        }

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

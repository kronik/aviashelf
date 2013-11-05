<?php
class FlightsetController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightset';
    protected $_permissions = array('save', 'add', 'delete');
    protected $_paging = 0;

    protected function _initFields()
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->where(new Kwf_Model_Select_Expr_Sql('visible = 1 AND groupType = 1'))->order('lastname');

        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select();
        
        $setTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $setTypeSelect = $setTypeModel->select()->whereEquals('name', 'Тип захода')->order('value');

        $accessTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $accessTypeSelect = $accessTypeModel->select()->whereEquals('name', 'Типы допусков')->order('value');
        
        $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_Select('wsTypeId', trlKwf('WsType')))
        ->setValues($wstypeModel)
        ->setSelect($wstypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('setId', 'Тип захода'))
        ->setValues($setTypeModel)
        ->setSelect($setTypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_TextField('setMeteoTypeId', 'Метеоминимум'))
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_Select('setTypeId', 'Тип допуска'))
        ->setValues($accessTypeModel)
        ->setSelect($accessTypeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('flightsCount', 'Кол-во полетов'))
        ->setValues(array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20'))
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_Select('setsCount', 'Кол-во заходов'))
        ->setValues(array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20'))
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_DateField('setStartDate', 'Дата начала'))->setAllowBlank(true);
        $this->_form->add(new Kwf_Form_Field_DateField('setEndDate', 'Дата окончания'))->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);        
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m4 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Employees');

        $wstypeModel = Kwf_Model_Abstract::getInstance('Wstypes');
        $wstypeSelect = $wstypeModel->select()->whereEquals('id', $row->wsTypeId);
        $prow = $wstypeModel->getRow($wstypeSelect);
        $row->wsTypeName = $prow->Name;

        $s = $m1->select()->whereEquals('id', $row->setId);
        $prow = $m1->getRow($s);
        $row->setName = $prow->value;
        
        $row->setMeteoTypeId = 0;
        $row->setMeteoTypeName = $prow->value;

        $s = $m4->select()->whereEquals('id', $row->setTypeId);
        $prow = $m4->getRow($s);
        $row->setTypeName = $prow->value;
        
        $s = $m2->select()->whereEquals('id', $row->employeeId);
        $prow = $m2->getRow($s);
        
        $row->employeeName = (string)$prow;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->flightId = $this->_getParam('flightId');

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}
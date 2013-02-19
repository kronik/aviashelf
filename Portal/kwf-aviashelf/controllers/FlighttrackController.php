<?php
class FlighttrackController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flighttracks';
    protected $_permissions = array('save', 'add', 'delete');
    protected $_paging = 0;

    protected function _initFields()
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employees1Select = $employeesModel->select()->whereEquals('visible', '1')->order('lastname');
        $employees2Select = $employeesModel->select()->whereEquals('visible', '1')->order('lastname');
        $employees3Select = $employeesModel->select()->whereEquals('visible', '1')->order('lastname');
        $employees4Select = $employeesModel->select()->whereEquals('visible', '1')->order('lastname');
        $employees5Select = $employeesModel->select()->whereEquals('visible', '1')->order('lastname');
        $employees6Select = $employeesModel->select()->whereEquals('visible', '1')->order('lastname');
        
        $landpointModel = Kwf_Model_Abstract::getInstance('Airports');
        $landpointSelect = $landpointModel->select()->order('Name');
        
        $this->_form->add(new Kwf_Form_Field_Select('airportId', trlKwf('Airport')))
        ->setValues($landpointModel)
        ->setSelect($landpointSelect)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('employee1Id', 'Дежурный КВС'))
        ->setValues($employeesModel)
        ->setSelect($employees1Select)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_Select('employee2Id', 'Руководитель ПБ (СЭИК)'))
        ->setValues($employeesModel)
        ->setSelect($employees2Select)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_Select('employee3Id', 'Руководитель ПБ (ЭНЛ)'))
        ->setValues($employeesModel)
        ->setSelect($employees3Select)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_Select('employee4Id', 'Руководитель ЛС ИАС'))
        ->setValues($employeesModel)
        ->setSelect($employees4Select)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_Select('employee5Id', 'Диспетчер ПДС по ОП'))
        ->setValues($employeesModel)
        ->setSelect($employees5Select)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_Select('employee6Id', 'Дежурный по компании'))
        ->setValues($employeesModel)
        ->setSelect($employees6Select)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(true);
                
        $this->_form->add(new Kwf_Form_Field_TextArea('comments', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);        
    }
        
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m = Kwf_Model_Abstract::getInstance('Employees');
        $landpointModel = Kwf_Model_Abstract::getInstance('Airports');
        
        $s = $landpointModel->select()->whereEquals('id', $row->airportId);
        $prow = $m->getRow($s);
        $row->airportName = (string)$prow;
        
        if ($row->employee1Id != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->employee1Id);
            $prow = $m->getRow($s);
            $row->employee1Name = (string)$prow;
        }
        
        if ($row->employee2Id != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->employee2Id);
            $prow = $m->getRow($s);
            $row->employee2Name = (string)$prow;
        }
        
        if ($row->employee3Id != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->employee3Id);
            $prow = $m->getRow($s);
            $row->employee3Name = (string)$prow;
        }
        
        if ($row->employee4Id != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->employee4Id);
            $prow = $m->getRow($s);
            $row->employee4Name = (string)$prow;
        }
        
        if ($row->employee5Id != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->employee5Id);
            $prow = $m->getRow($s);
            $row->employee5Name = (string)$prow;
        }
        
        if ($row->employee6Id != NULL)
        {
            $s = $m->select()->whereEquals('id', $row->employee6Id);
            $prow = $m->getRow($s);
            $row->employee6Name = (string)$prow;
        }
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->planId = $this->_getParam('planId');

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {        
        $this->updateReferences($row);
    }
}

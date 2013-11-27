<?php
class PlanerstateController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Planerstates';

    protected function _initFields()
    {
        $techModel = Kwf_Model_Abstract::getInstance('Employees');
        $techSelect = $techModel->select()->where(new Kwf_Model_Select_Expr_Sql('visible = 1 AND groupType = 2'))->order('listPosition');
        
        $this->_form->add(new Kwf_Form_Field_Select('responsibleId', 'Техник ПДО'))
        ->setValues($techModel)
        ->setSelect($techSelect)
        ->setWidth(400)
        ->setShowNoSelection(true)
        ->setAllowBlank(true);

        $airplanesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $airplanesSelect = $airplanesModel->select();
        
        $this->_form->add(new Kwf_Form_Field_Select('planeId', trlKwf('Airplane')))
        ->setValues($airplanesModel)
        ->setSelect($airplanesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $landpointsModel = Kwf_Model_Abstract::getInstance('Airports');
        $landpointsSelect = $landpointsModel->select()->order('Name');
        
        $this->_form->add(new Kwf_Form_Field_Select('landpointId', trlKwf('Base point')))
        ->setValues($landpointsModel)
        ->setSelect($landpointsSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $companyModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $companySelect = $companyModel->select()->whereEquals('name', 'Компании для ПЗ')->order('name');
        
        $this->_form->add(new Kwf_Form_Field_Select('typeId', trlKwf('Customer')))
        ->setValues($companyModel)
        ->setSelect($companySelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('priority', trlKwf('Priority')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $objModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $objSelect = $objModel->select()->whereEquals('name', 'Статусы ВС');
        
        $this->_form->add(new Kwf_Form_Field_Select('statusId', trlKwf('Status')))
        ->setValues($objModel)
        ->setSelect($objSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_DateField('statusDate', trlKwf('Date')))
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('reason', trlKwf('Failure Reason')))
        ->setWidth(400)
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_DateField('expectedDate', trlKwf('Expected date')))
        ->setAllowBlank(true);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
        ->setHeight(70)
        ->setWidth(400);        
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $companyModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $companySelect = $companyModel->select()->whereEquals('id', $row->typeId);

        $landpointsModel = Kwf_Model_Abstract::getInstance('Airports');
        $landpointSelect = $landpointsModel->select()->whereEquals('id', $row->landpointId);

        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Airplanes');
        
        $prow = $companyModel->getRow($companySelect);
        $row->typeName = $prow->value;
        
        $prow = $landpointsModel->getRow($landpointSelect);
        $row->landpointName = $prow->Name;
        
        $s = $m1->select()->whereEquals('id', $row->statusId);
        $prow = $m1->getRow($s);
        $row->statusName = $prow->value;
                
        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->NBort;
        
        if ($row->responsibleId != NULL)
        {
            $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
            $employeesSelect = $employeesModel->select()->whereEquals('id', $row->responsibleId);
            
            $prow = $employeesModel->getRow($employeesSelect);
            $row->responsibleName = (string)$prow;
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

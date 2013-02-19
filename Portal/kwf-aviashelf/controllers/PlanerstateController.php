<?php
class PlanerstateController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Planerstates';

    protected function _initFields()
    {
        $airplanesModel = Kwf_Model_Abstract::getInstance('Airplanes');
        $airplanesSelect = $airplanesModel->select();
        
        $this->_form->add(new Kwf_Form_Field_Select('planeId', trlKwf('Airplane')))
        ->setValues($airplanesModel)
        ->setSelect($airplanesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $landpointsModel = Kwf_Model_Abstract::getInstance('Landpoints');
        $landpointsSelect = $landpointsModel->select()->order('description');
        
        $this->_form->add(new Kwf_Form_Field_Select('landpointId', trlKwf('Base point')))
        ->setValues($landpointsModel)
        ->setSelect($landpointsSelect)
        ->setWidth(400)
        ->setAllowBlank(false);

        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->order('Name');
        
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
        $companyModel = Kwf_Model_Abstract::getInstance('Companies');
        $companySelect = $companyModel->select()->whereEquals('id', $row->typeId);

        $landpointsModel = Kwf_Model_Abstract::getInstance('Landpoints');
        $landpointSelect = $landpointsModel->select()->whereEquals('id', $row->landpointId);

        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        $m2 = Kwf_Model_Abstract::getInstance('Airplanes');
        
        $prow = $companyModel->getRow($companySelect);
        $row->typeName = $prow->Name;
        
        $prow = $landpointsModel->getRow($landpointSelect);
        $row->landpointName = $prow->name;
        
        $s = $m1->select()->whereEquals('id', $row->statusId);
        $prow = $m1->getRow($s);
        $row->statusName = $prow->value;
                
        $s = $m2->select()->whereEquals('id', $row->planeId);
        $prow = $m2->getRow($s);
        
        $row->planeName = $prow->NBort;
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

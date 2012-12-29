<?php
class DocumentsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Documents';
    protected $_defaultOrder = array('field' => 'endDate', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete', 'save');
    protected $_editDialog = array(
        'controllerUrl' => '/document',
        'width' => 550,
        'height' => 400
    );

    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));

        #$companyModel = Kwf_Model_Abstract::getInstance('Companies');
        #$companySelect = $companyModel->select()->whereEquals('Hidden', '0');
        
        #$docTypeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        #$docTypeSelect = $docTypeModel->select()->whereEquals('name', 'Типы документов');
        
        #$this->_columns->add(new Kwf_Grid_Column('typeId', trlKwf('Type')))
        #->setEditor(new Kwf_Form_Field_Select('typeId', trlKwf('Type')))
        #->setValues($docTypeModel)
        #->setSelect($docTypeSelect)
        #->setWidth(400)
        #->setAllowBlank(false);
        
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number')));
        $this->_columns->add(new Kwf_Grid_Column_Date('startDate', trlKwf('Doc Start Date')));
        $this->_columns->add(new Kwf_Grid_Column_Date('endDate', trlKwf('Doc End Date')))->setRenderer('checkDate');
        $this->_columns->add(new Kwf_Grid_Column('gradeName', trlKwf('Note')))->setWidth(300)->setRenderer('checkGrade');
        
        #$select = new Kwf_Form_Field_Select();
        #$select->setValues($companyModel);
        #$select->setSelect($companySelect);
        
        #$this->_columns->add(new Kwf_Grid_Column('companyId', trlKwf('Spec Doc company')))
        #->setEditor($select)
        #->setType('string')
        #->setShowDataIndex('Name');
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['ownerId = ?'] = $this->_getParam('ownerId');
        return $ret;
    }
}

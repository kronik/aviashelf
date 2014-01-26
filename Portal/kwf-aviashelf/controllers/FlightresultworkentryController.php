<?php
class FlightresultworkentryController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightresultwork';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $typeModel = Kwf_Model_Abstract::getInstance('Linkdata');
        $typeSelect = $typeModel->select()->whereEquals('name', 'Типы налета');
        
        $this->_form->add(new Kwf_Form_Field_Select('resultId', 'Налет'))
        ->setValues($typeModel)
        ->setSelect($typeSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_Select('workId', 'Наработка'))
        ->setValues(array('workTime1' => 'Фактическая наработка', 'workTime2' => 'Фактический налет', 'workTime3' => 'Налет ночью', 'workTime4' => 'Наработка ночью', 'workTime5' => 'Другая наработка'))
        ->setWidth(400)
        ->setAllowBlank(false);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('Linkdata');
        
        $s = $m1->select()->whereEquals('id', $row->resultId);
        $prow = $m1->getRow($s);
        
        if ($prow != NULL) {
            $row->resultName = $prow->value;
        } else {
            $row->resultName = '';
        }
        
        switch ($row->workId) {
            case 'workTime1':
                $row->workName = 'Фактическая наработка';
                break;
                
            case 'workTime2':
                $row->workName = 'Фактический налет';
                break;
                
            case 'workTime3':
                $row->workName = 'Налет ночью';
                break;
                
            case 'workTime4':
                $row->workName = 'Наработка ночью';
                break;
                
            case 'workTime5':
                $row->workName = 'Другая наработка';
                break;
                
            default:
                $row->workName = '';
                break;
        }
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }

}

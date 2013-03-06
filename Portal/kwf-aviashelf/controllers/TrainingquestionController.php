<?php
class TrainingquestionController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingQuestions';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_File('Picture', trlKwf('File')))
        ->setShowPreview(false)
        ->setAllowOnlyImages(true);
        
        $this->_form->add(new Kwf_Form_Field_ImageViewer('picture_id', trlKwf('Image'), 'Picture'));
        
        $this->_form->add(new Kwf_Form_Field_HtmlEditor('question', trlKwf('Text')))
        ->setWidth(650)
        ->setHeight(300)
        ->setMaxLength(3000)
        ->setAllowBlank(false);
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->trainingId = $this->_getParam('trainingId');

        $m = Kwf_Model_Abstract::getInstance('Trainings');
        
        $s = $m->select()->whereEquals('id', $row->trainingId);
        $prow = $m->getRow($s);
        
        $row->trainingName = (string)$prow;
    }
}

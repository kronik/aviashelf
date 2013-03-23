<?php
class MyquestionController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingContentQuestions';
    protected $_permissions = array();

    protected function _initFields()
    {
//        $cards = $this->_form->add(new Kwf_Form_Container_Cards('picture_id', trl('Picture')));
//        $cards->setCombobox(new Kwf_Form_Field_Hidden('picture_id'));
//
//        $card = $cards->add();
//        
//        $card->setName('0');
//        $card->setTitle('0');
//        
//        $card->fields->add(new Kwf_Form_Field_ImageViewer('picture_id', trlKwf('Image'), 'Picture'));
//        
//        $card->fields->add(new Kwf_Form_Field_ShowField('question', trlKwf('Text')))
//        ->setWidth(650)
//        ->setHeight(300);
//        
//        $card = $cards->add();
//        $card->setName('1');
//        $card->setTitle('1');
//        
//        $card->fields->add(new Kwf_Form_Field_ShowField('question', trlKwf('Text')))
//        ->setWidth(650)
//        ->setHeight(300);
        
        $this->_form->add(new Kwf_Form_Field_ImageViewer('picture_id', trlKwf('Image'), 'Picture'));
                        
        $this->_form->add(new Kwf_Form_Field_ShowField('question', trlKwf('Text')))
        ->setWidth(650)
        ->setHeight(300);
    }
}

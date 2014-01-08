<?php
    
require_once 'FormEx.php';

class SimpleflightplanController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_permissions = array('xls');
    protected $_modelName = 'Flightplans';
    protected $_buttons = array ('xls');
    
    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwf.autoformex';
    }

    protected function _initFields()
    {
        $users = Kwf_Registry::get('userModel');
        
        $this->_form->add(new Kwf_Form_Field_ShowField('planDate', trlKwf('Date')))
        ->setWidth(400);

        $this->_form->add(new Kwf_Form_Field_ShowField('employeeName', trlKwf('Responsible')))
        ->setWidth(400);

        $this->_form->add(new Kwf_Form_Field_ShowField('comment', trlKwf('Additional info')))
        ->setHeight(70)
        ->setWidth(400);            
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();
        
        $this->_progressBar = new Zend_ProgressBar(new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                                                   0, 100);
        $reporter = new Reporter ();
        $reporter->exportFlightPlanToXls($xls, $firstSheet, $row, $this->_progressBar);
        
        $this->_progressBar->finish();
    }
}

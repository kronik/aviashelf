<?php
    
require_once 'FormEx.php';
  
class WorkController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_permissions = array('save', 'add', 'xls');
    protected $_modelName = 'Works';
    protected $_buttons = array ('xls');
    
    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwf.autoformex';
    }

    protected function _initFields()
    {
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'power')
        {
            $today = new DateTime('NOW');
            
            $this->_form->add(new Kwf_Form_Field_Select('month', 'Месяц'))
            ->setValues(array('1' => trlKwf('January'), '2' => trlKwf('February'), '3' => trlKwf('March'), '4' => trlKwf('April'), '5' => trlKwf('May'), '6' => trlKwf('June'), '7' => trlKwf('July'), '8' => trlKwf('August'), '9' => trlKwf('September'), '10' => trlKwf('October'), '11' => trlKwf('November'), '12' => trlKwf('December')))
            ->setWidth(90)
            ->setDefaultValue($today->format('m'))
            ->setAllowBlank(false);
            
            $this->_form->add(new Kwf_Form_Field_Select('year', 'Год'))
            ->setValues(array('2014' => '2014', '2015' => '2015', '2016' => '2016', '2017' => '2017', '2018' => '2018', '2019' => '2019', '2020' => '2020'))
            ->setWidth(90)
            ->setDefaultValue((string)$today->format('Y'))
            ->setAllowBlank(false);
            
            $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Additional info')))
            ->setHeight(70)
            ->setWidth(400);            
        }
        else
        {
            $this->_form->add(new Kwf_Form_Field_ShowField('monthName', trlKwf('Month')))
            ->setWidth(400);

            $this->_form->add(new Kwf_Form_Field_ShowField('year', trlKwf('Year')))
            ->setWidth(400);

            $this->_form->add(new Kwf_Form_Field_ShowField('comment', trlKwf('Additional info')))
            ->setHeight(70)
            ->setWidth(400);            
        }        
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        switch ($row->month) {
            case 1:
                $row->monthName = trlKwf('January');
                break;
            case 2:
                $row->monthName = trlKwf('February');
                break;
            case 3:
                $row->monthName = trlKwf('March');
                break;
            case 4:
                $row->monthName = trlKwf('April');
                break;
            case 5:
                $row->monthName = trlKwf('May');
                break;
            case 6:
                $row->monthName = trlKwf('June');
                break;
            case 7:
                $row->monthName = trlKwf('July');
                break;
            case 8:
                $row->monthName = trlKwf('August');
                break;
            case 9:
                $row->monthName = trlKwf('September');
                break;
            case 10:
                $row->monthName = trlKwf('October');
                break;
            case 11:
                $row->monthName = trlKwf('November');
                break;
            case 12:
                $row->monthName = trlKwf('December');
                break;
                
            default:
                $row->monthName = '';
                break;
        }
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _afterInsert(Kwf_Model_Row_Interface $row) {
        $helper = new Helper ();
        $helper->updateWorkEntries($row->id, NULL, false);
    }
    
    protected function _beforeDelete(Kwf_Model_Row_Interface $row) {
        $db = Zend_Registry::get('db');
        
        $db->delete('employeeWorks', array('workId = ?' => $row->id));
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();
        
        $this->_progressBar = new Zend_ProgressBar(new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                                                   0, 100);
        $reporter = new Reporter ();
        
        $xls = PHPExcel_IOFactory::load("./templates/work_template.xls");
        
        $xls->setActiveSheetIndex(0);
        $firstSheet = $xls->getActiveSheet();
        
        $reporter->exportWorkToXls($xls, $firstSheet, $row, $this->_progressBar);
        
        $this->_progressBar->finish();
        
        return $xls;
    }
}

<?php
abstract class Kwf_Controller_Action_Auto_Form_Ex extends Kwf_Controller_Action_Auto_Form
{
    /**
     * @var Kwf_Form
     */
    protected $_progressBar = null;
    
    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwf.autoformex';
    }

    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        // Should be implemented by inherited form class
    }
    
    protected function _getColumnLetterByIndex($idx)
    {
        $letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
                         'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $maxLetterIndex = count($letters) - 1;
        if ($idx > $maxLetterIndex) {
            return $letters[floor(($idx) / count($letters))-1].$letters[($idx) % count($letters)];
        } else {
            return $letters[$idx];
        }
    }
    
    public function jsonXlsAction()
    {
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Kwf_Exception("XLS is not allowed.");
        }
        
        ini_set('memory_limit', "768M");
        set_time_limit(600); // 10 minuten
            
        $row = $this->_form->getRow();
        $primaryKey = $this->_form->getPrimaryKey();
        
        require_once Kwf_Config::getValue('externLibraryPath.phpexcel').'/PHPExcel.php';
        $xls = new PHPExcel();
        $xls->getProperties()->setCreator(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setLastModifiedBy(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setTitle("KWF Excel Export");
        $xls->getProperties()->setSubject("KWF Excel Export");
        $xls->getProperties()->setDescription("KWF Excel Export");
        $xls->getProperties()->setKeywords("KWF Excel Export");
        $xls->getProperties()->setCategory("KWF Excel Export");
        
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        
        $this->_progressBar = new Zend_ProgressBar(
            new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                                                   0, count($this->_fields));
        $outputXls = $xls;
        
        if ($row && $primaryKey) {
            $outputXls = $this->_fillTheXlsFile($xls, $sheet);
        }
        
        if ($outputXls != NULL) {
            // write the file
            $objWriter = PHPExcel_IOFactory::createWriter($outputXls, 'Excel5');
        } else {
            // write the file
            $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
        }
        
        $downloadkey = uniqid();
        $objWriter->save('temp/'.$downloadkey.'.xls');
        
        $this->_progressBar->finish();
        
        $this->view->downloadkey = $downloadkey;
    }
    
    public function downloadXlsFileAction()
    {
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Kwf_Exception("XLS is not allowed.");
        }
        if (!file_exists('temp/'.$this->_getParam('downloadkey').'.xls')) {
            throw new Kwf_Exception('Wrong downloadkey submitted');
        }
        Kwf_Util_TempCleaner::clean();
        
        $file = array(
                      'contents' => file_get_contents('temp/'.$this->_getParam('downloadkey').'.xls'),
                      'mimeType' => 'application/octet-stream',
                      'downloadFilename' => 'form_'.date('Ymd-Hi').'.xls'
                      );
        Kwf_Media_Output::output($file);
        $this->_helper->viewRenderer->setNoRender();
    }    
}

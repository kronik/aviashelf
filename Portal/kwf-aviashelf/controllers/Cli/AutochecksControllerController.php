<?php
class Cli_AutochecksControllerController extends Kwf_Controller_Action {
    public function indexAction() {
        
        ini_set('memory_limit', "768M");
        set_time_limit(600);
        
        $setsModel = Kwf_Model_Abstract::getInstance('Flightset');
        $setsSelect = $setsModel->select();

        $accessesModel = Kwf_Model_Abstract::getInstance('Flightaccesses');
        $accessesSelect = $accessesModel->select();

        $docsModel = Kwf_Model_Abstract::getInstance('Documents');
        $docsSelect = $docsModel->select();

        $rows = $setsModel->getRows($setsSelect);
        
        foreach ($rows as $row) {
        }

        $rows = $accessesModel->getRows($accessesSelect);
        
        foreach ($rows as $row) {
        }

        $rows = $docsModel->getRows($docsSelect);
        
        foreach ($rows as $row) {
        }

        echo "Done\n";
        
        exit;
    }
}
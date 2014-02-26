<?php
class Row_Employeework extends Kwf_Model_Db_Row {
    public function __get($name) {
        
        if ($name === 'totalOvertimeMinutes') {
            
            $helper = new Helper();
            $workMinutes = $helper->minutesFromDateTime(parent::__get('workTime1'));
            $normMinutes = $helper->minutesFromDateTime(parent::__get('timePerDay'));

            if ($workMinutes > $normMinutes) {
                return $helper->timeFromMinutes($workMinutes - $normMinutes);
            } else if ($workMinutes < $normMinutes) {
                return '-' . $helper->timeFromMinutes($normMinutes - $workMinutes);
            } else {
                return '00:00';
            }
        } else if ($name === 'totalOvertimeDays') {

            $helper = new Helper();
            $workMinutes = $helper->minutesFromDateTime(parent::__get('workTime1'));
            $normMinutes = $helper->minutesFromDateTime(parent::__get('timePerDay'));
            
            if ($normMinutes == 0 && $workMinutes > 0) {
                return 1;
            } else if ($normMinutes > 0 && $workMinutes == 0) {
                return -1;
            } else {
                return 0;
            }
            
        } else {
            return parent::__get($name);
        }
    }
    
    public function __set($name, $value) {
        if (($name === 'totalOvertimeMinutes') || ($name === 'totalOvertimeDays')) {
            
        } else {
            return parent::__set($name, $value);
        }
    }
}

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
            
//        } else if ($name === 'subType') {
//            
//            $helper = new Helper();
//            $work1Minutes = $helper->minutesFromDateTime(parent::__get('workTime1'));
//            $work2Minutes = $helper->minutesFromDateTime(parent::__get('workTime2'));
//            $work3Minutes = $helper->minutesFromDateTime(parent::__get('workTime3'));
//            $work4Minutes = $helper->minutesFromDateTime(parent::__get('workTime4'));
//            $work5Minutes = $helper->minutesFromDateTime(parent::__get('workTime5'));
//
//            $normMinutes = $helper->minutesFromDateTime(parent::__get('timePerDay'));
//
//            $work1Minutes = $work1Minutes + $work2Minutes + $work3Minutes + $work4Minutes + $work5Minutes;
//            
//            $startDate = new DateTime(parent::__get('workDate'));
//            
//            return (($work1Minutes > 0) && ($normMinutes == 0) && ($startDate->format('N') > 5)) ? 'РВ' : '';
//            
        } else if ($name === 'holidayWork') {
            
            $helper = new Helper();
            $work1Minutes = $helper->minutesFromDateTime(parent::__get('workTime1'));
            $work2Minutes = $helper->minutesFromDateTime(parent::__get('workTime2'));
            $work3Minutes = $helper->minutesFromDateTime(parent::__get('workTime3'));
            $work4Minutes = $helper->minutesFromDateTime(parent::__get('workTime4'));
            $work5Minutes = $helper->minutesFromDateTime(parent::__get('workTime5'));
            
            $normMinutes = $helper->minutesFromDateTime(parent::__get('timePerDay'));

            if (($normMinutes > 0) || (parent::__get('subTypeName') != 'РВ')) {
                return '00:00:00';
            } else {
                if ($work1Minutes > 0) {
                    return parent::__get('workTime1');
                } else if ($work2Minutes > 0) {
                    return parent::__get('workTime2');
                } else if ($work3Minutes > 0) {
                    return parent::__get('workTime3');
                } else if ($work4Minutes > 0) {
                    return parent::__get('workTime4');
                } else if ($work5Minutes > 0) {
                    return parent::__get('workTime5');
                }
            }
        } else if ($name === 'workMonthYear') {
            
            $workDate = new DateTime(parent::__get('workDate'));

            return $workDate->format('m-Y');
            
        } else {
            return parent::__get($name);
        }
    }
    
    public function __set($name, $value) {
        if (($name === 'totalOvertimeMinutes') || ($name === 'totalOvertimeDays') ||
            ($name === 'subType') || ($name === 'holidayWork') || ($name === 'workMonthYear')) {
            
        } else {
            return parent::__set($name, $value);
        }
    }
}

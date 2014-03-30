var Works = Ext.extend(Ext.Panel, {
    initComponent : function(test) {
                                              
        var employeeWorks = new Kwf.Auto.GridPanel({
               controllerUrl   : '/employeeworks',
               region          : 'center',
               height          : 600,
               resizable       : true,
               split           : true,
               title           : 'Наработка по сотрудникам'
        });

        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/works',
            region          : 'west',
            width           : 300,
            resizable       : true,
            split           : true,
            collapsible     : true,
            bindings: [{
                        queryParam: 'workId',
                        item: employeeWorks
                      }],
            title           : 'Календарь'
        });
                       
       this.layout = 'border';
       this.items = [grid, {
                     layout: 'border',
                     region: 'center',
                     items: [employeeWorks]
                     }];
        Works.superclass.initComponent.call(this);
    }
});

Ext.util.Format.dateCorrect = function(val)
{
    return val.format('d-m-Y');
};

Ext.util.Format.daysForTime = function(val)
{
    if (val == null) {
        return 0;
    }
    
    if (val == '00:00' || val == '00:00:00') {
        return '';
    }
    
    if (val == '7:12' || val == '07:12' || val == '07:12:00') {
        return '1 день';
    } else {
        var totalTimeValue = val.split(':');
        var hours = (totalTimeValue[0]) * 1;
        minutes = (totalTimeValue[1]) * 1;
    
        if (minutes < 10) {
            minutes = '0' + minutes;
        }
        
        return hours + ':' + minutes;
    }
};

function timeToMinutes(timeStr) {
    if (timeStr == null || timeStr == '' || timeStr == 0) {
        return 0;
    }

    totalTimeValue = timeStr.split(':');
    minutesInHours = (parseInt(totalTimeValue[0])) * 60;
    minutes = parseInt(totalTimeValue[1]);
    
    if (minutesInHours < 0) {
        minutes = -1 * minutes;
    }

    totalMinutes = minutesInHours + minutes;
    
//    console.log(timeStr + ' -> ' + totalMinutes);
    
    return totalMinutes;
}

function timeToString(minutes) {
    
    if (minutes == 0) {
        return '00:00';
    }
    
    mathSign = minutes / Math.abs(minutes);
    minutes = Math.abs(minutes);
    
    minutesStr = (Math.floor(minutes) % 60);
    hoursStr = ((minutes - minutesStr)  / 60) * mathSign;

    if (hoursStr < 10 && hoursStr >= 0) {
        hoursStr = '0' + hoursStr;
    }

    if (minutesStr < 10 && minutesStr >= 0) {
        minutesStr = '0' + minutesStr;
    }

    return hoursStr + ':' + minutesStr;
}

function timeDiff(time1, time2) {
    minutes1 = timeToMinutes(time1);
    minutes2 = timeToMinutes(time2);

    if (minutes1 > minutes2) {
        return timeToString(minutes1 - minutes2);
    } else {
        return '00:00';
    }
}

function timeSum(time1, time2) {
    minutes1 = timeToMinutes(time1);
    minutes2 = timeToMinutes(time2);
    
//    console.log('Minutes: ' + minutes1 + ' + ' + minutes2);
    
    return timeToString(minutes1 + minutes2);
}

Ext.grid.GroupSummary.Calculations['totalOvertime'] = function(v, record, field) {
    timeDifference = timeDiff(record.data.workTime1, record.data.timePerDay);
    timeSummary = timeSum(timeDifference, v);
    
    return timeSummary;
}

Ext.grid.GroupSummary.Calculations['totalOvertimeDaysSum'] = function(v, record, field) {
    
//    console.log(record.data.workDate + ': ' + record.data.totalOvertimeDays + ' + ' + v + ' = ' + (parseInt(record.data.totalOvertimeDays)+ parseInt(v)));
    
    if (v == 0)
    {
        return record.data.totalOvertimeDays;
    }
    
    if ((v == null) || (v == '')) {
        return 0;
    }
    
    if ((record.data.totalOvertimeDays == null) || (record.data.totalOvertimeDays == '')) {
        return v;
    }

    return parseInt(record.data.totalOvertimeDays) + parseInt(v);
}

Ext.grid.GroupSummary.Calculations['totalOvertimeMinutesSum'] = function(v, record, field) {
   
    timeSummary = timeSum(record.data.totalOvertimeMinutes, v);
    
//    console.log(record.data.workDate + ': ' + record.data.totalOvertimeMinutes + ' + ' + v);
    
    return timeSummary;
}

Ext.grid.GroupSummary.Calculations['totalDays'] = function(v, record, field)
{
    if (v == 0)
    {
        return record.data.timePerDay;
    }
    
    if ((v == null) || (v == '')) {
        return '00:00';
    }
    
    if ((record.data.timePerDay == null) || (record.data.timePerDay == '')) {
        return '00:00';
    }
    
    var totalTimeValue = v.split(':');
    var addTimeValue = record.data.timePerDay.split(':');
    var hours = (totalTimeValue[0]) * 1 + (addTimeValue[0]) * 1;
    var minutes = 0;
    var hoursAddition = 0;
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 59)
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 - 60;
        hoursAddition = 1;
    }
    else
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1;
    }
    
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    
    hours = hours + hoursAddition;
    
    return hours + ':' + minutes;
}

Ext.util.Format.totalOvertimeDaysColorer = function(val) {
    
    days = parseInt(val);
    
    if (days == 0) {
        return '<span style="color:black;">' + val + '</span>';
    } else if (days == 1) {
        return '<span style="color:red;">' + val + '</span>';
    } else {
        return '<span style="color:blue;">' + val + '</span>';
    }
};

Ext.util.Format.totalOvertimeMinutesColorer = function(val) {
    
    minutes = timeToMinutes(val);
    
    if (minutes == 0) {
        return '<span style="color:black;">' + val + '</span>';
    } else if (minutes > 0) {
        return '<span style="color:red;">' + val + '</span>';
    } else {
        return '<span style="color:blue;">' + val + '</span>';
    }
};


Ext.util.Format.dateShrink = function(val)
{
    if (val.getDay() == 0 || val.getDay() == 6)
    {
        return '<span style="color:red;">' + val.format('d') + '</span>';
    }
    else
    {
        return '<span style="color:black;">' + val.format('d') + '</span>';
    }
};

Ext.util.Format.typeHighlight = function(val)
{
    if (val != null && val != '' && (val == 'В' || val == 'РВ'|| val == 'Врд'))
    {
        return '<span style="color:red;">' + val + '</span>';
    }
    else
    {
        return '<span style="color:black;">' + val + '</span>';
    }
};

Ext.util.Format.dateClearEmpty = function(val)
{
    if (val == null || val == '00:00' || val == '00:00:00') {
        return '';
    } else {
        var totalTimeValue = val.split(':');

        return totalTimeValue[0] + ':' + totalTimeValue[1];
    }
};

Ext.util.Format.formatOvertime = function(val)
{
    if (val == null) {
        return '';
    } else {
        var totalTimeValue = val.split(':');
        
        return totalTimeValue[0] + ':' + totalTimeValue[1];
    }
};

Ext.grid.GroupSummary.Calculations['totalTimeDescription'] = function(v, record, field) {
    return 'Итого: '
}


Ext.grid.GroupSummary.Calculations['totalTime1'] = function(v, record, field)
{
    if (v == 0)
    {
        return record.data.workTime1;
    }
    
    if ((v == null) || (v == '')) {
        return '00:00';
    }
    
    if ((record.data.workTime1 == null) || (record.data.workTime1 == '')) {
        return '00:00';
    }
    
    var totalTimeValue = v.split(':');
    var addTimeValue = record.data.workTime1.split(':');
    var hours = (totalTimeValue[0]) * 1 + (addTimeValue[0]) * 1;
    var minutes = 0;
    var hoursAddition = 0;
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 59)
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 - 60;
        hoursAddition = 1;
    }
    else
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1;
    }
    
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    
    hours = hours + hoursAddition;
    
    return hours + ':' + minutes + ':00';
}

Ext.grid.GroupSummary.Calculations['totalTime2'] = function(v, record, field)
{
    if (v == 0)
    {
        return record.data.workTime2;
    }
    
    if ((v == null) || (v == '')) {
        return '00:00';
    }
    
    if ((record.data.workTime2 == null) || (record.data.workTime2 == '')) {
        return '00:00';
    }
    
    var totalTimeValue = v.split(':');
    var addTimeValue = record.data.workTime2.split(':');
    var hours = (totalTimeValue[0]) * 1 + (addTimeValue[0]) * 1;
    var minutes = 0;
    var hoursAddition = 0;
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 59)
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 - 60;
        hoursAddition = 1;
    }
    else
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1;
    }
    
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    
    hours = hours + hoursAddition;
    
    return hours + ':' + minutes + ':00';
}

Ext.grid.GroupSummary.Calculations['totalTime3'] = function(v, record, field)
{
    if (v == 0)
    {
        return record.data.workTime3;
    }
    
    if ((v == null) || (v == '')) {
        return '00:00';
    }
    
    if ((record.data.workTime3 == null) || (record.data.workTime3 == '')) {
        return '00:00';
    }
    
    var totalTimeValue = v.split(':');
    var addTimeValue = record.data.workTime3.split(':');
    var hours = (totalTimeValue[0]) * 1 + (addTimeValue[0]) * 1;
    var minutes = 0;
    var hoursAddition = 0;
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 59)
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 - 60;
        hoursAddition = 1;
    }
    else
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1;
    }
    
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    
    hours = hours + hoursAddition;
    
    return hours + ':' + minutes + ':00';
}

Ext.grid.GroupSummary.Calculations['totalTime4'] = function(v, record, field)
{
    if (v == 0)
    {
        return record.data.workTime4;
    }
    
    if ((v == null) || (v == '')) {
        return '00:00';
    }
    
    if ((record.data.workTime4 == null) || (record.data.workTime4 == '')) {
        return '00:00';
    }
    
    var totalTimeValue = v.split(':');
    var addTimeValue = record.data.workTime4.split(':');
    var hours = (totalTimeValue[0]) * 1 + (addTimeValue[0]) * 1;
    var minutes = 0;
    var hoursAddition = 0;
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 59)
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 - 60;
        hoursAddition = 1;
    }
    else
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1;
    }
    
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    
    hours = hours + hoursAddition;
    
    return hours + ':' + minutes + ':00';
}

Ext.grid.GroupSummary.Calculations['totalTime5'] = function(v, record, field)
{
    if (v == 0)
    {
        return record.data.workTime5;
    }
    
    if ((v == null) || (v == '')) {
        return '00:00';
    }
    
    if ((record.data.workTime5 == null) || (record.data.workTime5 == '')) {
        return '00:00';
    }
    
    var totalTimeValue = v.split(':');
    var addTimeValue = record.data.workTime5.split(':');
    var hours = (totalTimeValue[0]) * 1 + (addTimeValue[0]) * 1;
    var minutes = 0;
    var hoursAddition = 0;
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 59)
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 - 60;
        hoursAddition = 1;
    }
    else
    {
        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1;
    }
    
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    
    hours = hours + hoursAddition;
    
    return hours + ':' + minutes + ':00';
}


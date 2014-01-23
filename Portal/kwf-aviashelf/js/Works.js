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

Ext.util.Format.dateClearEmpty = function(val)
{
    if (val == null || val == '00:00' || val == '00:00:00') {
        return '';
    } else {
        return val;
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
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 60)
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
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 60)
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
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 60)
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
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 60)
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
    
    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 60)
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


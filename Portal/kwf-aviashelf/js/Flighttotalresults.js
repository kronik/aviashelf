var Flighttotalresults = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
           var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/flighttotalresults',
                                         region          : 'center',
                                         title           : 'Общий налет'
                                         });
       
       this.layout = 'border';
       this.items = [grid];

       Flighttotalresults.superclass.initComponent.call(this);
    }
});

// define a custom summary function
Ext.grid.GroupSummary.Calculations['totalFlightTime'] = function(v, record, field)
{
    if (v == 0)
    {
        return record.data.flightTime;
    }
    
    if ((v == null) || (v == '')) {
        return '00:00';
    }
    
    if ((record.data.flightTime == null) || (record.data.flightTime == '')) {
        return '00:00';
    }
    
    var totalTimeValue = v.split(':');
    var addTimeValue = record.data.flightTime.split(':');
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


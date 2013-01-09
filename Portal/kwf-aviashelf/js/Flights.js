var Flights = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var flightresults = new Kwf.Auto.GridPanel({
              controllerUrl   : '/flightfullresults',
              collapsible     : true,
              title           : trlKwf('Flight results')
        });
                         
        var flightgroups = new Kwf.Auto.GridPanel({
            controllerUrl   : '/flightgroups',
            collapsible     : true,
            title           : trlKwf('Flight groups')
        });
        
        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/flights',
            region          : 'west',
            width           : 300,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : trlKwf('Flights'),
            bindings: [
            {
                queryParam: 'flightId',
                item: flightresults
            },
            {
                queryParam: 'flightId',
                item: flightgroups
            }]
        });
                           
        var tabs = new Ext.TabPanel({
               border    : true,
               activeTab : 0,
               region    : 'center',
               tabPosition:'top',
               items:[flightgroups, flightresults]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [tabs]
        }];
        Flights.superclass.initComponent.call(this);
    }
});

// define a custom summary function
Ext.grid.GroupSummary.Calculations['totalTime'] = function(v, record, field)
{
    if (v == 0)
    {
        return record.data.flightTime;
    }

    var totalTimeValue = v.split(':');
    var addTimeValue = record.data.flightTime.split(':');
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
    
    hours = hours + hoursAddition;

    return hours + ':' + minutes + ':00';
}

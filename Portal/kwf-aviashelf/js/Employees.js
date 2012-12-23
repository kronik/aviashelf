var Employees = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var form = new Kwf.Auto.FormPanel({
            controllerUrl   : '/employee',
            region          : 'north',
            height          : 400,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : trlKwf('Employee')
        });
                           
        var documents = new Kwf.Auto.GridPanel({
             controllerUrl   : '/documents',
             region          : 'center',
             title           : trlKwf('Documents')
        });
        
        var summary = new Ext.grid.GroupSummary();

        var flightresults = new Kwf.Auto.GridPanel({
              controllerUrl   : '/flightresults',
              region          : 'south',
              height          : 200,
              resizable       : true,
              split           : true,
              collapsible     : true,
              title           : trlKwf('Flight results')
        });
        
        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/employees',
            region          : 'west',
            width           : 450,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : trlKwf('Employees'),
            bindings: [form, {
                queryParam: 'ownerId',
                item: documents
            },
            {
                queryParam: 'ownerId',
                item: flightresults
            }]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [form, documents, flightresults]
        }];
        Employees.superclass.initComponent.call(this);
    }
});

function toDate(dStr,format)
{
    console.log(dStr);

	var now = new Date();

	if (format == "h:m")
    {
 		now.setHours(dStr.substr(0,dStr.indexOf(":")));
 		now.setMinutes(dStr.substr(dStr.indexOf(":")+1));
 		now.setSeconds(0);
 		return now;
	}else
		return "Invalid Format";
}

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

Ext.util.Format.checkDate = function(val)
{
    var endDate = val.toISOString().slice(0, 10);
    var today = new Date();
    today.setDate(today.getDate() - 7);
    
    if (val > today)
    {
        return '<span style="color:green;">' + endDate + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + endDate + '</span>';
    }
    return val;
};

Ext.util.Format.checkGrade = function(val)
{    
    if (val == 'зачет' || val == 'пять' || val == 'четыре' || val == 'допущен')
    {
        return '<span style="color:green;">' + val + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + val + '</span>';
    }
    return val;
};

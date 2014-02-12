var Employees = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {                           
        var documents = new Kwf.Auto.GridPanel({
             controllerUrl   : '/documents',
             collapsible     : true,
             stripeRows      : true,
             title           : 'Периодическая подготовка'
        });
                           
        var user = new Kwf.Auto.FormPanel({
             controllerUrl   : '/employee',
             collapsible     : true,
             title           : trlKwf('General Info')
        });

        var accesses = new Kwf.Auto.GridPanel({
              controllerUrl   : '/flightaccesses',
              collapsible     : true,
              title           : 'Летные проверки'
        });
        
        var flightresults = new Kwf.Auto.GridPanel({
              controllerUrl   : '/flightresults',
              collapsible     : true,
              title           : trlKwf('Flight results')
        });

        var flightsets = new Kwf.Auto.GridPanel({
              controllerUrl   : '/myflightsets',
              collapsible     : true,
              title           : 'Заходы'
        });

        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/employees',
            region          : 'west',
            width           : 400,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : trlKwf('Flight crew'),
            bindings: [{
                queryParam: 'ownerId',
                item: documents
            },
            {
                queryParam: 'id',
                item: user
            },
            {
                queryParam: 'employeeId',
                item: accesses
            },
            {
                queryParam: 'ownerId',
                item: flightresults
            },
            {
                queryParam: 'ownerId',
                item: flightsets
            }]
        });
                           
        var tabs = new Ext.TabPanel({
               border    : true,
               activeTab : 0,
               region    : 'center',
               tabPosition:'top',
               items:[user, documents, flightresults, flightsets, accesses]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [tabs]
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
Ext.grid.GroupSummary.Calculations['totalCommonFlightTime'] = function(v, record, field)
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

//    if (v == 0)
//    {
//        if (record.data.typeName === "Налет общий") {
//            return record.data.flightTime;
//        } else {
//            return '00:00';
//        }
//    }
//    
//    if ((v == null) || (v == '')) {
//        return '00:00';
//    }
//    
//    if ((record.data.flightTime == null) || (record.data.flightTime == '')) {
//        return '00:00';
//    }
//    
//    var totalTimeValue = v.split(':');
//    var addTimeValue = "00:00".split(':');
//    
//    if (record.data.typeName === "Налет общий") {
//        addTimeValue = record.data.flightTime.split(':');
//    }
//    
//    var hours = (totalTimeValue[0]) * 1 + (addTimeValue[0]) * 1;
//    var minutes = 0;
//    var hoursAddition = 0;
//    
//    if ((totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 > 59)
//    {
//        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1 - 60;
//        hoursAddition = 1;
//    }
//    else
//    {
//        minutes = (totalTimeValue[1]) * 1 + (addTimeValue[1]) * 1;
//    }
//    
//    if (minutes < 10) {
//        minutes = '0' + minutes;
//    }
//    
//    hours = hours + hoursAddition;
//
//    return hours + ':' + minutes + ':00';
}

Ext.util.Format.docCheckDate = function(val)
{
    if ((val == null) || (val == '')) {
        return val;
    }

    var month = val.getMonth();
    var monthStr = month < 10 ? '0' + month : month;
    var day = val.getDay();
    var dayStr = day < 10 ? '0' + day : day;
    var year = val.getYear();
    
    var newdate = dayStr + "-" + monthStr + "-" + year;
    
    var dateToCheck = new Date();
    var today = new Date();
    
    dateToCheck.setDate(dateToCheck.getDate() - 7);
    
    if ((val > today) && (val > dateToCheck))
    {
        return '<span style="color:green;">' + val.format('d-m-Y') + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + val.format('d-m-Y') + '</span>';
    }
    return val;
};

Ext.util.Format.exCheckDate = function(val, record, field)
{
    if ((val == null) || (val == '')) {
        return val;
    }
        
    var month = val.getMonth();
    var monthStr = month < 10 ? '0' + month : month;
    var day = val.getDay();
    var dayStr = day < 10 ? '0' + day : day;
    var year = val.getYear();
    
    var newdate = dayStr + "-" + monthStr + "-" + year;
    
    var dateToCheck = new Date();
    var today = new Date();
    
    dateToCheck.setDate(dateToCheck.getDate() - 7);
    
    if ((val > today) && (val > dateToCheck))
    {
        return '<span style="color:green;">' + val.format('d-m-Y') + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + val.format('d-m-Y') + '</span>';
    }
    return val;
};

Ext.util.Format.checkGrade = function(val)
{
    if ((val == null) || (val == '')) {
        return val;
    }

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

//Kwf.keepAlive = function() {};
//Kwf.keepAliveActivated = false;
//Kwf.activateKeepAlive = function() {};

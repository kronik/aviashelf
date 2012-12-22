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

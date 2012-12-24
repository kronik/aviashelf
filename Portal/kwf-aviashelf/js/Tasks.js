var Tasks = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/task',
                                         region          : 'center'
                                         });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/tasks',
                                         region          : 'west',
                                         width           : 300,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('Tasks'),
                                         bindings: [{
                                                    queryParam: 'id',
                                                    item: form
                                                    }]
                                         });
       
       this.layout = 'border';
       this.items = [grid, {
                     layout: 'border',
                     region: 'center',
                     items: [form]
                     }];
       Tasks.superclass.initComponent.call(this);
    }
});

Ext.util.Format.checkDate = function(val)
{
    var endDate = val.toISOString().slice(0, 10);
    var today = new Date();
    today.setDate(today.getDate() - 3);
    
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

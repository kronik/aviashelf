var Calendar = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/calendarentry',
                                         region          : 'center'
                                         });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/calendar',
                                         region          : 'west',
                                         width           : 350,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : 'Календарь',
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
       Calendar.superclass.initComponent.call(this);
    }
});

var Flightresultwork = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/flightresultworkentry',
                                         region          : 'center'
                                         });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/flightresultwork',
                                         region          : 'west',
                                         width           : 300,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : 'Налет -> наработка',
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
       Flightresultwork.superclass.initComponent.call(this);
    }
});

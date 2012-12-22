var Airports = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/airport',
                                         region          : 'center'
                                         });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/airports',
                                         region          : 'west',
                                         width           : 450,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('Airports'),
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
       Airports.superclass.initComponent.call(this);
    }
});

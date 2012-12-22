var Countries = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/country',
                                         region          : 'center'
                                         });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/countries',
                                         region          : 'west',
                                         width           : 450,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('Countries'),
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
       Countries.superclass.initComponent.call(this);
    }
});

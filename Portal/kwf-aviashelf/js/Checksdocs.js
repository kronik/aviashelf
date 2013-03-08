var Checksdocs = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/checkdoc',
                                         region          : 'center'
                                         });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/checksdocs',
                                         region          : 'west',
                                         width           : 300,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('Documents checks'),
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
       Checksdocs.superclass.initComponent.call(this);
    }
});

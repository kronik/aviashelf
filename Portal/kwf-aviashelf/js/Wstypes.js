var Wstypes = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/wstype',
                                         region          : 'center'
                                         });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/wstypes',
                                         region          : 'west',
                                         width           : 400,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('WsTypes'),
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
       Wstypes.superclass.initComponent.call(this);
    }
});

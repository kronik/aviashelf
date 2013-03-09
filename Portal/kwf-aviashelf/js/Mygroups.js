var Mygroups = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {                           
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/mygroup',
                                         region          : 'center'
       });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/mygroups',
                                         region          : 'west',
                                         width           : 300,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('My groups'),
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
       Mygroups.superclass.initComponent.call(this);
    }
});
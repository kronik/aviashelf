var Mytrainings = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {                           
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/mytraining',
                                         region          : 'center'
       });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/mytrainings',
                                         region          : 'west',
                                         width           : 400,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('My trainings'),
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
       Mytrainings.superclass.initComponent.call(this);
    }
});
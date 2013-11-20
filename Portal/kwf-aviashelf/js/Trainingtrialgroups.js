var Trainingtrialgroups = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var results = new Kwf.Auto.GridPanel({
                                            controllerUrl   : '/trainingresults',
                                            stripeRows      : true,
                                            region          : 'east',
                                            width           : '40%',
                                            resizable       : true,
                                            split           : true,
                                            collapsible     : true,
                                            title           : trlKwf('Employees')
                                            });
                           
       var grid = new Kwf.Auto.GridPanel({
                                              controllerUrl   : '/traininggroups',
                                              stripeRows      : true,
                                              region          : 'center',
                                              title           : trlKwf('Groups'),
                                              bindings: [{
                                                         queryParam: 'groupId',
                                                         item: results
                                                         }]
                                              });
       this.layout = 'border';
       this.items = [results, {
                     layout: 'border',
                     region: 'center',
                     items: [grid]
                     }];
       Trainingtrialgroups.superclass.initComponent.call(this);
    }
});

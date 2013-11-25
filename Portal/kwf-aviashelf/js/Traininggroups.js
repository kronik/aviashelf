var Traininggroups = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
                                
       var topics = new Kwf.Auto.GridPanel({
                                           controllerUrl   : '/personresults',
                                           region          : 'east',
                                           width           : 500,
                                           //title           : 'Дисциплины'
                                        });

       var persons = new Kwf.Auto.GridPanel({
                                            controllerUrl   : '/grouppersons',
                                            region          : 'center',
                                            stripeRows      : true,
                                            bindings: [{
                                                       queryParam: 'groupPersonId',
                                                       item: topics
                                                       }]
       });
                                
       var details = new Ext.Panel({
                                     layout          :'border',
                                     stripeRows      : true,
                                     region          : 'east',
                                     width           : '60%',
                                     resizable       : true,
                                     split           : true,
                                     collapsible     : true,
                                     title           : trlKwf('Employees'),
                                     items: [persons, topics]
       });
                                
       var grid = new Kwf.Auto.GridPanel({
                                              controllerUrl   : '/traininggroups',
                                              stripeRows      : true,
                                              region          : 'center',
                                              title           : trlKwf('Groups'),
                                              bindings: [{
                                                         queryParam: 'groupId',
                                                         item: persons
                                                         }]
                                              });
       this.layout = 'border';
       this.items = [details, {
                     layout: 'border',
                     region: 'center',
                     items: [grid]
                     }];
       Traininggroups.superclass.initComponent.call(this);
    }
});

Ext.util.Format.checkResultScore = function(v, params, record)
{
    if (record.data.currentScore > 1)
    {
        return '<span style="color:green;">' + record.data.trainingName + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + record.data.trainingName + '</span>';
    }
}


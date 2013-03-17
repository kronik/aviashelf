var Trainings = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var results = new Kwf.Auto.GridPanel({
                                            controllerUrl   : '/trainingresults',
                                            region          : 'east',
                                            width           : 500,
                                            stripeRows      : true,
                                            split           : true,
                                            collapsible     : true,
                                            title           : trlKwf('Employees')
                                            });
                           
       var answers = new Kwf.Auto.GridPanel({
                                              controllerUrl   : '/traininganswers',
                                              region          : 'east',
                                              width           : 500,
                                              stripeRows      : true,
                                              split           : true,
                                              collapsible     : true,
                                              title           : trlKwf('Answers')
                                              });
                           
       var questions = new Kwf.Auto.GridPanel({
                                              controllerUrl   : '/trainingquestions',
                                              region          : 'center',
                                              stripeRows      : true,
                                              bindings: [{
                                                         queryParam: 'questionId',
                                                         item: answers
                                                         }]
                                              });
                           
       var groups = new Kwf.Auto.GridPanel({
                                              controllerUrl   : '/traininggroups',
                                              region          : 'center',
                                              stripeRows      : true,
                                              title           : trlKwf('Groups'),
                                              bindings: [{
                                                         queryParam: 'groupId',
                                                         item: results
                                                         }]
                                              });
                           
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/training',
                                         region          : 'center',
                                         title           : trlKwf('Theory')
       });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/trainings',
                                         region          : 'west',
                                         width           : '30%',
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('Trainings'),
                                         bindings: [{
                                                    queryParam: 'trainingId',
                                                    item: questions
                                                    },
                                                    {
                                                    queryParam: 'trainingId',
                                                    item: groups
                                                    },
                                                    {
                                                    queryParam: 'id',
                                                    item: form
                                                    }]
       });
    
       var practice = new Ext.Panel({
                           title: trlKwf('Practice'),
                           layout:'border',
                           items: [questions, answers]
       
       });
                           
       var process = new Ext.Panel({
                                    title: trlKwf('Groups'),
                                    layout:'border',
                                    items: [groups, results]
       });
                           
       var tabs = new Ext.TabPanel({
                                       border    : true,
                                       activeTab : 0,
                                       region    : 'center',
                                       tabPosition:'top',
                                       items:[form, practice, process]
       });
       
       this.layout = 'border';
       this.items = [grid, {
                     layout: 'border',
                     region: 'center',
                     items: [tabs]
                     }];
       Trainings.superclass.initComponent.call(this);
    }
});

Ext.util.Format.checkScore = function(v, params, record)
{
    if (record.data.currentScore > 1)
    {
        return '<span style="color:green;">' + record.data.employeeName + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + record.data.employeeName + '</span>';
    }
}

Ext.util.Format.highlightScore = function(val)
{
    if (val > 1)
    {
        return '<span style="color:green;">' + val + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + val + '</span>';
    }
    return val;
};

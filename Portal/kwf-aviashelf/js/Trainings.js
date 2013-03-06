var Trainings = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var answers = new Kwf.Auto.GridPanel({
                                              controllerUrl   : '/traininganswers',
                                              region          : 'east',
                                              width           : 500,
                                              stripeRows      : true,
                                              title           : trlKwf('Answers')

                                              //                                         title           : trlKwf('Questions')
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
                                              title           : trlKwf('Groups')

//                                              bindings: [{
//                                                         queryParam: 'questionId',
//                                                         item: answers
//                                                         }]
                                              });
                           
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/training',
                                         region          : 'center',
                                         title           : trlKwf('Theory')
       });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/trainings',
                                         region          : 'west',
                                         width           : 300,
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
    
       var panel = new Ext.Panel({
                           title: trlKwf('Practice'),
                           layout:'border',
                           items: [questions, answers]
       
       });
                           
       var tabs = new Ext.TabPanel({
                                       border    : true,
                                       activeTab : 0,
                                       region    : 'center',
                                       tabPosition:'top',
                                       items:[form, panel, groups]
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

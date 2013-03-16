var Mygroups = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
                          
       var results = new Kwf.Auto.GridPanel({
                                        controllerUrl   : '/myresults',
                                        region          : 'north',
                                        height          : 50,
                                        split           : true,
                                        collapsible     : false
       });
                          
       var answers = new Kwf.Auto.GridPanel({
                                           controllerUrl   : '/myanswers',
                                           region          : 'south',
                                           height          : 200,
                                           stripeRows      : true,
                                           split           : true,
                                           collapsible     : false,
                                           title           : trlKwf('Answers')
       });

       var question = new Kwf.Auto.FormPanel({
                                         title: trlKwf('Question'),
                                         controllerUrl   : '/myquestion',
                                         region          : 'center'
       });

       var questions = new Kwf.Auto.GridPanel({
                                             title: trlKwf('Questions'),
                                             controllerUrl   : '/myquestions',
                                             region          : 'west',
                                             width           : '5%',
                                             stripeRows      : true,
                                             split           : true,
                                             bindings: [{
                                                             queryParam: 'id',
                                                             item: question
                                                        }
                                                        ,
                                                        {
                                                            queryParam: 'questionId',
                                                            item: answers
                                                        }]
       });

              
       var grid = new MyGroupsGrid({
                                         controllerUrl   : '/mygroups',
                                         region          : 'west',
                                         width           : '100%',
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : false,
                                         title           : trlKwf('My groups'),
                                         bindings: [{
                                                        queryParam: 'groupId',
                                                        item: results
                                                    },
                                                    {
                                                        queryParam: 'groupId',
                                                        item: questions
                                                    }]
       });
                                                    
       var practice = new Ext.Panel({
                                   layout:'border',
                                   region:'center',
                                   height: '90%',
                                   items:[results, {
                                           layout: 'border',
                                           region: 'center',
                                           items: [questions, question, answers]
                                          }]
       });
           
       this.layout = 'border';
       this.items = [grid, {
                     layout: 'border',
                     region: 'center',
                     items: [practice]
                     }];
       Mygroups.superclass.initComponent.call(this);
    }
});
var Myresults = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {                           
       var form = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/myresults',
                                         region          : 'center',
                                         baseParams: {
                                           groupId: 123
                                         }
       });
                             
       var answers = new Kwf.Auto.GridPanel({
                                       controllerUrl   : '/mytrainings',
                                       region          : 'south',
                                       width           : 500,
                                       resizable       : true,
                                       split           : true,
                                       collapsible     : false,
                                       title           : trlKwf('Answers')
       });
//
//       var grid = new Kwf.Auto.GridPanel({
//                                         controllerUrl   : '/mygroups',
//                                         region          : 'west',
//                                         width           : 300,
//                                         resizable       : true,
//                                         split           : true,
//                                         collapsible     : true,
//                                         title           : trlKwf('My groups'),
//                                         bindings: [{
//                                                         queryParam: 'id',
//                                                         item: form
//                                                    },
//                                                    {
//                                                         queryParam: 'id',
//                                                         item: answers
//                                                    }]
//       });
           
       this.layout = 'border';
       this.items = [answers, {
                     layout: 'border',
                     region: 'center',
                     items: [form/*, answers*/]
                     }];
       Myresults.superclass.initComponent.call(this);
    }
});
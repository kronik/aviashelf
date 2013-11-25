Actiongrid = Ext.extend(Kwf.Auto.GridPanel,
{
    initComponent: function() {
        Actiongrid.superclass.initComponent.call(this);
        if (!this.columnsConfig) this.columnsConfig = { };
        this.columnsConfig['customButton'] = {
            clickHandler: function(grid, rowIndex, col, e) {
                var r = grid.getStore().getAt(rowIndex);
                this.onCustomAction(r.data.id);
            },
            scope: this
        };
    },

    onCustomAction: function(rowId) {}
});


Mygroupsgrid = Ext.extend(Actiongrid,
{
    initComponent: function() {
        Mygroupsgrid.superclass.initComponent.call(this);
    },

    onCustomAction: function(rowId) {
        var params = this.getBaseParams() || {};
        params[this.store.reader.meta.id] = rowId;
        this.collapse();
        this.hide();
    }
});

var Mygroups = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {                          
       var answers = new Kwf.Auto.GridPanel({
                                           controllerUrl   : '/myanswers',
                                           region          : 'south',
                                           height          : 300,
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

              
       var grid = new Mygroupsgrid({
                                         controllerUrl   : '/mygroups',
                                         region          : 'west',
                                         width           : '100%',
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : false,
                                         title           : trlKwf('My groups'),
                                         bindings: [{
                                                        queryParam: 'groupId',
                                                        item: questions
                                                    }]
       });
                                                    
       var practice = new Ext.Panel({
                                   layout:'border',
                                   region:'center',
                                   height: '90%',
                                   items:[questions, {
                                           layout: 'border',
                                           region: 'center',
                                           items: [question, answers]
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
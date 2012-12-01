var Dictionaries = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var dictionary = new Kwf.Auto.FormPanel({
                                        controllerUrl   : '/dictionary',
                                        region          : 'center',
                                        height          : 200
                                        });
                              
        var dictionaryentries = new Kwf.Auto.GridPanel({
                                            controllerUrl   : '/dictionary-entries',
                                            region          : 'south',
                                            resizable       : true,
                                            split           : true,
                                            collapsible     : true,
                                            title           : trl('Dictionary')
                                            });

        var grid = new Kwf.Auto.GridPanel({
                                       controllerUrl   : '/dictionaries',
                                       region          : 'west',
                                       width           : 300,
                                       resizable       : true,
                                       split           : true,
                                       collapsible     : true,
                                       title           : trl('Dictionaries'),
                                       bindings: [dictionary, {
                                                  queryParam: 'name',
                                                  item : dictionaryentries
                                                  }]
                                       });
        this.layout = 'border';
        this.items = [grid, {
                   layout: 'border',
                   region: 'center',
                   items: [dictionary, dictionaryentries]
                   }];
        Dictionaries.superclass.initComponent.call(this);
    }
});

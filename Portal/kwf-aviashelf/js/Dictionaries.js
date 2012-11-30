var Dictionaries = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var dictionary = new Kwf.Auto.GridPanel({
                                            controllerUrl   : '/dictionary',
                                            region          : 'center',
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
                                                  queryParam: 'name'
                                                  }]
                                       });

        this.layout = 'border';
        this.items = [grid, {
                   layout: 'border',
                   region: 'center',
                   items: [dictionary]
                   }];
        Dictionaries.superclass.initComponent.call(this);
    }
});

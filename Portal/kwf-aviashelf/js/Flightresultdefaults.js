var Flightresultdefaults = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var form = new Kwf.Auto.FormPanel({
                                        controllerUrl   : '/flightresultdefault',
                                        region          : 'center',
                                        title           : 'Связь'
                                        });

        var grid = new Kwf.Auto.GridPanel({
                                        controllerUrl   : '/flightresultdefaults',
                                        region          : 'west',
                                        width           : 430,
                                        resizable       : true,
                                        split           : true,
                                        collapsible     : true,
                                        title           : 'Связи',
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
        Flightresultdefaults.superclass.initComponent.call(this);
    }
});

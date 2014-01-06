var Flightresultobjdefaults = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var form = new Kwf.Auto.FormPanel({
                                        controllerUrl   : '/flightresultobjdefault',
                                        region          : 'center',
                                        title           : 'Связь'
                                        });

        var grid = new Kwf.Auto.GridPanel({
                                        controllerUrl   : '/flightresultobjdefaults',
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
        Flightresultobjdefaults.superclass.initComponent.call(this);
    }
});

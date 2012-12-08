var Links = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var form = new Kwf.Auto.FormPanel({
            controllerUrl   : '/link',
            region          : 'center'
        });

        var contacts = new Kwf.Auto.GridPanel({
            controllerUrl   : '/link-data',
            region          : 'south',
            height          : 600,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : trl('Link-data')
        });

        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/links',
            region          : 'west',
            width           : 300,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : trl('Links'),
            bindings: [form, {
                queryParam: 'link_id',
                item: contacts
            }]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [form, contacts]
        }];
        Links.superclass.initComponent.call(this);
    }
});

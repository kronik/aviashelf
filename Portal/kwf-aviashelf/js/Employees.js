var Employees = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var form = new Kwf.Auto.FormPanel({
            controllerUrl   : '/employee',
            region          : 'center'
        });
                           
        var documents = new Kwf.Auto.GridPanel({
             controllerUrl   : '/documents',
             region          : 'south',
             height          : 200,
             resizable       : true,
             split           : true,
             collapsible     : true,
             title           : trlKwf('Documents')
        });

        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/employees',
            region          : 'west',
            width           : 400,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : trlKwf('Employees'),
            bindings: [form, {
                queryParam: 'ownerId',
                item: documents
            }]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [form, documents]
        }];
        Employees.superclass.initComponent.call(this);
    }
});

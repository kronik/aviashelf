var Folders = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var books = new Kwf.Auto.GridPanel({
            controllerUrl   : '/books',
            region          : 'center',
            height          : 600,
            resizable       : true,
            split           : true,
            title           : ''
        });

        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/folders',
            region          : 'west',
            width           : 300,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : 'Разделы',
            bindings: [{
                queryParam: 'folderId',
                item: books
            }]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [books]
        }];
        Folders.superclass.initComponent.call(this);
    }
});

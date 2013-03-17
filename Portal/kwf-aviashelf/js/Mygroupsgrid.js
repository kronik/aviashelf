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

Ext.reg('mygroupsgrid', Mygroupsgrid);


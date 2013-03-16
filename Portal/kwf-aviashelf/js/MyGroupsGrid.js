MyGroupsGrid = Ext.extend(ActionGrid,
{
    initComponent: function() {
        MyGroupsGrid.superclass.initComponent.call(this);
    },
                        
    onCustomAction: function(rowId) {
        var params = this.getBaseParams() || {};
        params[this.store.reader.meta.id] = rowId;
        this.collapse();
        this.hide();
    }
});

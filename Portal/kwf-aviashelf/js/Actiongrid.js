ActionGrid = Ext.extend(Kwf.Auto.GridPanel,
{
    initComponent: function() {
        ActionGrid.superclass.initComponent.call(this);
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

Ext.reg('actiongrid', ActionGrid);


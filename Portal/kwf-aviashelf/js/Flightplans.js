var Flightplans = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var flightresults = new Kwf.Auto.GridPanel({
            controllerUrl   : '/flightfullresults',
            collapsible     : true,
            title           : trlKwf('Flight results')
        });

        var flightgroups = new Kwf.Auto.GridPanel({
            controllerUrl   : '/flightgroups',
            collapsible     : true,
            title           : trlKwf('Flight groups')
        });
                             
        var flighttasks = new Kwf.Auto.GridPanel({
           controllerUrl   : '/flights',
           region          : 'center',
           title           : trlKwf('Flights'),
           bindings: [
                         {
                             queryParam: 'flightId',
                             item: flightresults
                         },
                         {
                             queryParam: 'flightId',
                             item: flightgroups
                         }
                     ]
        });

        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/flightplans',
            region          : 'west',
            width           : 300,
            resizable       : true,
            split           : true,
            collapsible     : true,
            bindings: [{
                        queryParam: 'planId',
                        item: flighttasks
                      }],
            title           : trlKwf('Flight plans')
        });
                             
        var flighttabs = new Ext.TabPanel({
             border    : true,
             activeTab : 0,
             region    : 'east',
             width     : 600,
             tabPosition:'top',
             resizable   : true,
             split       : true,
             collapsible : true,
             items:[flightgroups, flightresults]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [flighttasks, flighttabs]
        }];
        Flightplans.superclass.initComponent.call(this);
    }
});
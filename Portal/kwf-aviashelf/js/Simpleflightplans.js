var Simpleflightplans = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {
        var flightgroups = new Kwf.Auto.GridPanel({
            controllerUrl   : '/simpleflightgroups',
            collapsible     : true,
            title           : trlKwf('Flight groups')
        });
                             
        var staffgroups = new Kwf.Auto.GridPanel({
            controllerUrl   : '/simplestaffgroups',
            collapsible     : true,
            title           : trlKwf('Staff groups')
        });
                                   
        var flight = new Kwf.Auto.FormPanelEx({
             controllerUrl   : '/simpleflight',
             collapsible     : true,
             title           : trlKwf('Flight')
        });
                             
        var flighttasks = new Kwf.Auto.GridPanel({
           controllerUrl   : '/simpleflights',
           region          : 'center',
           bindings: [
                         {
                             queryParam: 'id',
                             item: flight
                         },
                         {
                             queryParam: 'flightId',
                             item: flightgroups
                         },
                         {
                             queryParam: 'flightId',
                             item: staffgroups
                         }
                     ]
        });
                             
        var flighttracks = new Kwf.Auto.GridPanel({
              controllerUrl   : '/flighttracks',
              region          : 'center',
              title           : trlKwf('Responsibles')
        });
                             
        var planerstates = new Kwf.Auto.GridPanel({
              controllerUrl   : '/planerstates',
              region          : 'center',
              title           : trlKwf('Planer states')
        });
                                   
        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/simpleflightplans',
            region          : 'west',
            width           : 300,
            resizable       : true,
            split           : true,
            collapsible     : true,
            bindings: [{
                        queryParam: 'planId',
                        item: flighttasks
                      },
                      {
                        queryParam: 'planId',
                        item: flighttracks
                      },
                      {
                        queryParam: 'planId',
                        item: planerstates
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
             items:[flightgroups, staffgroups]
        });
                             
        var panel = new Ext.Panel({
                               title: trlKwf('Flights'),
                               layout:'border',
                               items: [flighttasks, flighttabs]
                               });
                             
        var tabs = new Ext.TabPanel({
           border    : true,
           activeTab : 0,
           region    : 'center',
           tabPosition:'top',
           split       : true,
           items:[panel, planerstates, flighttracks]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [tabs]
        }];
        Simpleflightplans.superclass.initComponent.call(this);
    }
});

Ext.util.Format.flightTimeCorrect = function(val)
{
    if ((val == null) || (val == '')) {
        return 'TBA';
    }

    var time = val.split(':');
    
    return time[0] + ':' + time[1];
};

Ext.util.Format.dateCorrect = function(val)
{
    return val.format('d-m-Y');
};

Ext.util.Format.planerStateColorer = function(val)
{
    if (val == 'исправно' || val == 'Исправно')
    {
        return '<span style="color:green;">' + val + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + val + '</span>';
    }
    return val;
};

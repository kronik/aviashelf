var Myflights = Ext.extend(Ext.Panel,
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
                             
        var staffgroups = new Kwf.Auto.GridPanel({
            controllerUrl   : '/staffgroups',
            collapsible     : true,
            title           : trlKwf('Staff groups')
        });
                           
        var flight = new Kwf.Auto.FormPanel({
             controllerUrl   : '/myflight',
             collapsible     : true,
             title           : trlKwf('Flight')
        });
                           
        var flighttasks = new Kwf.Auto.GridPanel({
           controllerUrl   : '/myflights',
           region          : 'center',
           bindings: [
                         {
                             queryParam: 'id',
                             item: flight
                         },
                         {
                             queryParam: 'flightId',
                             item: flightresults
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
                             
        var tabs = new Ext.TabPanel({
           border          : true,
           activeTab       : 0,
           region          : 'east',
           width           : '40%',
           resizable       : true,
           split           : true,
           collapsible     : true,
           tabPosition     : 'top',
           split           : true,
           items:[flight, flightgroups, staffgroups, flightresults]
        });

        this.layout = 'border';
        this.items = [tabs, {
            layout: 'border',
            region: 'center',
            items: [flighttasks]
        }];
        Myflights.superclass.initComponent.call(this);
    }
});
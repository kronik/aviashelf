var Staffs = Ext.extend(Ext.Panel,
{
    initComponent : function(test)
    {                           
        var documents = new Kwf.Auto.GridPanel({
             controllerUrl   : '/staffdocuments',
             collapsible     : true,
             stripeRows      : true,
             title           : trlKwf('Documents')
        });
        
        var user = new Kwf.Auto.FormPanel({
              controllerUrl   : '/staff',
              collapsible     : true,
              title           : trlKwf('General Info')
        });
                        
//        var accesses = new Kwf.Auto.GridPanel({
//              controllerUrl   : '/flightaccesses',
//              collapsible     : true,
//              title           : trlKwf('Accesses')
//        });
//        
//        var flightresults = new Kwf.Auto.GridPanel({
//              controllerUrl   : '/flightresults',
//              collapsible     : true,
//              title           : trlKwf('Flight results')
//        });
        
        var grid = new Kwf.Auto.GridPanel({
            controllerUrl   : '/staffs',
            region          : 'west',
            width           : 300,
            resizable       : true,
            split           : true,
            collapsible     : true,
            title           : trlKwf('Staff groups'),
            bindings: [{
                queryParam: 'ownerId',
                item: documents
               },
               {
                queryParam: 'id',
                item: user
               
//            },
//            {
//                queryParam: 'employeeId',
//                item: accesses
//            },
//            {
//                queryParam: 'ownerId',
//                item: flightresults
            }]
        });
                           
        var tabs = new Ext.TabPanel({
               border    : true,
               activeTab : 0,
               region    : 'center',
               tabPosition:'top',
               items:[user, documents]//, accesses, flightresults]
        });

        this.layout = 'border';
        this.items = [grid, {
            layout: 'border',
            region: 'center',
            items: [tabs]
        }];
        Staffs.superclass.initComponent.call(this);
    }
});

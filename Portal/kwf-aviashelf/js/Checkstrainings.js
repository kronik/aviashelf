var Checkstrainings = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var form = new Kwf.Auto.FormPanel({
                                         controllerUrl   : '/checktraining',
                                         region          : 'center'
                                         });
              
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/checkstrainings',
                                         region          : 'west',
                                         width           : 300,
                                         resizable       : true,
                                         split           : true,
                                         collapsible     : true,
                                         title           : trlKwf('Trainings checks'),
                                         bindings: [{
                                                    queryParam: 'id',
                                                    item: form
                                                    }]
                                         });
       
       this.layout = 'border';
       this.items = [grid, {
                     layout: 'border',
                     region: 'center',
                     items: [form]
                     }];
       Checkstrainings.superclass.initComponent.call(this);
    }
});

/*
 var Checks = Ext.extend(Ext.Panel,
 {
 initComponent : function(test)
 {
 var formdoc = new Kwf.Auto.FormPanel({
 controllerUrl   : '/checkdoc',
 region          : 'center',
 title           : trlKwf('Documents checks')
 });
 
 var formflight = new Kwf.Auto.FormPanel({
 controllerUrl         : '/checkflight',
 region          : 'center',
 title           : trlKwf('Flights checks')
 });
 var formtraining = new Kwf.Auto.FormPanel({
 controllerUrl           : '/checktraining',
 region          : 'center',
 title           : trlKwf('Trainings checks')
 });
 
 var grid = new Kwf.Auto.GridPanel({
 controllerUrl   : '/checks',
 region          : 'west',
 width           : 300,
 resizable       : true,
 split           : true,
 collapsible     : true,
 title           : trlKwf('Checks'),
 bindings: [{
 queryParam: 'id',
 item: formdoc
 },
 {
 queryParam: 'id',
 item: formflight
 },
 {
 queryParam: 'id',
 item: formtraining
 }]
 });
 
 var tabs = new Ext.TabPanel({
 border    : true,
 activeTab : 0,
 region    : 'center',
 tabPosition:'top',
 items:[formdoc, formflight, formtraining]
 });
 
 this.layout = 'border';
 this.items = [grid, {
 layout: 'border',
 region: 'center',
 items: [tabs]
 }];
 Checks.superclass.initComponent.call(this);
 }
 });
 */


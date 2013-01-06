var Checkresults = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
           var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/checkresults',
                                         region          : 'center',
                                         title           : trlKwf('Check Results')
                                         });
       
       this.layout = 'border';
       this.items = [grid];

       Checkresults.superclass.initComponent.call(this);
    }
});

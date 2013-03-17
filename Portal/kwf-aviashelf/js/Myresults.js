var Myresults = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {

       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/myresults',
                                         region          : 'center',
                                         title           : trlKwf('My results'),
       });
           
       this.layout = 'border';
       this.items = [grid];
       Myresults.superclass.initComponent.call(this);
    }
});
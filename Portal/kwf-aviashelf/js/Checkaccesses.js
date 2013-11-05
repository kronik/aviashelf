var Checkaccesses = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/checkaccesses',
                                         region          : 'center',
                                         title           : 'Летные проверки'
       });
       
       this.layout = 'border';
       this.items = [grid];
       Checkaccesses.superclass.initComponent.call(this);
    }
});

Ext.util.Format.accessCheckDate = function(val)
{
    if ((val == null) || (val == '')) {
        return val;
    }
    
    var month = val.getUTCMonth() + 1;
    var monthStr = month < 10 ? '0' + month : month;
    var day = val.getUTCDate() + 1;
    var dayStr = day < 10 ? '0' + day : day;
    var year = val.getUTCFullYear();
    
    var newdate = dayStr + "-" + monthStr + "-" + year;
    
    var dateToCheck = new Date();
    var today = new Date();
    
    dateToCheck.setDate(dateToCheck.getDate() - 7);
    
    if ((val > today) && (val > dateToCheck))
    {
        return '<span style="color:green;">' + newdate + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + newdate + '</span>';
    }
    return val;
};

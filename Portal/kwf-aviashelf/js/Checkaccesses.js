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
    
    var month = val.getMonth();
    var monthStr = month < 10 ? '0' + month : month;
    var day = val.getDay();
    var dayStr = day < 10 ? '0' + day : day;
    var year = val.getYear();
    
    var newdate = dayStr + "-" + monthStr + "-" + year;
    
    var dateToCheck = new Date();
    var today = new Date();
    
    dateToCheck.setDate(dateToCheck.getDate() - 7);
    
    if ((val > today) && (val > dateToCheck))
    {
        return '<span style="color:green;">' + val.format('d-m-Y') + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + val.format('d-m-Y') + '</span>';
    }
    return val;
    return val;
};


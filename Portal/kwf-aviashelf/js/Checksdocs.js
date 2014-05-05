var Checksdocs = Ext.extend(Ext.Panel,
{
       initComponent : function(test)
       {
       var grid = new Kwf.Auto.GridPanel({
                                         controllerUrl   : '/checksdocs',
                                         region          : 'center',
                                         title           : 'Периодическая подготовка'
                                         });
       
       this.layout = 'border';
       this.items = [grid];
       Checksdocs.superclass.initComponent.call(this);
    }
});

Ext.util.Format.documentsCheckDate = function(val)
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
    
    var dateToCheck1 = new Date();
    
    var today = new Date();
    
    dateToCheck1.setDate(dateToCheck1.getDate() + 30);
    
    if ((val > today) && (val > dateToCheck1)) {
        return '<span style="color:green;">' + val.format('d-m-Y') + '</span>';
    } else if ((val > today) && (val < dateToCheck1)) {
        return '<span style="color:orange;">' + val.format('d-m-Y') + '</span>';
    } else {
        return '<span style="color:red;">' + val.format('d-m-Y') + '</span>';
    }
    return val;
};

Ext.util.Format.documentsCheckGrade = function(val)
{
    if (val == 'зачет' || val == 'пять' || val == 'четыре' || val == 'допущен')
    {
        return '<span style="color:green;">' + val + '</span>';
    }
    else
    {
        return '<span style="color:red;">' + val + '</span>';
    }
    return val;
};



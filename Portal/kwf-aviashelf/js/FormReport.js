/*
Kwf.Auto.FormReportPanel = Ext.extend(Kwf.Auto.FormPanel,
{
    this.actions.report = new Ext.Action(
    {
        text    : trlKwf('Report'),
        icon    : '/assets/silkicons/table_go.png',
        cls     : 'x-btn-text-icon',
        handler : this.onReport,
        scope: this
    });
    Kwf.Auto.FormReportPanel.superclass.initComponent.call(this);

                                      onReport : function()
                                      {
                                      
                                      }
});

Ext.reg('kwf.FormReportPanel', Kwf.Auto.FormReportPanel);
*/
                                      /*
                                onPdf : function()
                                {
                                window.open(this.controllerUrl+'/pdf?'+Ext.urlEncode(this.getStore().baseParams));
                                },
                                onCsv : function()
                                {
                                Ext.Ajax.request({
                                                 url : this.controllerUrl+'/json-csv',
                                                 params  : this.getStore().baseParams,
                                                 timeout: 600000, // 10 minuten
                                                 progress: true,
                                                 progressTitle : trlKwf('CSV export'),
                                                 success: function(response, opt, r) {
                                                 if (Ext.isIE) {
                                                 Ext.Msg.show({
                                                              title: trlKwf('Your download is ready'),
                                                              msg: trlKwf('Please click on the following link to download your CSV file.')
                                                              +'<br /><br />'
                                                              +'<a class="xlsExportLink" href="'+this.controllerUrl+'/download-csv-export-file?downloadkey='+r.downloadkey+'" target="_blank">'
                                                              +trlKwf('CSV export file')+'</a>',
                                                              icon: Ext.Msg.INFO,
                                                              buttons: { ok: trlKwf('Close') }
                                                              });
                                                 } else {
                                                 Ext.getBody().createChild({
                                                                           html: '<iframe width="0" height="0" src="'+this.controllerUrl+'/download-csv-export-file?downloadkey='+r.downloadkey+'"></iframe>'
                                                                           });
                                                 }
                                                 },
                                                 scope: this
                                                 });
                                },
                                onXls : function()
                                {
                                var params = Kwf.clone(this.getStore().baseParams);
                                if(this.getStore().sortInfo){
                                var pn = this.getStore().paramNames;
                                params[pn["sort"]] = this.getStore().sortInfo.field;
                                params[pn["dir"]] = this.getStore().sortInfo.direction;
                                }
                                
                                Ext.Ajax.request({
                                                 url : this.controllerUrl+'/json-xls',
                                                 params  : params,
                                                 timeout: 600000, // 10 minuten
                                                 progress: true,
                                                 progressTitle : trlKwf('Excel export'),
                                                 success: function(response, opt, r) {
                                                 var downloadUrl = this.controllerUrl+'/download-export-file?downloadkey='+r.downloadkey;
                                                 for (var i in params) {
                                                 downloadUrl += '&' + i + '=' + params[i];
                                                 }
                                                 if (Ext.isIE) {
                                                 Ext.Msg.show({
                                                              title: trlKwf('Your download is ready'),
                                                              msg: trlKwf('Please click on the following link to download your Excel file.')
                                                              +'<br /><br />'
                                                              +'<a class="xlsExportLink" href="'+downloadUrl+'" target="_blank">'
                                                              +trlKwf('Excel export file')+'</a>',
                                                              icon: Ext.Msg.INFO,
                                                              buttons: { ok: trlKwf('Close') }
                                                              });
                                                 } else {
                                                 Ext.getBody().createChild({
                                                                           html: '<iframe width="0" height="0" src="'+downloadUrl+'"></iframe>'
                                                                           });
                                                 }
                                                 },
                                                 scope: this
                                                 });
                                }*/



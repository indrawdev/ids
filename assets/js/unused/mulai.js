var isi = Ext.create('Ext.panel.Panel', {
    html: '<p>Welcome to ASTER.<br>This is a web version of the ASTER.<br>ASTER stands for "Aplikasi Sistem Terpadu" (Integrated System Applications),<br>ASTER refers to Accounting Information Systems.<br><br><br>Regards,<br>ASTER Team</p>',
	bodyStyle: 'padding:15px 15px;',
	border: false,
	frame: true
});

Ext.onReady(function(){
    Ext.QuickTips.init();
	
	var winCashBank = Ext.widget('form', {
        closable: false,
        draggable: false,
        height: 200,
		id: 'winMulai',
        layout: 'border',
        plain: true,
		renderTo: Ext.getBody(),
        resizable: false,
        title: 'Welcome',
        width: 500,
        items: [
			isi
		]
	});
});
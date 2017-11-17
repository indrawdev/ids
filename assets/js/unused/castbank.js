var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

var grupdept = new Ext.create('Ext.data.Store',{
	autoLoad: true,
	fields: ['fs_code','fs_nm_code'],
	proxy: {
		type: 'ajax',
		url: 'castbank/cbin_dept',
		actionMethods: 'POST',
		reader: {
			type: 'json'
		}
	}
});

var grupstrx = new Ext.create('Ext.data.Store',{
	autoLoad: true,
	fields: ['fs_kd_strx','fs_nm_strx'],
	proxy: {
		type: 'ajax',
		url: 'castbank/cbin_trs',
		actionMethods: 'POST',
		reader: {
			type: 'json'
		}
	}
});

var gruprefno = new Ext.create('Ext.data.Store',{
	autoLoad: true,
	fields: ['fs_refno','fs_nm_dept'],
	proxy: {
		type: 'ajax',
		url: 'castbank/cbin_refno',
		actionMethods: 'POST',
		reader: {
			type: 'json'
		}
	}
});

var frmCastBank = new Ext.tab.Panel({
	//url: 'login/ceklogin',
	method: 'POST',
	id: 'formCastBank',
	bodyStyle: 'padding:15px 35px;',
	frame: false,
	border: false,
	region: 'center',
	defaultType: 'textfield',
	xtype: 'tabpanel',
	items: [{
		title: 'Entry Cash Bank In',
		xtype: 'form',
		border: false,
		frame: false,
		items: [{
			xtype: 'fieldset',
			//layout: 'form',
			title: 'Header',
			items: [{
				xtype: 'combobox',
				editable: false,
				id: 'cbodept',
				name: 'cbodept',
				queryMode: 'remote',
				valueField: 'fs_code',
				displayField: 'fs_nm_code',
				width: 378,
				fieldLabel: 'Department',
				emptyText: 'Select a Department',
				afterLabelTextTpl: required,
				allowBlank: false,
				triggerAction: 'all',
				//anchor: '50%',
				store: grupdept
			},{
				xtype: 'container',
				anchor: '100%',
				layout: 'hbox',
				items: [{
					xtype: 'container',
					flex: 1,
					layout: 'anchor',
					items: [{
						xtype: 'combobox',
						editable: false,
						id: 'cbotrs',
						name: 'cbotrs',
						queryMode: 'remote',
						valueField: 'fs_kd_strx',
						displayField: 'fs_nm_strx',
						//width: 350,
						fieldLabel: 'Trans Type',
						emptyText: 'Select a Trans Type',
						afterLabelTextTpl: required,
						allowBlank: false,
						triggerAction: 'all',
						anchor: '95%',
						store: grupstrx,
						listeners: {
							change: function(combo, value) {
								var xcborefno = Ext.getCmp('cborefno');
								xcborefno.clearValue();
								gruprefno.load({
									params: {
										fs_kd_strx: value
									}
								});
							}
						}
					},{
						xtype: 'combobox',
						editable: false,
						id: 'cborefno',
						name: 'cborefno',
						queryMode: 'local',
						valueField: 'fs_refno',
						displayField: 'fs_nm_dept',
						//width: 350,
						fieldLabel: 'Ref. No',
						emptyText: 'AUTOMATIC',
						afterLabelTextTpl: required,
						//allowBlank: false,
						triggerAction: 'all',
						anchor: '95%',
						store: gruprefno
					},{
						xtype: 'textfield',
						fieldLabel: 'Document No',
						name: 'docno',
						id: 'docno',
						allowBlank: true,
						anchor: '95%'
					}]
				},{
					xtype: 'container',
					flex: 1,
					layout: 'anchor',
					items: [{
						xtype: 'combobox',
						editable: false,
						id: 'cbodraft',
						name: 'cbodraft',
						//queryMode: 'local',
						//valueField: 'fs_code',
						//displayField: 'fs_nm_code',
						width: 250,
						fieldLabel: 'Status',
						emptyText: 'DRAFT',
						afterLabelTextTpl: required,
						allowBlank: false,
						triggerAction: 'all',
						anchor: '95%'/*,
						store: grupdept*/
					},{
						xtype: 'datefield',
						fieldLabel: 'Ref. Date',
						afterLabelTextTpl: required,
						name: 'refdate',
						allowBlank: false,
						anchor: '95%'
						//value: 'Griffin'
					},{
						xtype: 'datefield',
						fieldLabel: 'Doc. Date',
						afterLabelTextTpl: required,
						name: 'docdate',
						allowBlank: false,
						anchor: '95%'
					}]
				}]
			},{
				xtype: 'textarea',
				fieldLabel: 'Note',
				width: 378/*,
				anchor: '100%'*/
			}]
		},{
			xtype: 'fieldset',
			//layout: 'form',
			title: 'Detail'
		}],
		
		buttons: [{
			text: 'Save'
		},{
			text: 'Reset',
			handler: function(){
				frmCastBank.getForm().reset();
			}
		}]
	},{
		title: 'Entry Cash Bank Out',
		xtype: 'form',
		border: false,
		frame: false
	}]/*,
	renderTo: Ext.getBody()*/
});

Ext.onReady(function(){
    Ext.QuickTips.init();
	
	var winCastBank = new Ext.Window({
        layout: 'border',
        title: 'Cast Bank Form',
		id: 'winCastBank',
        draggable: false,
        height: 500,
        minHeight: 500,
        minWidth: 900,
        width: 900,
        plain: true,
        closable: false,
        resizable: false,
        items: [frmCastBank]
        });
    winCastBank.show();
});
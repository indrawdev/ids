Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.form.NumericField',
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

Ext.onReady(function() {
    Ext.QuickTips.init();
	Ext.util.Format.thousandSeparator = ',';
	Ext.util.Format.decimalSeparator = '.';

	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	function gridTooltipCust(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Single click on the customer item in the list to display register',
			target: view.el,
			trackMouse: true
		});
	}

	Ext.define('DataGridCust', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_kd_cust', type: 'string'},
			{name: 'fs_count', type: 'string'},
			{name: 'fs_nm_cust', type: 'string'},
			{name: 'fs_alamat', type: 'string'},
			{name: 'fs_nm_product', type: 'string'},
			{name: 'fs_rangka', type: 'string'},
			{name: 'fs_mesin', type: 'string'},
			{name: 'fs_stnk', type: 'string'},
			{name: 'fd_stnk', type: 'string'},
			{name: 'fs_bpkb', type: 'string'},
			{name: 'fd_bpkb', type: 'string'},
			{name: 'fs_nopol', type: 'string'},
			{name: 'fs_note', type: 'string'},
			{name: 'fs_nm_stnk_qq', type: 'string'},
			{name: 'fs_almt_stnk_qq', type: 'string'},
			{name: 'fs_nm_bpkb_qq', type: 'string'},
			{name: 'fs_almt_bpkb_qq', type: 'string'}
		]
	});

	var grupGridCust = Ext.create('Ext.data.Store', {
		autoLoad: true,
		model: 'DataGridCust',
		pageSize: 25,
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'statusfaktur/cust_list'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_cari': Ext.getCmp('txtCari').getValue()
				});
			}
		}
	});

	var gridCust = Ext.create('Ext.grid.Panel', {
		defaultType: 'textfield',
		height: 300,
		sortableColumns: false,
		store: grupGridCust,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridCust
		}),
		columns: [{
			xtype: 'rownumberer',
			width: 45
		},{
			text: 'Cust Cd',
			dataIndex: 'fs_kd_cust',
			hidden: true,
			locked: true,
			menuDisabled: true
		},{
			text: 'Count',
			dataIndex: 'fs_count',
			hidden: true,
			locked: true,
			menuDisabled: true
		},{
			text: 'Customer',
			dataIndex: 'fs_nm_cust',
			locked: true,
			menuDisabled: true,
			width: 200
		},{
			text: 'Address',
			dataIndex: 'fs_alamat',
			menuDisabled: true,
			width: 250
		},{
			text: 'Product',
			dataIndex: 'fs_nm_product',
			menuDisabled: true,
			width: 100
		},{
			text: 'Chassis',
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			width: 125
		},{
			text: 'Machine',
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			width: 100
		}],
		listeners: {
			itemclick: function(grid, record) {
				Ext.getCmp('txtCust').setValue(record.get('fs_nm_cust'));
				Ext.getCmp('txtRangka').setValue(record.get('fs_rangka'));
				Ext.getCmp('txtMesin').setValue(record.get('fs_mesin'));
			}
		},
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Type customer / chassis / machine',
				id: 'txtCari',
				name: 'txtCari',
				xtype: 'textfield'
			}]
		},{
			flex: 0.2,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '100%',
				text: 'Search',
				xtype: 'button',
				handler: function() {
					grupGridCust.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipCust
			},
			markDirty: false,
			stripeRows: true
		}
	});

	Ext.define('DataGridDetil', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_refno', type: 'string'},
			{name: 'fs_chasis', type: 'string'},
			{name: 'fs_engine', type: 'string'}
		]
	});

	var grupGridDetil = Ext.create('Ext.data.Store', {
		autoLoad: true,
		model: 'DataGridDetil',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				rootProperty: 'hasil',
				totalProperty: 'total',
				type: 'json'
			},
			type: 'ajax',
			url: 'statusfaktur/griddetil'
		},
	});

	var gridDetil = Ext.create('Ext.grid.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		sortableColumns: false,
		store: grupGridDetil,
		columns: [{
			xtype: 'rownumberer',
			width: 35
		},{
			dataIndex: 'fs_refno',
			menuDisabled: true,
			text: 'Ref. No',
			width: 200
		},{
			dataIndex: 'fd_refno',
			menuDisabled: true,
			text: 'Ref. Date',
			width: 150
		},{
			dataIndex: 'fs_nm_cust',
			menuDisabled: true,
			text: 'Cust',
			width: 150
		},{
			dataIndex: 'fs_rangka',
			menuDisabled: true,
			text: 'Chassis',
			width: 100
		},{
			dataIndex: 'fs_mesin',
			menuDisabled: true,
			text: 'Engine',
			width: 100
		},{
			dataIndex: 'fs_status',
			menuDisabled: true,
			text: 'Status',
			width: 100
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupGridDetil
		}),
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}

	});
	
	var txtCust = {
		anchor: '95%',
		fieldLabel: 'Customer',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtCust',
		name: 'txtCust',
		readOnly: true,
		xtype: 'textfield'
	}

	var txtRangka = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		fieldLabel: 'Chassis',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtRangka',
		name: 'txtRangka',
		readOnly: true,
		xtype: 'textfield'
	};
	
	var txtMesin = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		fieldLabel: 'Machine',
		fieldStyle: 'background-color: #eee; background-image: none;',
		id: 'txtMesin',
		name: 'txtMesin',
		readOnly: true,
		xtype: 'textfield'
	};
	
	
	var dataStatus = Ext.create('Ext.data.Store', {
	    fields: ['id', 'name'],
	    	data: [
	    		{"id":"2", "name":"MASIH PROSES"},
	    		{"id":"3", "name":"SUDAH SELESAI"}
	    	]
	});

	var cboStatus = Ext.create('Ext.form.ComboBox', {
		afterLabelTextTpl: required,
		fieldLabel: 'Status',
		anchor: '80%',
		store: dataStatus,
		queryMode: 'local',
		displayField: 'name',
		id: 'cboStatus',
		name: 'cboStatus',
		valueField: 'id',
		emptyText : 'Pilih Status',
	});

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'statusfaktur/ceksave',
				params: {
					'fs_rangka': Ext.getCmp('txtRangka').getValue(),
					'fs_mesin': Ext.getCmp('txtMesin').getValue(),
					'fs_status': Ext.getCmp('cboStatus').getValue()
				},
				success: function(response) {
					var xtext = Ext.decode(response.responseText);
					
					if (xtext.sukses === false) {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK,
							closable: false,
							icon: Ext.MessageBox.INFO,
							msg: xtext.hasil,
							title: 'IDS'
						});
					}
					else {
						if (xtext.sukses === true && xtext.hasil == 'lanjut') {
							fnSave();
						}
						else {
							Ext.MessageBox.show({
								buttons: Ext.MessageBox.YESNO,
								closable: false,
								icon: Ext.Msg.QUESTION,
								msg: xtext.hasil,
								title: 'IDS',
								fn: function(btn) {
									if (btn == 'yes') {
										fnSave();
									}
								}
							});
						}
					}
				},
				failure: function(response) {
					var xtext = Ext.decode(response.responseText);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: 'Saving Failed, Connection Failed!!',
						title: 'IDS'
					});
					fnMaskHide();
				}
			});
		}
	}

	function fnSave() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'statusfaktur/save',
			params: {
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue(),
				'fs_status': Ext.getCmp('cboStatus').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});
				if (xtext.sukses === true) {
					fnReset();
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnCekRemove() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'statusfaktur/cekremove',
			params: {
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				if (xtext.sukses === false) {
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});
				}
				else {
					if (xtext.sukses === true && xtext.hasil == 'lanjut') {
						fnRemove();
					}
					else {
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.YESNO,
							closable: false,
							icon: Ext.Msg.QUESTION,
							msg: xtext.hasil,
							title: 'IDS',
							fn: function(btn) {
								if (btn == 'yes') {
									fnRemove();
								}
							}
						});
					}
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Saving Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnRemove() {
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'statusfaktur/remove',
			params: {
				'fs_rangka': Ext.getCmp('txtRangka').getValue(),
				'fs_mesin': Ext.getCmp('txtMesin').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});
				if (xtext.sukses === true) {
					fnReset();
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Remove Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnReset() {
		Ext.getCmp('txtCust').setValue('');
		Ext.getCmp('txtRangka').setValue('');
		Ext.getCmp('txtMesin').setValue('');
		Ext.getCmp('cboStatus').setValue('');
		
		grupGridCust.removeAll();
		grupGridDetil.removeAll();
		gridCust.getView().refresh();
		gridDetil.getView().refresh();
		grupGridCust.load();
		grupGridDetil.load();
	}

	var frmStatusFaktur = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Status Faktur Form',
		width: 950,
		items: [{
			activeTab: 0,
			bodyStyle: 'padding: 5px; background-color: '.concat(gBasePanel),
			border: false,
			plain: true,
			xtype: 'tabpanel',
			items: [{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Request',
				xtype: 'form',
				items: [{
					style: 'padding: 5px;',
					title: 'Customer List',
					xtype: 'fieldset',
					items: [{
						anchor: '100%',
						items: [
							gridCust
						]
					}]
				},{
					style: 'padding: 5px;',
					title: 'Register Details',
					xtype: 'fieldset',
						items: [{
							anchor: '100%',
							layout: 'hbox',
							xtype: 'container',
							items: [{
								flex: 2,
								layout: 'anchor',
								xtype: 'container',
								items: [{
									anchor: '95%',
									layout: 'hbox',
									xtype: 'container',
									items: [{
										flex: 2,
										layout: 'anchor',
										xtype: 'container',
										items: [
											txtCust,
											txtRangka,
											txtMesin
										]
									}]
								}]
							},{
								flex: 1,
								layout: 'anchor',
								xtype: 'container',
								items: [
									cboStatus
								]
							}]
						}]
				}],
				buttons: [{
					text: 'Save',
					handler: fnCekSave
				},{
					text: 'Reset',
					handler: fnReset
				},{
					text: 'Remove',
					handler: fnCekRemove
				}]
			}, {
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'Data Status Faktur',
				xtype: 'form',
				items: [
					gridDetil
				]
			}]
		}]

	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmStatusFaktur
	});
	
	function fnMaskShow() {
		frmStatusFaktur.mask('Please wait...');
	}

	function fnMaskHide() {
		frmStatusFaktur.unmask();
	}
	
	frmStatusFaktur.render(Ext.getBody());
	Ext.get('loading').destroy();


});
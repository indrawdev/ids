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

	function zeroPad(num, places) {
		var zero = places - num.toString().length + 1;
		return Array(+(zero > 0 && zero)).join('0') + num;
	}

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_agen','fs_nm_agen','fs_tlp',
			'fs_alamat','fb_aktif','fs_status'
		],
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
			url: 'biro/kodeagen'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_agen': Ext.getCmp('cboAgen').getValue(),
					'fs_nm_agen': Ext.getCmp('cboAgen').getValue(),
					'fs_cari': Ext.getCmp('txtCari').getValue()
				});
			}
		}
	});
	
	var winGrid = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupRefno,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Agen Cd", dataIndex: 'fs_kd_agen', menuDisabled: true, width: 100},
			{text: "Agen", dataIndex: 'fs_nm_agen', menuDisabled: true, width: 150},
			{text: "Phone", dataIndex: 'fs_tlp', menuDisabled: true, width: 150},
			{text: "Address", dataIndex: 'fs_alamat', menuDisabled: true, hidden: true},
			{text: "Active", dataIndex: 'fb_aktif', menuDisabled: true, hidden: true},
			{text: "Active", dataIndex: 'fs_status', menuDisabled: true, width: 80}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Agen Cd / Agen',
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
					grupRefno.load();
				}
			}]
		},{
			flex: 0.5,
			layout: 'anchor',
			xtype: 'container',
			items: []
		}],
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupRefno,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboAgen').setValue(record.get('fs_kd_agen'));
				Ext.getCmp('cekAktif').setValue(record.get('fb_aktif'));
				Ext.getCmp('txtAgen').setValue(record.get('fs_nm_agen'));
				Ext.getCmp('txtTlp').setValue(record.get('fs_tlp'));
				Ext.getCmp('txtAlamat').setValue(record.get('fs_alamat'));
				
				winCari.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupRefno.load();
				vMask.show();
			}
		}
	});

	var cboAgen = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		emptyText: 'Enter an Agen Code',
		fieldLabel: 'Agent Code',
		id: 'cboAgen',
		name: 'cboAgen',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtAgen').setValue('');
			}
		},
		triggers: {
			reset: {
				cls: 'x-form-clear-trigger',
				handler: function(field) {
					field.setValue('');
				}
			},
			cari: {
				cls: 'x-form-search-trigger',
				handler: function() {
					winCari.show();
					winCari.center();
				}
			}
		}
	};

	var cekAktif = {
		boxLabel: 'Active',
		checked: true,
		id: 'cekAktif',
		name: 'cekAktif',
		xtype: 'checkboxfield'
	};

	var txtAgen = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '100%',
		emptyText: 'Enter an Agen Name',
		fieldLabel: 'Agent Name',
		id: 'txtAgen',
		name: 'txtAgen',
		xtype: 'textfield'
	};

	var txtTlp = {
		anchor: '100%',
		emptyText: 'Enter an Agen Phone',
		fieldLabel: 'Phone',
		id: 'txtTlp',
		name: 'txtTlp',
		xtype: 'textfield'
	};

	var txtAlamat = {
		anchor: '100%',
		emptyText: 'Enter an Agen Address',
		fieldLabel: 'Address',
		grow: true,
		growMin: 35,
		growMax: 35,
		id: 'txtAlamat',
		name: 'txtAlamat',
		xtype: 'textareafield'
	};

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'biro/ceksave',
				params: {
					'fs_kd_agen': Ext.getCmp('cboAgen').getValue()
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
			url: 'biro/save',
			params: {
				'fs_kd_agen': Ext.getCmp('cboAgen').getValue(),
				'fb_aktif': Ext.getCmp('cekAktif').getValue(),
				'fs_nm_agen': Ext.getCmp('txtAgen').getValue(),
				'fs_tlp': Ext.getCmp('txtTlp').getValue(),
				'fs_alamat': Ext.getCmp('txtAlamat').getValue()
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

	function fnPrint() {
		// Ext.Ajax.on('beforerequest', fnMaskShow);
		// Ext.Ajax.on('requestcomplete', fnMaskHide);
		// Ext.Ajax.on('requestexception', fnMaskHide);
		
		// Ext.Ajax.request({
			// method: 'POST',
			// timeout: 60000,
			// url: 'jual/printsrtpernyataan',
			// params: {
				// 'fs_refno': Ext.getCmp('txtRefno').getValue(),
				// 'fs_nama': Ext.getCmp('txtNama').getValue()
			// },
			// success: function(response) {
				// var xtext = Ext.decode(response.responseText);
				// /*
				// Ext.MessageBox.show({
					// buttons: Ext.MessageBox.OK,
					// closable: false,
					// icon: Ext.MessageBox.INFO,
					// msg: xtext.hasil,
					// title: 'IDS'
				// });*/
				
				// if (xtext.sukses === true) {
					// var xfile = xtext.nmfile.trim();
					
					// var winpdf = Ext.create('Ext.window.Window', {
						// closable: false,
						// draggable: false,
						// height: 500,
						// layout: 'fit',
						// title: 'Agreement Letter',
						// width: 900,
						// items: {
							// xtype: 'component',
							// autoEl: {
								// src: xfile,
								// tag: 'iframe'
							// }
						// },
						// buttons: [{
							// text: 'Exit',
							// handler: function() {
								// winpdf.hide();
							// }
						// }]
					// }).show();
				// }
				// else {
					// Ext.MessageBox.show({
						// buttons: Ext.MessageBox.OK,
						// closable: false,
						// icon: Ext.MessageBox.INFO,
						// msg: xtext.hasil,
						// title: 'IDS'
					// });				
				// }
			// },
			// failure: function(response) {
				// var xtext = Ext.decode(response.responseText);
				// Ext.MessageBox.show({
					// buttons: Ext.MessageBox.OK,
					// closable: false,
					// icon: Ext.MessageBox.INFO,
					// msg: 'Print Failed, Connection Failed!!',
					// title: 'IDS'
				// });
				// fnMaskHide();
			// }
		// });
	}

	function fnReset() {
		Ext.getCmp('cboAgen').setValue('');
		Ext.getCmp('cekAktif').setValue(true);
		Ext.getCmp('txtAgen').setValue('');
		Ext.getCmp('txtTlp').setValue('');
		Ext.getCmp('txtAlamat').setValue('');
	}

	var frmBiro = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Service Agent Master Form',
		width: 550,
		fieldDefaults: {
			labelAlign: 'right',
			labelSeparator: '',
			labelWidth: 80,
			msgTarget: 'side'
		},
		items: [{
			style: 'padding: 5px;',
			xtype: 'fieldset',
			items: [{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 8,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cboAgen
					]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [
						cekAktif
					]
				}]
			},
				txtAgen,
				txtTlp,
				txtAlamat
			]
		}],
		buttons: [{
			text: 'Save',
			handler: fnCekSave
		},/*{
			text: 'Print',
			handler: fnReset
		},*/{
			text: 'Reset',
			handler: fnReset
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmBiro
	});
	
	function fnMaskShow() {
		frmBiro.mask('Please wait...');
	}

	function fnMaskHide() {
		frmBiro.unmask();
	}
	
	frmBiro.render(Ext.getBody());
	Ext.get('loading').destroy();
});

Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX);

Ext.require([
	'Ext.ux.LiveSearchGridPanel',
	'Ext.ux.ProgressBarPager'
]);

Ext.onReady(function() {
    Ext.QuickTips.init();
	Ext.util.Format.thousandSeparator = ',';
	Ext.util.Format.decimalSeparator = '.';

	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

	function tglDMY(text) {
		var x = '-';
		return text.substr(6,2).concat(x,text.substr(4,2),x,text.substr(0,4));
	}

	function gridTooltipSearch(view) {
		view.tip = Ext.create('Ext.tip.ToolTip', {
			delegate: view.itemSelector,
			html: 'Double click on record to choose',
			target: view.el,
			trackMouse: true
		});
	}

	var grupDept = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: ['fs_code','fs_count','fs_nm_code'],
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
			url: 'cashbank/cbin_dept'
		}
	});

	var winGrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupDept,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupDept,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Dept Code", dataIndex: 'fs_code', menuDisabled: true, hidden: true},
			{text: "Dept Count", dataIndex: 'fs_count', menuDisabled: true, hidden: true},
			{text: "Dept Name", dataIndex: 'fs_nm_code', menuDisabled: true, width: 500}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboDept').setValue(record.get('fs_nm_code'));
				Ext.getCmp('txtDept').setValue(record.get('fs_code'));
				Ext.getCmp('txtCount').setValue(record.get('fs_count'));
				
				winCari.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari.add(winGrid);

	Ext.define('Ext.ux.SearchDept', {
		alias: 'widget.searchDept',
		extend: 'Ext.form.field.Trigger',
		initComponent: function() {
			this.callParent(arguments);
			this.on('specialkey', function(f, e) {
				if (e.getKey() == e.ENTER) {
					this.onTriggerClick();
				}
			}, this);
		},
		onTriggerClick: function() {
			grupDept.load();
			winGrid.show();
			winCari.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	var cboDept = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '50%',
		displayField: 'fs_nm_code',
		editable: false,
		emptyText: 'Select a Department',
		fieldLabel: 'Department',
		id: 'cboDept',
		name: 'cboDept',
		value: vDept,
		valueField: 'fs_code',
		xtype: 'searchDept',
		listeners: {
			change: function(combo, value) {
				fnResetIn();
			}
		}
	};

	var txtDept = {
		anchor: '50%',
		fieldLabel: 'Dept Code',
		hidden: true,
		id: 'txtDept',
		name: 'txtDept',
		value: vDeptCd,
		xtype: 'textfield',
		listeners: {
			change: function(combo, value) {
				grupRefno.load();
				fnResetIn();
			}
		}
	};

	var txtCount = {
		anchor: '50%',
		fieldLabel: 'Dept Count',
		hidden: true,
		id: 'txtCount',
		name: 'txtCount',
		value: vCount,
		xtype: 'textfield'
	};

	var grupRefno = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_nm_dept','fs_refno'
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
			url: 'infojurnal/refno_jurnal'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_dept': Ext.getCmp('txtDept').getValue(),
					'fs_count': Ext.getCmp('txtCount').getValue()
				});
			}
		}
	});

	var winGrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupRefno,
		bbar: Ext.create('Ext.PagingToolbar', {
			displayInfo: true,
			pageSize: 25,
			plugins: Ext.create('Ext.ux.ProgressBarPager', {}),
			store: grupRefno,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari2.hide();
				}
			}]
		}),
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Dept Name", dataIndex: 'fs_nm_dept', menuDisabled: true, width: 200},
			{text: "Ref No", dataIndex: 'fs_refno', menuDisabled: true, width: 150}
		],
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboDeptIn').setValue(record.get('fs_nm_dept'));
				Ext.getCmp('txtDeptIn').setValue(record.get('fs_kd_dept'));
				Ext.getCmp('txtCountIn').setValue(record.get('fs_count'));
				Ext.getCmp('cboTrsIn').setValue(record.get('fs_kd_strx'));
				Ext.getCmp('cboStatusIn').setValue(record.get('fs_kd_status'));
				Ext.getCmp('cboRefno').setValue(record.get('fs_refno'));
				Ext.getCmp('txtDocnoIn').setValue(record.get('fs_doc'));
				Ext.getCmp('txtNoteIn').setValue(record.get('fs_descrp'));
				Ext.getCmp('txtRefnodtIn').setValue(tglDMY(record.get('fd_refno')));
				Ext.getCmp('txtDocnodtIn').setValue(tglDMY(record.get('fd_doc')));
				
				grupGridIn.load();
				winCari2.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			listeners: {
				render: gridTooltipSearch
			}
		}
	});

	var winCari2 = Ext.create('Ext.menu.Menu', {
		bodyPadding: '5 0 5 0',
		plain: true
	});
	winCari2.add(winGrid2);

	Ext.define('Ext.ux.form.SearchRefno', {
		alias: 'widget.searchRefno',
		extend: 'Ext.form.field.Trigger',
		initComponent: function() {
			this.callParent(arguments);
			this.on('specialkey', function(f, e) {
				if (e.getKey() == e.ENTER) {
					this.onTriggerClick();
				}
			}, this);
		},
		onTriggerClick: function() {
			grupRefno.load();
			winGrid2.show();
			winCari2.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
		}
	});

	var cboRefno = {
		anchor: '50%',
		editable: true,
		fieldLabel: 'From Ref. No',
		id: 'cboRefno',
		name: 'cboRefno',
		xtype: 'searchRefno'
	};

	var cboRefno2 = {
		anchor: '50%',
		editable: true,
		fieldLabel: 'To Ref. No',
		id: 'cboRefno2',
		name: 'cboRefno2',
		xtype: 'searchRefno'
	};

	function fnReset() {
		Ext.getCmp('cboWH').setValue('');
		Ext.getCmp('txtWH').setValue('');
		Ext.getCmp('cboProduct').setValue('');
		Ext.getCmp('txtProduct').setValue('');
	}

	function fnReset2() {
		Ext.getCmp('cboDept').setValue('');
		Ext.getCmp('txtDept').setValue('');
		Ext.getCmp('txtCount').setValue('');
		// Ext.getCmp('txtDept').setValue('');
		// Ext.getCmp('cboWH2').setValue('');
		// Ext.getCmp('txtWH2').setValue('');
		// Ext.getCmp('cboProduct2').setValue('');
		// Ext.getCmp('txtProduct2').setValue('');
		// Ext.getCmp('cboCasis').setValue('');
		// Ext.getCmp('txtDate').setValue(new Date());
	}

	function fnReset3() {
		Ext.getCmp('cboWH3').setValue('');
		Ext.getCmp('txtWH3').setValue('');
		Ext.getCmp('cboProduct3').setValue('');
		Ext.getCmp('txtProduct3').setValue('');
	}

	var pbar = Ext.create('Ext.ProgressBar', {
		id: 'pbar',
		width: 600
	}).hide();

	function fnPB() {
		pbar.wait({
			duration: 60000,
			increment: 600,
			interval: 100,
			scope: this,
			fn: function() {
				pbar.hide();
				pbar.reset(true);
			}
		}).show();
	}

	var pbar2 = Ext.create('Ext.ProgressBar', {
		id: 'pbar2',
		width: 600
	}).hide();

	function fnPB2() {
		pbar2.wait({
			duration: 60000,
			increment: 600,
			interval: 100,
			scope: this,
			fn: function() {
				pbar2.hide();
				pbar2.reset(true);
			}
		}).show();
	}

	var pbar3 = Ext.create('Ext.ProgressBar', {
		id: 'pbar3',
		width: 600
	}).hide();

	function fnPB3() {
		pbar3.wait({
			duration: 60000,
			increment: 600,
			interval: 100,
			scope: this,
			fn: function() {
				pbar3.hide();
				pbar3.reset(true);
			}
		}).show();
	}

	function fnPrint() {
		fnPB();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'regstatus/printreg',
			params: {
				'fs_kd_wh': Ext.getCmp('cboWH').getValue(),
				'fs_kd_product': Ext.getCmp('cboProduct').getValue(),
				'fd_tgl': tglDMY(Ext.Date.format(new Date(), 'Ymd'))
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = xtext.nmfile.trim();
					
					var winpdf = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Regstatus',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf.hide();
							}
						}]
					}).show();
					pbar.hide();
					pbar.reset(true);
				}
				else {
					pbar.hide();
					pbar.reset(true);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnPrint2() {
		fnPB2();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'regstatus/printreg2',
			params: {
				'fs_kd_dept': Ext.getCmp('txtDeptCd').getValue(),
				'fs_count': Ext.getCmp('txtCount').getValue(),
				'fs_kd_wh': Ext.getCmp('cboWH2').getValue(),
				'fs_kd_product': Ext.getCmp('cboProduct2').getValue(),
				'fs_rangka': Ext.getCmp('cboCasis').getValue(),
				'fd_tgl': tglDMY(Ext.Date.format(new Date(), 'Ymd')),
				'fd_tgl2': Ext.Date.format(Ext.getCmp('txtDate').getValue(), 'Ymd')
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = xtext.nmfile.trim();
					
					var winpdf2 = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Regstatus Per Date',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf2.hide();
							}
						}]
					}).show();
					pbar2.hide();
					pbar2.reset(true);
				}
				else {
					pbar2.hide();
					pbar2.reset(true);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	function fnPrint3() {
		fnPB3();
		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			timeout: 60000,
			url: 'regstatus/printsum',
			params: {
				'fs_kd_wh': Ext.getCmp('cboWH3').getValue(),
				'fs_kd_product': Ext.getCmp('cboProduct3').getValue()
			},
			success: function(response) {
				var xtext = Ext.decode(response.responseText);
				/*
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: xtext.hasil,
					title: 'IDS'
				});*/
				
				if (xtext.sukses === true) {
					var xfile = xtext.nmfile.trim();
					
					var winpdf3 = Ext.create('Ext.window.Window', {
						closable: false,
						draggable: false,
						height: 500,
						layout: 'fit',
						title: 'Regstatus',
						width: 900,
						items: {
							xtype: 'component',
							autoEl: {
								src: xfile,
								tag: 'iframe'
							}
						},
						buttons: [{
							text: 'Exit',
							handler: function() {
								winpdf3.hide();
							}
						}]
					}).show();
					pbar3.hide();
					pbar3.reset(true);
				}
				else {
					pbar3.hide();
					pbar3.reset(true);
					Ext.MessageBox.show({
						buttons: Ext.MessageBox.OK,
						closable: false,
						icon: Ext.MessageBox.INFO,
						msg: xtext.hasil,
						title: 'IDS'
					});				
				}
			},
			failure: function(response) {
				var xtext = Ext.decode(response.responseText);
				Ext.MessageBox.show({
					buttons: Ext.MessageBox.OK,
					closable: false,
					icon: Ext.MessageBox.INFO,
					msg: 'Print Failed, Connection Failed!!',
					title: 'IDS'
				});
				fnMaskHide();
			}
		});
	}

	var frmInfoJurnal = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'Journal Report Form',
		width: 645,
		items: [{
			activeTab: 1,
			bodyStyle: 'padding: 5px;',
			plain: true,
			xtype: 'tabpanel',
			items: [{
				border: false,
				frame: true,
				title: 'Journal Report',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 80,
						msgTarget: 'side'
					},
					style: 'padding: 5px;',
					xtype: 'fieldset',
					items: [{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: []
					}]
				}],
				buttons: [{
					text: 'Print',
					handler: fnPrint
				},{
					text: 'Reset',
					handler: fnReset
				}],
				bbar: Ext.create('Ext.toolbar.Toolbar', {
					items: [pbar]
				})
			},{
				border: false,
				frame: true,
				title: 'Daily Journal Report',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 80,
						msgTarget: 'side'
					},
					style: 'padding: 5px;',
					xtype: 'fieldset',
					items: [
						cboDept,
						txtDept,
						txtCount,
						cboRefno,
						cboRefno2
					]
				}],
				buttons: [{
					text: 'Print',
					handler: fnPrint2
				},{
					text: 'Reset',
					handler: fnReset2
				}],
				bbar: Ext.create('Ext.toolbar.Toolbar', {
					items: [pbar2]
				})
			},{
				border: false,
				frame: true,
				title: 'General Ledger',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 80,
						msgTarget: 'side'
					},
					style: 'padding: 5px;',
					xtype: 'fieldset',
					items: [{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: []
					}]
				}],
				buttons: [{
					text: 'Print',
					handler: fnPrint3
				},{
					text: 'Reset',
					handler: fnReset3
				}],
				bbar: Ext.create('Ext.toolbar.Toolbar', {
					items: [pbar3]
				})
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmInfoJurnal
	});
	
	function fnMaskShow() {
		frmInfoJurnal.mask('Please wait...');
	}

	function fnMaskHide() {
		frmInfoJurnal.unmask();
	}
	
	frmInfoJurnal.render(Ext.getBody());
	Ext.get('loading').destroy();
});

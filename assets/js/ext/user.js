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

	var vLevel = '';
	var grupUser = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_kd_user','fs_nm_user','fs_password',
			'fs_level','fb_part','fs_status'
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
			url: 'user/kodeuser'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_kd_user': Ext.getCmp('cboUser').getValue(),
					'fs_nm_user': Ext.getCmp('cboUser').getValue(),
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
		store: grupUser,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "User Cd", dataIndex: 'fs_kd_user', menuDisabled: true, width: 100},
			{text: "User", dataIndex: 'fs_nm_user', menuDisabled: true, width: 280},
			{text: "Password", dataIndex: 'fs_password', menuDisabled: true, hidden: true},
			{text: "Level", dataIndex: 'fs_level', menuDisabled: true, hidden: true},
			{text: "Part", dataIndex: 'fb_part', menuDisabled: true, hidden: true},
			{text: "Status", dataIndex: 'fs_status', menuDisabled: true, width: 100}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'User Cd / User',
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
					grupUser.load();
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
			store: grupUser,
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
				Ext.getCmp('cboUser').setValue(record.get('fs_kd_user'));
				Ext.getCmp('cekPart').setValue(record.get('fb_part'));
				Ext.getCmp('txtUser').setValue(record.get('fs_nm_user'));
				Ext.getCmp('txtPassword').setValue(record.get('fs_password'));
				Ext.getCmp('txtPassword2').setValue(record.get('fs_password'));
				Ext.getCmp('cboLevel').setValue(record.get('fs_level'));
				
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
				grupUser.load();
				vMask.show();
			}
		}
	});

	var cboUser = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '95%',
		emptyText: 'Select / Enter a User Code',
		fieldLabel: 'Code',
		id: 'cboUser',
		name: 'cboUser',
		xtype: 'textfield',
		listeners: {
			change: function(field, newValue) {
				Ext.getCmp('txtUser').setValue('');
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

	var cekPart = {
		boxLabel: 'User SparePart',
		checked: false,
		id: 'cekPart',
		name: 'cekPart',
		xtype: 'checkboxfield'
	};

	var txtUser = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '70%',
		emptyText: 'Enter a User Name',
		fieldLabel: 'Name',
		id: 'txtUser',
		name: 'txtUser',
		xtype: 'textfield'
	};
	
	var txtPassword = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '50%',
		emptyText: 'Password',
		fieldLabel: 'Password',
		id: 'txtPassword',
		inputType: 'password',
		name: 'txtPassword',
		xtype: 'textfield'
	};
	
	var txtPassword2 = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '50%',
		emptyText: 'Confirm Password',
		fieldLabel: 'Confirm',
		id: 'txtPassword2',
		inputType: 'password',
		name: 'txtPassword2',
		xtype: 'textfield'
	};

	var grupLevel = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_level'
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
			url: 'user/kodelevel'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_level': Ext.getCmp('cboLevel').getValue(),
					'fs_cari': Ext.getCmp('txtCari2').getValue()
				});
			}
		}
	});


	var winGrid2 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupLevel,
		columns: [	
			{xtype: 'rownumberer', width: 45},
			{text: "Level", dataIndex: 'fs_level', menuDisabled: true, width: 480}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Level',
				id: 'txtCari2',
				name: 'txtCari2',
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
					grupLevel.load();
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
			store: grupLevel,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari2.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboLevel').setValue(record.get('fs_level'));
				
				winCari2.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari2 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid2
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupLevel.load();
				vMask.show();
			}
		}
	});

	var cboLevel = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '60%',
		emptyText: 'Select a User Level',
		fieldLabel: 'Level',
		id: 'cboLevel',
		name: 'cboLevel',
		xtype: 'textfield',
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
					winCari2.show();
					winCari2.center();
				}
			}
		}
	};

	var grupLevel2 = Ext.create('Ext.data.Store', {
		autoLoad: false,
		fields: [
			'fs_level'
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
			url: 'user/kodelevel'
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_level': Ext.getCmp('cboLevel2').getValue(),
					'fs_cari': Ext.getCmp('txtCari3').getValue()
				});
			}
		}
	});


	var winGrid3 = Ext.create('Ext.grid.Panel',{
		anchor: '100%',
		autoDestroy: true,
		height: 450,
		width: 550,
		sortableColumns: false,
		store: grupLevel2,
		columns: [
			{xtype: 'rownumberer', width: 45},
			{text: "Level", dataIndex: 'fs_level', menuDisabled: true, width: 480}
		],
		tbar: [{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [{
				anchor: '98%',
				emptyText: 'Level',
				id: 'txtCari3',
				name: 'txtCari3',
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
					grupLevel2.load();
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
			store: grupLevel2,
			items:[
				'-', {
				text: 'Exit',
				handler: function() {
					winCari3.hide();
				}
			}]
		}),
		listeners: {
			itemdblclick: function(grid, record)
			{
				Ext.getCmp('cboLevel2').setValue(record.get('fs_level'));
				
				vLevel = record.get('fs_level').trim();
				grupGridLevel.load();
				
				winCari3.hide();
			}
		},
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false
		}
	});

	var winCari3 = Ext.create('Ext.window.Window', {
		border: false,
		closable: false,
		draggable: true,
		frame: false,
		layout: 'fit',
		plain: true,
		resizable: false,
		title: 'Searching...',
		items: [
			winGrid3
		],
		listeners: {
			beforehide: function() {
				vMask.hide();
			},
			beforeshow: function() {
				grupLevel2.load();
				vMask.show();
			}
		}
	});

	var cboLevel2 = {
		afterLabelTextTpl: required,
		allowBlank: false,
		anchor: '60%',
		emptyText: 'Select / Enter a Level',
		fieldLabel: 'Level',
		id: 'cboLevel2',
		name: 'cboLevel2',
		xtype: 'textfield',
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
					winCari3.show();
					winCari3.center();
				}
			}
		}
	};

	Ext.define('DataGridLevel', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'fs_nm_menu', type: 'string'},
			{name: 'fs_kd_induk', type: 'string'},
			{name: 'fs_kd_menu', type: 'string'},
			{name: 'fb_tambah', type: 'bool'}
		]
	});

	var grupGridLevel = Ext.create('Ext.data.TreeStore', {
		autoLoad: true,
		model: 'DataGridLevel',
		proxy: {
			actionMethods: {
				read: 'POST'
			},
			reader: {
				type: 'json'
			},
			type: 'ajax',
			url: 'user/ambil_nodes'
		},
		rootProperty: {
			expanded: true
		},
		listeners: {
			beforeload: function(store) {
				Ext.apply(store.getProxy().extraParams, {
					'fs_level': vLevel
				});
			}
		}
	});

	var gridLevel = Ext.create('Ext.tree.Panel', {
		anchor: '100%',
		autoDestroy: true,
		height: 400,
		rootVisible: false,
		sortableColumns: false,
		store: grupGridLevel,
		columns: [{
			dataIndex: 'fs_nm_menu',
			flex: 1.5,
			menuDisabled: true,
			text: 'Menu',
			xtype: 'treecolumn'
		},{
			dataIndex: 'fs_kd_induk',
			flex: 0.5,
			hidden: true,
			menuDisabled: true,
			text: 'Kode'
		},{
			dataIndex: 'fs_kd_menu',
			flex: 0.5,
			hidden: true,
			menuDisabled: true,
			text: 'Kode'
		},{
			align: 'center',
			dataIndex: 'fb_tambah',
			flex: 0.25,
			menuDisabled: true,
			stopSelection: false,
			text: 'Add',
			xtype: 'checkcolumn',
			listeners: {
				checkchange: function(grid, rowIndex, checked) {
					var xStore = gridLevel.getStore();
					var xRecord = xStore.getAt(rowIndex);
					var xTotal = grupGridLevel.getCount();
					
					var xKode = xRecord.get('fs_kd_menu').trim();
					var xCek = xRecord.get('fb_tambah');
					var xLen = 0;
					xLen = xKode.length;
					var j = 0;
					var xAda = false;
					
					if (xCek === true) {
		
						
						if (xLen === 0) {
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
		
								if (xLen === 0 && i !== rowIndex) {
									break;
								}
								else {
									xRecord.set('fb_tambah','1');
								}
							}
						}
						
						else if (xLen === 4) {
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if ((xLen === 0) || (xLen === 4 && i !== rowIndex)) {
									break;
								}
								else {
									xRecord.set('fb_tambah','1');
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
						}
						
						else if (xLen === 6) {
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if ((xLen === 0) || (xLen === 4) || (xLen === 6 && i !== rowIndex)) {
									break;
								}
								else {
									xRecord.set('fb_tambah','1');
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 4) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
						}
						
						else if (xLen === 8) {
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if ((xLen === 0) || (xLen === 4) || (xLen === 6) || (xLen === 8 && i !== rowIndex)) {
									break;
								}
								else {
									xRecord.set('fb_tambah','1');
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 6) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 4) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
						}
						
						else if (xLen === 10) {
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if ((xLen === 0) || (xLen === 4) || (xLen === 6) || (xLen === 8) || (xLen === 10 && i !== rowIndex)) {
									break;
								}
								else {
									xRecord.set('fb_tambah','1');
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 8) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 6) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 4) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
							
							for (i=rowIndex;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0) {
									xRecord.set('fb_tambah','1');
									break;
								}
							}
						}
						
					}
					
					else { //uncek
						
						if (xLen === 0) {
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0 && i !== rowIndex) {
									break;
								}
								else {
									xRecord.set('fb_tambah','0');
								}
							}
						}
						
						else if (xLen === 4) {
							// uncek 4
							j = 0;
							xAda = false;
							
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if ((xLen === 0) || (xLen === 4 && i !== rowIndex)) {
									break;
								}
								else if (xLen === 6 || xLen === 8) {
									xRecord.set('fb_tambah','0');
								}
							}
							// end of uncek 4
							
							// uncek 2
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 0) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 4 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 2
						}
						
						else if (xLen === 6) {
							// uncek 6
							j = 0;
							xAda = false;
							
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if ((xLen === 0) || (xLen === 4) || (xLen === 6 && i !== rowIndex)) {
									break;
								}
								else if (xLen === 8) {
									xRecord.set('fb_tambah','0');
								}
							}
							// end of uncek 6
							
							// uncek 4
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0 || xLen === 4) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 4) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 6 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 4
							
							// uncek 2
							j = 0;
							xAda = false;
							
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 0) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 4 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 2
						}
						
						else if (xLen === 8) {
							// uncek 8
							j = 0;
							xAda = false;
							
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if ((xLen === 0) || (xLen === 4) || (xLen === 6) || (xLen === 8 && i !== rowIndex)) {
									break;
								}
								else if (xLen === 10) {
									xRecord.set('fb_tambah','0');
								}
							}
							// end of uncek 8
							
							// uncek 6
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0 || xLen === 4 || xLen === 6) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 6) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 8 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 6
							
							// uncek 4
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0 || xLen === 4) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 4) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 6 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 4
							
							// uncek 2
							j = 0;
							xAda = false;
							
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 0) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 4 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 2
						}
						
						else if (xLen === 10) {
							// uncek 10
							j = 0;
							xAda = false;
							
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if ((xLen === 0) || (xLen === 4) || (xLen === 6) || (xLen === 8) || (xLen === 10 && i !== rowIndex)) {
									break;
								}
								else if (xLen === 12) {
									xRecord.set('fb_tambah','0');
								}
							}
							// end of uncek 10
							
							// uncek 8
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0 || xLen === 4 || xLen === 6 || xLen === 8) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 8) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 10 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 8
							
							// uncek 6
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0 || xLen === 4 || xLen === 6) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 6) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 8 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 6
							
							// uncek 4
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0 || xLen === 4) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 4) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 6 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 4
							
							// uncek 2
							j = 0;
							xAda = false;
							
							for (i=rowIndex;i<xTotal;i++) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								
								if (xLen === 0) {
									j = i - 1;
									break;
								}
								else if (i === xTotal - 1) {
									j = i;
									break;
								}
							}
							
							xAda = false;
							for (i=j;i>=0;i--) {
								xRecord = xStore.getAt(i);
								xKode = xRecord.get('fs_kd_menu').trim();
								xLen = xKode.length;
								xCek = xRecord.get('fb_tambah');
								
								if (xLen === 0) {
									if (xAda === false) {
										xRecord.set('fb_tambah','0');
									}
									break;
								}
								else {
									if (xLen === 4 && xCek === true) {
										xAda = true;
									}
								}
							}
							// end of uncek 2
						}
						
					}
				}
			}
		}],
		viewConfig: {
			getRowClass: function() {
				return 'rowwrap';
			},
			markDirty: false,
			stripeRows: true
		}
	});

	function fnCekSave() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'user/ceksave',
				params: {
					'fs_kd_user': Ext.getCmp('cboUser').getValue(),
					'fs_password': Ext.getCmp('txtPassword').getValue(),
					'fs_password2': Ext.getCmp('txtPassword2').getValue()
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
			url: 'user/save',
			params: {
				'fs_kd_user': Ext.getCmp('cboUser').getValue(),
				'fs_nm_user': Ext.getCmp('txtUser').getValue(),
				'fs_password': Ext.getCmp('txtPassword').getValue(),
				'fs_level': Ext.getCmp('cboLevel').getValue(),
				'fb_part': Ext.getCmp('cekPart').getValue()
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

	function fnCekSave2() {
		if (this.up('form').getForm().isValid()) {
			Ext.Ajax.on('beforerequest', fnMaskShow);
			Ext.Ajax.on('requestcomplete', fnMaskHide);
			Ext.Ajax.on('requestexception', fnMaskHide);
			
			Ext.Ajax.request({
				method: 'POST',
				url: 'user/ceksave2',
				params: {
					'fs_level': Ext.getCmp('cboLevel2').getValue()
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
							fnSave2();
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
										fnSave2();
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

	function fnSave2() {
		var xKdInduk = '';
		var xKdMenu = '';
		var store = gridLevel.getStore();
		
		store.each(function(record, idx) {
			if (record.get('fb_tambah') === true) {
				xKdInduk = xKdInduk +'|'+ record.get('fs_kd_induk');
				xKdMenu = xKdMenu +'|'+ record.get('fs_kd_menu');
			}
		});

		Ext.Ajax.on('beforerequest', fnMaskShow);
		Ext.Ajax.on('requestcomplete', fnMaskHide);
		Ext.Ajax.on('requestexception', fnMaskHide);
		
		Ext.Ajax.request({
			method: 'POST',
			url: 'user/save2',
			params: {
				'fs_level': Ext.getCmp('cboLevel2').getValue(),
				'fs_kd_induk': xKdInduk,
				'fs_kd_menu': xKdMenu
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
					fnReset2();
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

	function fnReset() {
		Ext.getCmp('cboUser').setValue('');
		Ext.getCmp('cekPart').setValue(false);
		Ext.getCmp('txtUser').setValue('');
		Ext.getCmp('txtPassword').setValue('');
		Ext.getCmp('txtPassword2').setValue('');
		Ext.getCmp('cboLevel').setValue('');
	}

	function fnReset2() {
		Ext.getCmp('cboLevel2').setValue('');
		vLevel = ''
		grupGridLevel.load();
	}

	var frmUser = Ext.create('Ext.form.Panel', {
		border: false,
		frame: true,
		region: 'center',
		title: 'User Master Form',
		width: 550,
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
				title: 'User Entry',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 60,
						msgTarget: 'side'
					},
					style: 'padding: 5px;',
					xtype: 'fieldset',
					items: [{
						anchor: '100%',
						layout: 'hbox',
						xtype: 'container',
						items: [{
							flex: 1.2,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cboUser
							]
						},{
							flex: 1,
							layout: 'anchor',
							xtype: 'container',
							items: [
								cekPart
							]
						}]
					},
						txtUser,
						txtPassword,
						txtPassword2,
						cboLevel
					]
				}],
				buttons: [{
					text: 'Save',
					handler: fnCekSave
				},{
					text: 'Reset',
					handler: fnReset
				}]
			},{
				bodyStyle: 'background-color: '.concat(gBasePanel),
				border: false,
				frame: false,
				title: 'User Level',
				xtype: 'form',
				items: [{
					fieldDefaults: {
						labelAlign: 'right',
						labelSeparator: '',
						labelWidth: 40,
						msgTarget: 'side'
					},
					style: 'padding: 5px;',
					xtype: 'fieldset',
					items: [
						cboLevel2,
						gridLevel
					]
				}],
				buttons: [{
					text: 'Save',
					handler: fnCekSave2
				},{
					text: 'Reset',
					handler: fnReset2
				}]
			}]
		}]
	});

	var vMask = new Ext.LoadMask({
		msg: 'Please wait...',
		target: frmUser
	});
	
	function fnMaskShow() {
		frmUser.mask('Please wait...');
	}

	function fnMaskHide() {
		frmUser.unmask();
	}
	
	frmUser.render(Ext.getBody());
	Ext.get('loading').destroy();
});

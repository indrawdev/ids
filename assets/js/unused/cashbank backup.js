Ext.Loader.setConfig({enabled: true});

Ext.Loader.setPath('Ext.ux', '../assets/js');

Ext.require([
    'Ext.ux.LiveSearchGridPanel', 'Ext.ux.form.NumericField', 'Ext.window.MessageBox'
]);

var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

function zeroPad(num, places) {
  var zero = places - num.toString().length + 1;
  return Array(+(zero > 0 && zero)).join("0") + num;
}

function gridTooltip(view) {
    view.tip = Ext.create('Ext.tip.ToolTip', {
        delegate: view.itemSelector,
		html: 'Click an item to edit or delete',
        target: view.el,
        trackMouse: true,
        listeners: {
            beforeshow: function (tip) {
                var tooltip = view.getRecord(tip.triggerElement).get('tooltip');
                if(tooltip){
                    tip.update(tooltip);
                } else {
                     tip.on('show', function(){
                         Ext.defer(tip.hide, 1000, tip);
                     }, tip, {single: true});
                }
            }
        }
    });
}

var grupstrx = new Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: [
		'fs_kd_strx','fs_nm_strx'
	],
	proxy: {
		actionMethods: 'POST',
		reader: {
			type: 'json'
		},
		type: 'ajax',
		url: 'cashbank/cbin_trs'
	}
});

var grupdept = new Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: ['fs_code','fs_nm_code'],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'cashbank/cbin_dept'
	}
});

var popgrid = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 500,
	store: grupdept,
	bbar: new Ext.PagingToolbar({
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		store: grupdept,
		items:[
			'-', {
			text: 'Exit',
			handler: function(){
				popgrid.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer'},
		{text: "Dept Code", dataIndex: 'fs_code', hidden: true},
		{text: "Dept Name", dataIndex: 'fs_nm_code', width: 500}
	],
    listeners: {
        cellclick: function(grid, record, rowIndex)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			var record = grid.store.getAt(xpos.row);
			Ext.getCmp('cbodept').setValue(record.get('fs_nm_code'));
			Ext.getCmp('txtdept').setValue(record.get('fs_code'));
		},
		celldblclick: function(grid, record, rowIndex)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			var record = grid.store.getAt(xpos.row);
			Ext.getCmp('cbodept').setValue(record.get('fs_nm_code'));
			Ext.getCmp('txtdept').setValue(record.get('fs_code'));
			popgrid.hide();
		}
    }
});

var helpGrid = new Ext.menu.Menu();
helpGrid.add(popgrid);

Ext.define('Ext.ux.SearchDept', {
	alias: 'widget.searchdept',
	extend: 'Ext.form.field.Trigger',
	initComponent: function(){
		this.callParent(arguments);
		this.on('specialkey', function(f, e){
			if(e.getKey() == e.ENTER){
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function(){
		popgrid.show();
		helpGrid.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var gruprefno = new Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: [
		'fs_code','fs_nm_dept','fs_kd_strx',
		'fs_nm_strx','fs_refno','fd_refno',
		'fs_doc','fd_doc','fs_descrp',
		'fs_kd_status','fs_nm_status'
	],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'cashbank/cbin_refno'
	}
});

var popgrid2 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 500,
	store: gruprefno,
	bbar: new Ext.PagingToolbar({
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		store: gruprefno,
		items:[
			'-', {
			text: 'Exit',
			handler: function(){
				popgrid2.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer'},
		{text: "Dept Cd", dataIndex: 'fs_code', hidden: true},
		{text: "Dept Name", dataIndex: 'fs_nm_dept', hidden: true},
		{text: "sTrx Cd", dataIndex: 'fs_kd_strx', hidden: true},
		{text: "sTrx", dataIndex: 'fs_nm_strx', hidden: true},
		{text: "Ref No", dataIndex: 'fs_refno', width: 200},
		{text: "Ref Date", dataIndex: 'fd_refno', width: 80},
		{text: "Doc No", dataIndex: 'fs_doc', width: 100},
		{text: "Doc Date", dataIndex: 'fd_doc', hidden: true},
		{text: "Descrp", dataIndex: 'fs_descrp', width: 250},
		{text: "Status Cd", dataIndex: 'fs_kd_status', hidden: true},
		{text: "Status", dataIndex: 'fs_nm_status', width: 80}
	],
    listeners: {
        cellclick: function(grid, record, rowIndex)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			var record = grid.store.getAt(xpos.row);
			Ext.getCmp('cbodept').setValue(record.get('fs_nm_dept'));
			Ext.getCmp('txtdept').setValue(record.get('fs_code'));
			Ext.getCmp('cbotrs').setValue(record.get('fs_kd_strx'));
			Ext.getCmp('cbodraft').setValue(record.get('fs_kd_status'));
			Ext.getCmp('cborefno').setValue(record.get('fs_refno'));
			Ext.getCmp('txtdocno').setValue(record.get('fs_doc'));
			Ext.getCmp('txtnote').setValue(record.get('fs_descrp'));
			
			var x = "-";
			var xdate = record.get('fd_refno');
			var xdate = xdate.substr(3,2).concat(x,xdate.substr(0,2),x,xdate.substr(6,4));
			Ext.getCmp('txtrefnodt').setValue(xdate);
			
			var xdate = record.get('fd_doc');
			var xdate = xdate.substr(3,2).concat(x,xdate.substr(0,2),x,xdate.substr(6,4));
			Ext.getCmp('txtdocnodt').setValue(xdate);
		},
		celldblclick: function(grid, record, rowIndex)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			var record = grid.store.getAt(xpos.row);
			Ext.getCmp('cbodept').setValue(record.get('fs_nm_dept'));
			Ext.getCmp('txtdept').setValue(record.get('fs_code'));
			Ext.getCmp('cbotrs').setValue(record.get('fs_kd_strx'));
			Ext.getCmp('cbodraft').setValue(record.get('fs_kd_status'));
			Ext.getCmp('cborefno').setValue(record.get('fs_refno'));
			Ext.getCmp('txtdocno').setValue(record.get('fs_doc'));
			Ext.getCmp('txtnote').setValue(record.get('fs_descrp'));
			
			var x = "-";
			var xdate = record.get('fd_refno');
			var xdate = xdate.substr(3,2).concat(x,xdate.substr(0,2),x,xdate.substr(6,4));
			Ext.getCmp('txtrefnodt').setValue(xdate);
			
			var xdate = record.get('fd_doc');
			var xdate = xdate.substr(3,2).concat(x,xdate.substr(0,2),x,xdate.substr(6,4));
			Ext.getCmp('txtdocnodt').setValue(xdate);
			popgrid2.hide();
		}
    }
});

var helpGrid2 = new Ext.menu.Menu();
helpGrid2.add(popgrid2);

Ext.define('Ext.ux.form.SearchRefno', {
	alias: 'widget.searchrefno',
	extend: 'Ext.form.field.Trigger',
	initComponent: function(){
		this.callParent(arguments);
		this.on('specialkey', function(f, e){
			if(e.getKey() == e.ENTER){
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function(){
		popgrid2.show();
		helpGrid2.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var grupacno = new Ext.create('Ext.data.Store', {
	autoLoad: true,
	fields: [
		'fs_kd_acno','fs_nm_acno'
	],
	pageSize: 25,
	proxy: {
		actionMethods: 'POST',
		reader: {
			root: 'hasil',
			totalProperty: 'total',
			type: 'json'
		},
		type: 'ajax',
		url: 'cashbank/cbin_acno'
	}
});

var popgrid3 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 500,
	store: grupacno,
	bbar: new Ext.PagingToolbar({
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		store: grupacno,
		items:[
			'-', {
			text: 'Exit',
			handler: function(){
				popgrid3.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer'},
		{text: "Acc. No", dataIndex: 'fs_kd_acno', width: 150},
		{text: "Acc. Name", dataIndex: 'fs_nm_acno', width: 550}
	],
    listeners: {
        cellclick: function(grid, record, rowIndex)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			var record = grid.store.getAt(xpos.row);
			Ext.getCmp('cboacno').setValue(record.get('fs_kd_acno'));
			Ext.getCmp('txtacno').setValue(record.get('fs_nm_acno'));
		},
		celldblclick: function(grid, record, rowIndex)
		{
			var xpos = grid.getSelectionModel().getCurrentPosition();
			var record = grid.store.getAt(xpos.row);
			Ext.getCmp('cboacno').setValue(record.get('fs_kd_acno'));
			Ext.getCmp('txtacno').setValue(record.get('fs_nm_acno'));
			popgrid3.hide();
		}
    }
});

var helpGrid3 = new Ext.menu.Menu();
helpGrid3.add(popgrid3);

Ext.define('Ext.ux.SearchAcno', {
	alias: 'widget.searchacno',
	extend: 'Ext.form.field.Trigger',
	initComponent: function(){
		this.callParent(arguments);
		this.on('specialkey', function(f, e){
			if(e.getKey() == e.ENTER){
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function(){
		popgrid3.show();
		helpGrid3.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

var popgrid4 = Ext.create('Ext.ux.LiveSearchGridPanel', {
	autoDestroy: true,
	height: 250,
	width: 500,
	store: grupacno,
	bbar: new Ext.PagingToolbar({
		displayInfo: true,
		displayMsg: 'Displaying record {0} - {1} of {2}',
		emptyMsg: "No record to display",
		pageSize: 25,
		store: grupacno,
		items:[
			'-', {
			text: 'Exit',
			handler: function(){
				popgrid4.hide();
			}
		}]
	}),
	columns: [
		{xtype: 'rownumberer'},
		{text: "Acc. No", dataIndex: 'fs_kd_acno', width: 150},
		{text: "Acc. Name", dataIndex: 'fs_nm_acno', width: 550}
	],
    listeners: {
        cellclick: function(grid, record, rowIndex)
		{
			var xposgrid = griddetail.getSelectionModel().getCurrentPosition();
			var recordgrid = griddetail.store.getAt(xposgrid.row);
			var xpos = grid.getSelectionModel().getCurrentPosition();
			var record = grid.store.getAt(xpos.row);
			recordgrid.set('fs_kd_acno',record.get('fs_kd_acno'));
			recordgrid.set('fs_nm_acno',record.get('fs_nm_acno'));
		},
		celldblclick: function(grid, record, rowIndex)
		{
			var xposgrid = griddetail.getSelectionModel().getCurrentPosition();
			var recordgrid = griddetail.store.getAt(xposgrid.row);
			var xpos = grid.getSelectionModel().getCurrentPosition();
			var record = grid.store.getAt(xpos.row);
			recordgrid.set('fs_kd_acno',record.get('fs_kd_acno'));
			recordgrid.set('fs_nm_acno',record.get('fs_nm_acno'));
			popgrid4.hide();
		}
    }
});

var helpGrid4 = new Ext.menu.Menu();
helpGrid4.add(popgrid4);

Ext.define('Ext.ux.SearchGridAcno', {
	alias: 'widget.searchgridacno',
	extend: 'Ext.form.field.Trigger',
	initComponent: function(){
		this.callParent(arguments);
		this.on('specialkey', function(f, e){
			if(e.getKey() == e.ENTER){
				this.onTriggerClick();
			}
		}, this);
	},
	onTriggerClick: function(){
		popgrid4.show();
		helpGrid4.showAt([this.getPosition()[0],this.getPosition()[1]+this.getHeight()]);
	}
});

Ext.define('DataGrid', {
	extend: 'Ext.data.Model',
	fields: [
		{name: 'fs_kd_acno', type: 'string'},
		{name: 'fs_nm_acno', type: 'string'},
		{name: 'fs_note', type: 'string'},
		{name: 'fn_debet', type: 'float'},
		{name: 'fn_credit', type: 'float'},
		{name: 'fs_seqno', type: 'string'}
	]
});

var grupgrid = Ext.create('Ext.data.Store', {
	autoDestroy: true,
	model: 'DataGrid',
	proxy: {
		type: 'memory'
	},
	// data: data,
	sorters: [{
		property: 'start',
		direction: 'ASC'
	}]
});

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
	clicksToMoveEditor: 1/*,
	autoCancel: false*/
});

var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
	clicksToMoveEditor: 1,
	autoCancel: false
});

var griddetail = Ext.create('Ext.grid.Panel', {
	store: grupgrid,
	height: 170,
	defaultType: 'textfield',
	columns: [{
		xtype: 'rownumberer'
	},{
		text: 'Acc. No',
		dataIndex: 'fs_kd_acno',
		flex: 0.75,
		editor: {
			xtype: 'searchgridacno'
		}
	},{
		text: 'Acc. Name',
		dataIndex: 'fs_nm_acno',
		flex: 1
	},{
		text: 'Note',
		dataIndex: 'fs_note',
		flex: 1.5,
		editor: {
			xtype: 'textfield'
		}
	},{
		align: 'right',
		dataIndex: 'fn_debet',
		flex: 0.75,
		format: '0,000.00',
		text: 'Debet',
		xtype: 'numbercolumn',
		editor: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: ',',
				hideTrigger: true,
				keyNavEnabled: false,
				mouseWheelEnabled: false,
				thousandSeparator: '.',
				useThousandSeparator: true,
				xtype: 'numberfield'
			})
		]
	},{
		align: 'right',
		dataIndex: 'fn_credit',
		flex: 0.75,
		format: '0,000.00',
		text: 'Credit',
		xtype: 'numbercolumn',
		editor: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: ',',
				hideTrigger: true,
				keyNavEnabled: false,
				mouseWheelEnabled: false,
				thousandSeparator: '.',
				useThousandSeparator: true,
				xtype: 'numberfield'
			})
		]
	},{
		text: 'Seqno',
		dataIndex: 'fs_seqno',
		flex: 0.5,
		hidden: false,
		renderer: function(grid, value, record, rowIndex, colIndex){
			return zeroPad(rowIndex + 1, 6);
		}
	}],
	listeners: {
		'selectionchange': function(view, records) {
			griddetail.down('#editData').setDisabled(!records.length);
			griddetail.down('#removeData').setDisabled(!records.length);
		}
	},/*
	plugins: [
		cellEditing//rowEditing//
	],*/
    viewConfig: {
        listeners: {
            render: gridTooltip
        }
    },
	bbar: [{
		anchor: '100%',
		layout: 'hbox',
		xtype: 'container',
		items: [{
			xtype: 'tbspacer', width: 375
		},{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
					anchor: '75%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: ',',
					editable: false,
					fieldLabel: 'Total Debet',
					hideTrigger: true,
					labelAlign: 'right',
					id: 'txttotaldebet',
					keyNavEnabled: false,
					mouseWheelEnabled: false,
					name: 'txttotaldebet',
					thousandSeparator: '.',
					useThousandSeparator: true,
					value: 0
				})
			]
		},{
			flex: 1,
			layout: 'anchor',
			xtype: 'container',
			items: [
				Ext.create('Ext.ux.form.NumericField', {
					alwaysDisplayDecimals: true,
					anchor: '75%',
					currencySymbol: null,
					decimalPrecision: 2,
					decimalSeparator: ',',
					editable: false,
					fieldLabel: 'Total Credit',
					hideTrigger: true,
					labelAlign: 'right',
					id: 'txttotalcredit',
					keyNavEnabled: false,
					mouseWheelEnabled: false,
					name: 'txttotalcredit',
					thousandSeparator: '.',
					useThousandSeparator: true,
					value: 0
				})
			]
		}]
	}],
	tbar: [{
		flex: 1.5,
		layout: 'anchor',
		xtype: 'container',
		items: [{
			anchor: '95%',
			// displayField: 'fs_nm_acno',
			emptyText: 'Select an Account',
			fieldLabel: 'Account No',
			id: 'cboacno',
			labelAlign: 'top',
			name: 'cboacno',
			// valueField: 'fs_kd_acno',
			xtype: 'searchacno',
			listeners: {
				change: function(combo, value) {
					grupacno.load({
						params: {
							'fs_kd_acno': Ext.getCmp('cboacno').getValue(),
							'fs_nm_acno': Ext.getCmp('cboacno').getValue()
						}
					});
					Ext.getCmp('txtacno').setValue('');
				}
			}
		},{
			anchor: '95%',
			emptyText: 'Enter an Acc No',
			hidden: true,
			id: 'txtacno',
			name: 'txtacno',
			xtype: 'textfield'
		}]
	},{
		flex: 1.75,
		layout: 'anchor',
		xtype: 'container',
		items: [{
			anchor: '95%',
			emptyText: 'Enter a Description',
			fieldLabel: 'Description',
			id: 'txtdesc',
			labelAlign: 'top',
			name: 'txtdesc',
			xtype: 'textfield'
		}]
	},{
		flex: 1,
		layout: 'anchor',
		xtype: 'container',
		items: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				anchor: '95%',
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: ',',
				fieldLabel: 'Debet',
				hideTrigger: true,
				id: 'txtdebet',
				keyNavEnabled: false,
				labelAlign: 'top',
				mouseWheelEnabled: false,
				name: 'txtdebet',
				thousandSeparator: '.',
				useThousandSeparator: true,
				value: 0,
				listeners: {
					change: function(value) {
						if (Ext.isEmpty(Ext.getCmp('txtdebet').getValue()))
						{
							Ext.getCmp('txtdebet').setValue(0);
						}
						else
						{
							return value;
						}
					}
				}
			})
		]
	},{
		flex: 1,
		layout: 'anchor',
		xtype: 'container',
		items: [
			Ext.create('Ext.ux.form.NumericField', {
				alwaysDisplayDecimals: true,
				anchor: '95%',
				currencySymbol: null,
				decimalPrecision: 2,
				decimalSeparator: ',',
				fieldLabel: 'Credit',
				hideTrigger: true,
				id: 'txtcredit',
				keyNavEnabled: false,
				labelAlign: 'top',
				mouseWheelEnabled: false,
				name: 'txtcredit',
				thousandSeparator: '.',
				useThousandSeparator: true,
				value: 0,
				listeners: {
					change: function(value) {
						if (Ext.isEmpty(Ext.getCmp('txtcredit').getValue()))
						{
							Ext.getCmp('txtcredit').setValue(0);
						}
						else
						{
							return value;
						}
					}
				}
			})
		]
	},{
		iconCls: 'icon-add',
		text: 'Add',
		handler : function() {
			var total = grupgrid.getCount();
			var xdata = Ext.create('DataGrid', {
				fs_kd_acno: Ext.getCmp('cboacno').getValue(),
				fs_nm_acno: Ext.getCmp('txtacno').getValue(),
				fs_note: Ext.getCmp('txtdesc').getValue(),
				fn_debet: Ext.getCmp('txtdebet').getValue(),
				fn_credit: Ext.getCmp('txtcredit').getValue(),
				fs_seqno: ''
			});
			
			var xdebet = Ext.getCmp('txtdebet').getValue();
			var xcredit = Ext.getCmp('txtcredit').getValue();
			if (xdebet == 0 && xcredit == 0)
			{
				Ext.Msg.show({
					buttons: Ext.MessageBox.OK,
					icon: Ext.Msg.WARNING,
					msg: 'One of Debet or Credit should be zero!',
					title: 'Aster Web'
			   });
			   return;
			}
			
			grupgrid.insert(total, xdata);
			Ext.getCmp('cboacno').setValue('');
			Ext.getCmp('txtacno').setValue('');
			Ext.getCmp('txtdesc').setValue('');
			Ext.getCmp('txtdebet').setValue(0);
			Ext.getCmp('txtcredit').setValue(0);
			
			// var grid = Ext.getCmp('griddetail');
			// grid.getSelectionModel().selectAll();
			var sm = griddetail.getSelectionModel();
			var sr = sm.getSelection();
			var xdebet = 0;
			var xcredit = 0;
			for (i=0; i<=sr.length-1; i++) {
				xdebet = xdebet + sr[i].get('fn_debet');
				xcredit = xcredit + sr[i].get('fn_debet');
			}
			Ext.getCmp('txttotaldebet').setValue(xdebet);
			Ext.getCmp('txttotalcredit').setValue(xcredit);
		}
	}, '-',{
		iconCls: 'icon-edit',
		itemId: 'editData',
		text: 'Edit',
		handler: function() {
			var sm = griddetail.getSelectionModel();
			var xpos = griddetail.getSelectionModel().getCurrentPosition();
			var record = griddetail.store.getAt(xpos.row);
			
			Ext.getCmp('cboacno').setValue(record.get('fs_kd_acno'));
			Ext.getCmp('txtacno').setValue(record.get('fs_nm_acno'));
			Ext.getCmp('txtdesc').setValue(record.get('fs_note'));
			Ext.getCmp('txtdebet').setValue(record.get('fn_debet'));
			Ext.getCmp('txtcredit').setValue(record.get('fn_credit'));
			
			grupgrid.remove(sm.getSelection());
			griddetail.getView().refresh();
			if (grupgrid.getCount() > 0) {
				sm.select(0);
			}
		},
		disabled: true
	}, '-',{
		iconCls: 'icon-delete',
		itemId: 'removeData',
		text: 'Delete',
		handler: function() {
			var sm = griddetail.getSelectionModel();
			// cellEditing.cancelEdit();//rowEditing.cancelEdit();//
			grupgrid.remove(sm.getSelection());
			griddetail.getView().refresh();
			if (grupgrid.getCount() > 0) {
				sm.select(0);
			}
		},
		disabled: true
	}]
});

function fnReset(){
	Ext.getCmp('cbodept').setValue(vDept);
	Ext.getCmp('txtdept').setValue(vDeptCd);
	Ext.getCmp('cbodraft').setValue('1');
	Ext.getCmp('cborefno').setValue('');
	Ext.getCmp('txtrefnodt').setValue(new Date());
	Ext.getCmp('txtdocno').setValue('');
	Ext.getCmp('txtdocnodt').setValue(new Date());
	Ext.getCmp('txtnote').setValue('');
	Ext.getCmp('cboacno').setValue('');
	Ext.getCmp('txtacno').setValue('');
	Ext.getCmp('txtdebet').setValue('0');
	Ext.getCmp('txtcredit').setValue('0');
}

var frmCashBank = new Ext.tab.Panel({
	activeTab: 'CBIn',
	bodyStyle: 'padding:7px 7px;',
	border: false,
	defaultType: 'textfield',
	frame: false,
	id: 'formCashBank',
	method: 'POST',
	region: 'center',
	//url: 'login/ceklogin',
	xtype: 'tabpanel',
	items: [{
		border: false,
		frame: true,
		id: 'CBIn',
		name: 'CBIn',
		title: 'Entry Cash Bank In',
		xtype: 'form',
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 80
        },
		items: [{
			title: 'Header',
			xtype: 'fieldset',
			items: [{
				afterLabelTextTpl: required,
				allowBlank: false,
				displayField: 'fs_nm_code',
				editable: false,
				emptyText: 'Select a Department',
				fieldLabel: 'Department',
				id: 'cbodept',
				name: 'cbodept',
				queryMode: 'remote',
				// triggerAction: 'all',
				// store: grupdept,
				value: vDept,
				valueField: 'fs_code',
				width: 420,
				xtype: 'searchdept',
				listeners: {
					change: function(combo, value) {
						// var xcborefno = Ext.getCmp('cborefno');
						// xcborefno.clearValue();
						gruprefno.load({
							params: {
								'fs_code': Ext.getCmp('txtdept').getValue(),
								'fs_kd_strx': Ext.getCmp('cbotrs').getValue(),
								'fs_refno': Ext.getCmp('cborefno').getValue()
							}
						});
						Ext.getCmp('cborefno').setValue('');
					}
				}
			},{
				fieldLabel: 'Dept Code',
				hidden: true,
				id: 'txtdept',
				name: 'txtdept',
				value: vDeptCd,
				width: 420,
				xtype: 'textfield'
			},{
				anchor: '100%',
				layout: 'hbox',
				xtype: 'container',
				items: [{
					flex: 1.25,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '90%',
						displayField: 'fs_nm_strx',
						editable: false,
						emptyText: 'Select a Trans Type',
						fieldLabel: 'Trans Type',
						id: 'cbotrs',
						name: 'cbotrs',
						queryMode: 'remote',
						store: grupstrx,
						triggerAction: 'all',
						valueField: 'fs_kd_strx',
						xtype: 'combobox',
						listeners: {
							change: function(combo, value) {
								// var xcborefno = Ext.getCmp('cborefno');
								// xcborefno.clearValue();
								gruprefno.load({
									params: {
										'fs_code': Ext.getCmp('txtdept').getValue(),
										'fs_kd_strx': Ext.getCmp('cbotrs').getValue(),
										'fs_refno': Ext.getCmp('cborefno').getValue()
									}
								});
								Ext.getCmp('cborefno').setValue('');
							}
						}
					},{
						afterLabelTextTpl: required,
						//allowBlank: true,
						anchor: '90%',
						//displayField: 'fs_refno',//'fs_nm_dept',
						editable: true,
						emptyText: 'AUTOMATIC',
						fieldLabel: 'Ref. No',
						id: 'cborefno',
						name: 'cborefno',
						//queryMode: 'local',
						// store: gruprefno,
						// triggerAction: 'all',
						valueField: 'fs_refno',
						//width: 350,
						xtype: 'searchrefno',//'combobox',
						listeners: {
							change: function(combo, value) {
								// var xcborefno = Ext.getCmp('cborefno');
								// xcborefno.clearValue();
								gruprefno.load({
									params: {
										'fs_code': Ext.getCmp('txtdept').getValue(),
										'fs_kd_strx': Ext.getCmp('cbotrs').getValue(),
										'fs_refno': Ext.getCmp('cborefno').getValue()
									}
								});
							}
						}
					},{
						anchor: '90%',
						emptyText: 'Enter a Document No',
						fieldLabel: 'Doc. No',
						id: 'txtdocno',
						name: 'txtdocno',
						xtype: 'textfield'
					}]
				},{
					flex: 1,
					layout: 'anchor',
					xtype: 'container',
					items: [{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '50%',
						displayField: 'fs_nm_status',
						editable: false,
						fieldLabel: 'Status',
						id: 'cbodraft',
						name: 'cbodraft',
						queryMode: 'local',
						triggerAction: 'all',
						value: 'DRAFT',
						valueField: 'fs_kd_status',
						xtype: 'combobox',
						store: new Ext.data.ArrayStore({
							autoLoad: true,
							data: [['0', 'APPROVED'], ['1', 'DRAFT']],
							fields: ['fs_kd_status', 'fs_nm_status']
						}),
					},{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '50%',
						editable: true,
						fieldLabel: 'Ref. Date',
						format: 'd-m-Y',
						id: 'txtrefnodt',
						name: 'txtrefnodt',
						value: new Date(),
						xtype: 'datefield'
					},{
						afterLabelTextTpl: required,
						allowBlank: false,
						anchor: '50%',
						editable: true,
						fieldLabel: 'Doc. Date',
						format: 'd-m-Y',
						id: 'txtdocnodt',
						name: 'txtdocnodt',
						value: new Date(),
						xtype: 'datefield'
					}]
				}]
			},{
				emptyText: 'Enter a Note',
				fieldLabel: 'Note',
				id: 'txtnote',
				name: 'txtnote',
				rows: 1,
				width: 654,
				xtype: 'textarea'
			},{
				xtype: 'form',
				buttons: [{
					text: 'Save'
				},{
					text: 'Reset',
					handler: fnReset
				},{
					text: 'Remove',
					handler: fnReset
				}]
			}]
		},{
			title: 'Detail',
			xtype: 'fieldset',
			items: [
				griddetail
				,{
					xtype: 'form',
					buttons: [{
						text: 'Save'
					},{
						text: 'Remove',
						handler: fnReset
					}]
				}
			]
		}]
	},{
		border: false,
		frame: false,
		id: 'CBOut',
		name: 'CBOut',
		title: 'Entry Cash Bank Out',
		xtype: 'form'
	}]/*,
	renderTo: Ext.getBody()*/
});

Ext.onReady(function(){
    Ext.QuickTips.init();
	Ext.util.Format.thousandSeparator = '.';
	Ext.util.Format.decimalSeparator = ',';
	
	var winCashBank = new Ext.Window({
        closable: false,
        draggable: false,
        height: 550,
		id: 'winCashBank',
        layout: 'border',
        minHeight: 550,
        minWidth: 900,
        plain: true,
        resizable: false,
        title: 'Cash Bank Form',
        width: 900,
        items: [
			frmCashBank
		]
	});
    winCashBank.show();
});
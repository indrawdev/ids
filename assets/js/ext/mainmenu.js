Ext.Loader.setConfig({
	enabled: true
});

Ext.Loader.setPath('Ext.ux', gBaseUX.concat('/statusbar'));

Ext.require('Ext.ux.StatusBar');

Ext.onReady(function() {
	Ext.QuickTips.init();

	var txtTgl = Ext.create('Ext.toolbar.TextItem', {
		text: Ext.Date.format(new Date(), 'l, d F Y')
	});

	var txtJam = Ext.create('Ext.toolbar.TextItem', {
		text: Ext.Date.format(new Date(), 'H:i')
	});
		
	function buatMenu() {
		
		Ext.Date.dayNames = [
			'Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'
		];

		Ext.Date.monthNames = [
			'Januari','Februari','Maret','April','Mei','Juni',
			'Juli','Agustus','September','Oktober','Nopember','Desember'
		];

		var datatree = Ext.create('Ext.data.TreeStore', {
			proxy: {
				type: 'ajax',
				url: 'mainmenu/ambil_nodes'
			},
			reader: {
				type: 'json'
			},
			root: {
				expanded: true
			}
		});

		// var cmdButton = Ext.create('Ext.Button', {
			// id: 'cmdWin',
			// text: 'Open in new window',
			// listeners : {
				// click : function() {
					// var aktifTab = tabPanel.getActiveTab();
					// if (aktifTab !== null) {
						// var win = window.open(aktifTab.itemId, '_blank');
						// win.focus();
					// }
				// }
			// }
		// });

		var tabPanel = Ext.create('Ext.tab.Panel', {
			activeItem: 0,
			bodyStyle: 'background-color: '.concat(gBasePanel),
			border: false,
			defaults: {
				autoScroll: true
			},
			deferredRender: true,
			enableTabScroll: true,
			id: 'isipanel',
			margins: '2 5 5 0',
			layout: 'fit',
			layoutOnTabChange: true,
			plain: false,
			region: 'center',
			resizeTabs: true,
			xtype: 'tabpanel',
			items: []/*,
			tabBar: {
				items:[{
					xtype: 'tbfill'
					}, cmdButton
				]
			}*/
		});

		var xTxt = '';
		if (Ext.Date.format(new Date(), 'Y') === '2015') {
			xTxt = 'Copyright &copy; '+ Ext.Date.format(new Date(), 'Y') +' ~ IDS Team';
		}
		else {
			xTxt = 'Copyright &copy; 2015 - '+ Ext.Date.format(new Date(), 'Y') +' ~ IDS Team';
		}
		
		var tbPanel = Ext.create('Ext.form.Panel', {
			id: 'contentPanel',
			layout: 'border',
			region: 'center',
			items: [
				tabPanel
			],
			bbar: Ext.create('Ext.ux.StatusBar', {
				id: 'win-statusbar',
				defaultText: xTxt,
				items: ['-',
					Ext.create('Ext.toolbar.TextItem', {
						text: vUser.toUpperCase()
					}), '-',
					Ext.create('Ext.toolbar.TextItem', {
						text: 'Dept: ' + vDeptCd + vDeptCount + ' - ' + vDept.toUpperCase()
					}), '-',
					Ext.create('Ext.toolbar.TextItem', {
						text: vDB.toUpperCase() + ' on ' + vServer
					}), '-',
					txtTgl, '-', txtJam
				]
			})/*,
			tbar: ['->',{
				iconCls: 'icon-logout',
				text: 'Logout',
				handler: fnLogout
			},{xtype: 'tbspacer'}]*/
		});

		var treePanel = Ext.create('Ext.tree.Panel', {
			autoScroll: true,
			border: false,
			collapsible: true,
			height: '100%',
			hideHeaders: true,
			id: 'astermenu',
			margins: '2 0 5 5',
			maxSize: 250,
			minSize: 150,
			region: 'west',
			rootVisible: false,
			scroll: true,
			split: true,
			store: datatree,
			title: 'IDS Main Menu',
			width: 250,
			tbar: [{
				iconCls: 'icon-delete',
				text: 'Expand All',
				handler: function() {
					treePanel.expandAll();
				}
			},{
				iconCls: 'icon-add',
				text: 'Collapse All',
				handler: function() {
					treePanel.collapseAll();
				}
			},{
				iconCls: 'icon-logout',
				text: 'Logout',
				handler: fnLogout
			}],
			listeners: {
				itemclick: function(view, record, item, index, evt, eOpts) {
					// get the click tree node's text and ID attribute values.
					var nodeText = record.get('text');
					var tabId = record.get('id');
					var id_arr = tabId.split('|');
					var tableaf = record.get('leaf');
					// var tablet = record.get('href');
					
					if (tableaf === false) {
						// Ext.Msg.alert('Tree Folder Clicked', 'You clicked on tree folder "' + nodeText + '"');
						return;
					}
					
					// get a reference to the target Tab Panel.
					var tabPanel = Ext.ComponentQuery.query('viewport tabpanel')[0];
					
					// does the tab already exist?  Note the use of the '#' symbol to indicate
					// your looking for an object with the specified itemId.
					var tab = tabPanel.child('#' + id_arr[1]);
					
					// if tab not already present, create it and add to tab panel.
					if (tab === null && id_arr[1].trim() !== '') {
						tab = Ext.create('Ext.Component', {
							autoEl: {
								src: id_arr[1],
								tag: 'iframe'
							},
							closable: true,
							height: '100%',
							itemId: id_arr[1].trim(),
							renderto: 'tabPanel',
							title: nodeText.trim(),
							xtype: 'component'
						});
						tabPanel.add(tab);
						tabPanel.doLayout();
					}
					tabPanel.setActiveTab(tab);
				}
			},
			viewConfig: {
				getRowClass: function() {
					// return 'rowwrap';
				},
				stripeRows: true
			}
		});

		function fnLogout() {
			Ext.Ajax.request({
				method: 'POST',
				url: 'login/logout',
				success: function() {
					window.location.href = 'login';
				}
			});
		}
		
		Ext.TaskManager.start({
			interval: 1000,
			run: function() {
				Ext.fly(txtTgl.getEl()).update(Ext.Date.format(new Date(), 'l, d F Y'));
				Ext.fly(txtJam.getEl()).update(Ext.Date.format(new Date(), 'H:i'));
			}
		});
		
		Ext.create('Ext.container.Viewport', {
			layout: 'border',
			renderTo: Ext.getBody(),
			title: 'IDS - Integrated Dealership System',
			items: [{
				height: 2, //warna biru tua di atas menu
				id: 'header',
				region: 'north',
				xtype: 'box'
			},
				treePanel, tbPanel
			]
		});
	}

	Ext.Ajax.request({
		method: 'POST',
		url: 'mainmenu/ambildefa',
		success: function(response) {
			var xText = Ext.decode(response.responseText);
			
			if (xText.sukses === true) {
				vUser = xText.kduser;
				vDeptCd = xText.kddept;
				vDeptCount = xText.count;
				vDept = xText.nmdept;
				vDB = xText.nmdb;
				vServer = xText.nmserver;
				
				buatMenu();
			}
		},
		failure: function(response) {
			var xText = Ext.decode(response.responseText);
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK,
				closable: false,
				icon: Ext.MessageBox.INFO,
				message: 'Tampil nilai default Gagal, Koneksi Gagal!!',
				title: 'IDS'
			});
		}
	});

	Ext.get('loading').destroy();
});

Ext.onReady(function() {
	Ext.QuickTips.init();
	
	var datatree = Ext.create('Ext.data.TreeStore', {
		root: {
			text: 'ASTER',
			id: 'aster_node',
			expanded: true
		},
		proxy: {
			type: 'ajax',
			url: 'mainmenu/ambil_nodes'
		},
		reader: {
			type: 'json'
		}
	});
	
	var treePanel = Ext.create('Ext.tree.Panel', {
		store: datatree,
		hideHeaders: true,
        rootVisible: true,
		region: 'north',
        split: true,
        height: '100%',
        minSize: 150,
		title: 'Aster Main Menu',
		rootVisible: false,
		autoScroll: true,
		dockedItems: [{
			xtype: 'toolbar',
			items: [
			{
				text: 'Expand All',
				handler: function(){
					treePanel.expandAll();
				}
			},
			{
				text: 'Collapse All',
				handler: function(){
					treePanel.collapseAll();
				}
			},
			{
				text: 'Logout',
				handler: fnlogout
			}
			]
		}],
		listeners: {
			itemclick: function(view, record, item, index, evt, eOpts)
			{
				// get the click tree node's text and ID attribute values.
				var nodeText = record.get('text');
				var tabId = record.get('id');
				//var id_arr = array();
				var id_arr = tabId.split('|');
				var tableaf = record.get('leaf');
				//var tablet = record.get('href');
				
				// if the ID starts with 'branch', then you clicked on a tree branch, instead of a leaf.
				/*
				if (tabId.toLowerCase().substr(0, 6) == 'branch') {
					Ext.Msg.alert('Tree Branch Clicked', 'You clicked on tree branch ' + nodeText);
					return;
				}*/
				if (tableaf == false) {
					//Ext.Msg.alert('Tree Folder Clicked', 'You clicked on tree folder ' + nodeText);
					return;
				}
				
				// get a reference to the target Tab Panel.
				var tabPanel = Ext.ComponentQuery.query('viewport tabpanel')[0];
				
				// does the tab already exist?  Note the use of the '#' symbol to indicate
				// your looking for an object with the specified itemId.
				var tab = tabPanel.child('#' + id_arr[0]);//tabId
				
				// if tab not already present, create it and add to tab panel.
				if (tab == null) {
					tab = Ext.create('Ext.panel.Panel', {//Ext.create('Ext.ux.IFrame', {//
					//html: id_arr[1],//tablet,//nodeText + ' Content Here',
					itemId: id_arr[0],//tabId,
					title: nodeText,
					/*loader: {
						autoLoad: false,
						scripts: true,
						url: Id_arr[1],
						loadMask: true
					},*/
					autoLoad: {url: id_arr[1], callback: this.initSearch, scope: this},//tabId
					closable: true,
					//renderto: 'tabPanel'
					});
					tabPanel.add(tab);
					tabPanel.doLayout();
				}
				
				// set the tab as active/on-top.
				tabPanel.setActiveTab(tab);
				//this.callParent();
				/*
				var url = t.getAttributeNS('ext', 'url');
                window.open(url);
				return this.callParent(arguments);
				*/
			}
		},
	});
	/*
    treePanel.getSelectionModel().on('select', function(selModel, record) {
        if (record.get('leaf')) {
            Ext.getCmp('content-panel').layout.setActiveItem(record.getId() + '-panel');
			if (!detailEl) {
				var bd = Ext.getCmp('details-panel').body;
				bd.update('').setStyle('background','#fff');
				detailEl = bd.createChild(); //create default empty div
            }
            detailEl.hide().update(Ext.getDom(record.getId() + '-details').innerHTML).slideIn('l', {stopAnimation:true,duration: 200});
        }
    });
	*/
	/*
	treePanel.on('click', function(n){
		var tabtitle = n.text; //n.text is the title for the tab, we can then use n.text if the tab has been added or not yet
		var varname = n.id;  //n.id is the name of the var for the TabPanel items to be added
		var tab = Ext.getCmp('content-panel').items.find(function(i){
				return i.title === tabtitle}); 
		if (!tab)
		{
			var tp = Ext.getCmp('content-panel'); //content-panel is the center region id of the viewport with xtype:'tabpanel'
			tp.add(varname); //error here, how can I add tabitem from using the n.id as the variable name????, let say n.text = 'windowFormTab'         
			tp.doLayout(); 
			tp.setTitle(tabtitle);         
			tp.setActiveTab(tp.items.getCount() - 1);
		}
		else
		{
			Ext.getCmp('content-panel').setActiveTab(varname);   //also here i dont how to set it active
		}
	});
	*/
	/*
	var tabCenter = Ext.Toolbar({
		items: ['->', {
			icon: "<?php echo base_url();?>assets/img/icons/minus-circle.png",
			text: 'Logout',
			handler: do_logout
		}]
	});*/
	
    var tabPanel = {
		layoutOnTabChange: true,
		deferredRender: true,
		renderTo: Ext.getBody(),
		id: 'content-panel',//'center-region',
		region: 'center', // this is what makes this panel into a region within the containing layout
		//layout: 'card',
		xtype: 'tabpanel',
		layout: 'fit',
		margins: '2 5 5 0',
		activeItem: 0,
		border: false,
		items: []
    };
    
    Ext.create('Ext.Viewport', {
        layout: 'border',
        title: 'Aster - Aplikasi Sistem Terpadu',
        items: [
		{
            xtype: 'box',
            id: 'header',
            region: 'north',
            height: 10
        },
		{
            layout: 'border',
            id: 'layout-browser',
            region: 'west',
			collapsible: true,
            border: false,
            split: true,
            margins: '2 0 5 5',
            width: 250,
            minSize: 100,
            maxSize: 500,
            items: [treePanel]
        },
            tabPanel
        ],
		//this.callParent();*/
        renderTo: Ext.getBody()
    });
	
	function fnlogout() {
		Ext.Ajax.request({
			url: 'login/logout',
			method: 'POST',
			success: function() {
				window.location = 'login';
			}
		});
	}
});
IOH.Navigator = Ext.extend(Ext.Panel, 
{
	initComponent: function () {
		var items = [];
		Ext.each(IOH.mainMenu, function (group) {
			items.push(this._mainMenuGroup(group));
		}, this);
		var config = {
			defaults: {
	        	border: false,
	        	bodyStyle: 'padding: 10px'
	        },
	        region:'west',
	        id:'west-panel',
	        split:true,
	        collapseMode: 'mini',
	        width: 200,
	        minSize: 175,
	        maxSize: 400,
	        collapsible: true,
	        layout:'accordion',
	        layoutConfig:{
	            animate:true
	        },
	        items: items
		};
		
		Ext.apply(this, config);
		Ext.apply(this.initialConfig, config);
		
		IOH.Navigator.superclass.initComponent.apply(this, arguments);
	},
	
	_mainMenuGroup: function (config) {
		config.xtype = config.xtype || 'treepanel';
		
		if (config.xtype == 'treepanel' && config.items) {
			var children = [];
			for (var i = 0; i < config.items.length; i++) {
				children[i] = Ext.apply({
					leaf: true
				}, config.items[i]);
				if ((!children[i].listeners || !children[i].listeners.click)) {
					if (!children[i].listeners) {
						children[i].listeners = {};
					}
					children[i].listeners.click = function () {
						IOH.APP.activate(this.attributes['activate']);
					}
				}
			}
			delete config.items;
			
			config = Ext.apply(config, {
	            root: new Ext.tree.AsyncTreeNode({children:children}),
		        rootVisible:false,
		        lines:false,
		        autoScroll:true
			}); 
		}
		
		return config;
	},
	
	go: function (tab, component) {
		tab = this.getComponent(tab);
		
		if (!tab) {
			return;
		}
		
		this.getLayout().setActiveItem(tab);
		
		if (component) {
			IOH.APP.activate(component);
		}
	}
});

Ext.reg('IOH.Navigator', IOH.Navigator);
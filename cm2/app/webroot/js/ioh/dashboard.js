IOH.Dashboard = Ext.extend(Ext.Panel, 
{
	initComponent: function () {
		var config = {
			id: 'dashboard',
			html: 'Dashboard'
		};
	
		Ext.apply(this, config);
		Ext.apply(this.initialConfig, config);
		
		IOH.Dashboard.superclass.initComponent.apply(this, arguments);
	}
});

Ext.reg('IOH.Dashboard', IOH.Dashboard);
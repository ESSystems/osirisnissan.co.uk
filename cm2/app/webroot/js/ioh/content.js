IOH.Content = Ext.extend(Ext.Container, 
{
	initComponent: function () {
		var config = {
	        region:'center',
	        border: false,
	        id:'content-panel',
	        layout:'card',
	        activeItem: 0,
	        items: [{
	        	xtype: 'IOH.Dashboard'
	        }]
		};
	
		Ext.apply(this, config);
		Ext.apply(this.initialConfig, config);
		
		IOH.Content.superclass.initComponent.apply(this, arguments);
	}
});

Ext.reg('IOH.Content', IOH.Content);
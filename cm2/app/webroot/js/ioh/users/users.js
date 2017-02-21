IOH.Users = Ext.extend(Ext.Panel, 
{
	
	initComponent: function () {
		this.userForm = new IOH.UsersForm({	        
	        autoScroll: true,
	        autoHeight: true,
			region: 'north',
			split: true,
			border: true
		});
		
		this.userGrid = new IOH.UsersGrid({
			region: 'center',
			layout: 'fit',
			minHeight: 150,
			maxHeight: 300
		});
	
		var config = {
	    	layout: 'border',
			border: false,
	    	items: [
	    		this.userForm, 
	    		this.userGrid
	    	]
		};
		
		Ext.apply(this, config);
		IOH.Users.superclass.initComponent.apply(this, arguments);
		
		this.userGrid.reload({});
	},
	
	loadUser: function (id) {
		this.userForm.loadUser(id);
	}

});

Ext.reg('IOH.Users', IOH.Users);
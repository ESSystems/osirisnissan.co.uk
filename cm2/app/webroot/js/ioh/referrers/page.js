/**
 * 
 */
Ext.ns('IOH.referrers');

IOH.referrers.page = Ext.extend(Ext.Container,
{
	initComponent: function() {
		this.referrerForm = new IOH.referrers.form({
			region: 'north',
			autoHeight: true,
			split: true
		});
		this.referrersGrid = new IOH.referrers.grid({
			region: 'center',
			layout: 'fit',
			minHeight: 150,
			maxHeight: 300
		});
		var cfg = {
			layout: 'border',
			items: [
			        this.referrerForm,
			        this.referrersGrid
			]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.referrers.page.superclass.initComponent.apply(this, arguments);
		
		this.referrersGrid.reload({});
	},
	
	loadReferrer: function (id) {
		this.referrerForm.loadReferrer(id);
	}
});

Ext.reg('IOH.referrers.page', IOH.referrers.page);

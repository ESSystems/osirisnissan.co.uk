/**
 * 
 */
Ext.ns('IOH.Triages.Referrals.Details');

IOH.Triages.Referrals.Details.Declined = Ext.extend(Ext.Panel,
{
	initComponent: function () {
		var cfg = {
			layout: {
				type: 'hbox',
				align: 'stretch'
			},
			padding: 10,
			defaults: {
				xtype: 'container',
				style: 'padding-right: 10px;',
				data: {}
			},
			items: [{
				width: 200,
				tpl: [
				    '<h1>Declined</h1>',
				    '<tpl if="values.Declination" for="Declination">',
				    '<p>on: {created}</p>',
				    '<p>by: {created_by}</p>',
				    '</tpl>'
				]
			},{
				flex: 1,
				tpl:[
				    '<h3>Reason</h3>',
				    '<hr />',
				    '<tpl if="values.Declination" for="Declination">',
				    '<p>{reason}</p>',
				    '</tpl>'
				]
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Triages.Referrals.Details.Declined.superclass.initComponent.apply(this, arguments);
		
		this.on({
			bind: this.onBind, 	
			unbind: this.onUnbind, 	
			scope: this
		});
	},

	onBind: function (id, rec) {
		this.items.each(function (i) {
			date = new Date.parseDate(rec.json.Declination.created,"Y-m-d H:i:s");
			rec.json.Declination.created = Ext.util.Format.date(date, "d/m/Y H:i");
			i.update(rec.json);
		});
		this.bindRec = rec;
	},
	
	onUnbind: function () {
		this.items.each(function (i) { i.update(null); });
		this.bindRec = null;
	}
});


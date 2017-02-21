Ext.ns('IOH.Person');

IOH.Person.Summary = Ext.extend(Ext.Panel,
{
	initComponent: function () {
		var cfg = {
			layout: 'border',
			border: false,
			defaults: {
				padding: '10px'
			},
			items: [new IOH.Person.Summary.Form({
				ref: 'summaryForm',
				region: 'north',
				split: true,
				autoHeight: true,
				collapsible: true,
				collapseMode: 'mini'
			}), new IOH.Person.Summary.Summary({
				ref: 'summaryPanel',
				region: 'center',
				disabled: true
			})]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Person.Summary.superclass.initComponent.apply(this, arguments);
		
		this.summaryPanel.relayEvents(this.summaryForm, ['getsummary', 'printsummary']);
	}
});

Ext.reg('IOH.Person.Summary', IOH.Person.Summary);
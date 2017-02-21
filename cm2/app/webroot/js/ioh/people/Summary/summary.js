Ext.ns('IOH.Person.Summary');

IOH.Person.Summary.Summary = Ext.extend(Ext.Panel,
{
	initComponent: function () {
		var cfg = {
			bodyCssClass: ['story', 'person-summary'],
			autoScroll: true
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Person.Summary.Summary.superclass.initComponent.apply(this, arguments);
		
		this.on('getsummary', this.loadSummary, this);
		this.on('printsummary', this.printSummary, this);
	},
	
	loadSummary: function (personId) {
		this.load({
			url: '/persons/summary/' + personId,
			callback: function () {
				this.setDisabled(false);
			},
			scope: this
		});
	},
	
	printSummary: function (personId) {
		window.open('/persons/summary/' + personId);
	}
});

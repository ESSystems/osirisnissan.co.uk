Ext.ns('IOH.Person.Summary');

IOH.Person.Summary.Form = Ext.extend(Ext.form.FormPanel,
{
	title: 'Select Person',
	
	initComponent: function () {
		this.getSummaryAction = new Ext.Action({
			text: 'Get Summary',
			handler: this.getSummary,
			scope: this,
			cls: 'x-btn-text-icon',
			iconCls: 'page_add',
			disabled: false
		});
		this.printSummaryAction = new Ext.Action({
			text: 'Print',
			handler: this.printSummary,
			scope: this,
			cls: 'x-btn-text-icon',
			iconCls: 'printer',
			disabled: false
		});

		var cfg = {
			labelAlign: 'right',
			items: [new IOH.PersonCombo({
	            fieldLabel: 'Person',
	            hideLabel: true,
	            name: 'PersonName',
	            hiddenName: 'Person.id',
	            width: 300,
	            showLeavers: true
        	})],
        	buttons: [this.getSummaryAction, this.printSummaryAction],
        	buttonAlign: 'left'
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Person.Summary.Form.superclass.initComponent.apply(this, arguments);
	},
	
	getSummary: function () {
		var personId = this.getForm().findField('Person.id').getValue();
		
		if (!personId) {
			alert('Select a person first');
			return;
		}
		
		this.fireEvent('getsummary', personId);
	},
	
	printSummary: function () {
		var personId = this.getForm().findField('Person.id').getValue();
		
		if (!personId) {
			alert('Select a person first');
			return;
		}
		
		this.fireEvent('printsummary', personId);
	}
});
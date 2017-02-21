Ext.ns('IOH.System');

IOH.System.ImportCSVForm = Ext.extend(Ext.FormPanel,
{
	initComponent: function () {
		var config = {
			title: 'Import Employees from a CSV file',
			hideBorders: true,
			bodyStyle: 'padding: 15px',
			id: 'import-employees-form',
			url: '/employees/import.json',
			fileUpload: true,
			plain: true,
			labelAlign: 'right',
			items: [
				{
					xtype: 'component',
					autoEl: {
						tag: 'div',
						cls: 'x-form-item',
						style: 'font-size: 1em;',
						html: 'Please, select the file containing employees data and click "Import".'
					}
				}, 
				{
					xtype: 'field',
					inputType: 'file',
					fieldLabel: 'Employee CSV file',
					name: 'Employee.file'
				}, {
					xtype: 'combo',
					mode: 'local',
					triggerAction: 'all',
					hiddenName: 'Employee.import_format',
					value: 'sap',
					store: {
						xtype: 'arraystore',
						fields: ['type', 'label'],
						data: [['old', 'Legacy Format'],['sap', 'SAP Format']]
					},
					valueField: 'type',
					displayField: 'label',
					editable: false,
					forceSelection: true,
					fieldLabel: 'Import Format'
				}, {
					xtype: 'button',
					fieldLabel: ' ',
					labelSeparator: '',
					text: 'Import',
					handler: function () {
						this.el.mask('Importing employees, please wait ...', 'x-mask-loading');
						this.getForm().submit({
							timeout: 10*60,
							success: function(form, action) {
								this.el.unmask(true);
								Ext.Msg.alert('Success', action.result.msg);
							},
							failure: function(form, action) {
								this.el.unmask(true);
								switch (action.failureType) {
									case Ext.form.Action.CLIENT_INVALID:
										Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
										break;
									case Ext.form.Action.CONNECT_FAILURE:
										Ext.Msg.alert('Failure', 'Ajax communication failed');
										break;
									case Ext.form.Action.SERVER_INVALID:
										Ext.Msg.alert('Failure', action.result.msg);
								}
							},
							scope: this
						});
					},
					scope: this
				}
			]
		};
		
		Ext.apply(this, config);
		Ext.apply(this.initialConfig, config);
		
		IOH.System.ImportCSVForm.superclass.initComponent.apply(this, arguments);
	}
});

Ext.reg('IOH.System.ImportCSVForm', IOH.System.ImportCSVForm);
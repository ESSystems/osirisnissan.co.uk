/**
 * 
 */
IOH.AppointmentsGrid = Ext.extend(Ext.grid.GridPanel,
{
	viewConfig: {
		forceFit: true
	},
	initComponent: function () {
		this.editAppointmentAction = new Ext.Action({
			text: 'Edit',
			handler: this.editAppointment,
			scope: this,
			cls: 'x-btn-text-icon',
			iconCls: 'page_edit',
			disabled: true
		});
		
		var cfg = {
			columns: [{
				header: 'When',
				dataIndex: Ext.ensible.cal.EventMappings.Period.name
			},{
				header: 'Title',
				dataIndex: Ext.ensible.cal.EventMappings.Title.name
			}],
			loadMask: true,
			tbar: [this.editAppointmentAction],
			sm: new Ext.grid.RowSelectionModel({singleSelect: true})
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.AppointmentsGrid.superclass.initComponent.apply(this, arguments);
		
		this.getSelectionModel().on('selectionchange', this.selectionChange, this);
		
		this.store.on('save', function (store) {
			store.reload();
			this.calendar.store.reload();
			this.calendar.getActiveView().dismissEventEditor();
		}, this);
	},
	
	editAppointment: function () {
		var sel = this.getSelectionModel().getSelected();
		
		if (!sel) {
			return;
		}
		
		this.calendar.getActiveView().showEventEditor(sel);
	},
	
	selectionChange: function (sm) {
		this.editAppointmentAction.setDisabled(sm.getSelections().length == 0);
	}
});
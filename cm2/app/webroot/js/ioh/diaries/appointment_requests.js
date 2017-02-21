/**
 * 
 */
IOH.AppointmentRequests = Ext.extend(Ext.grid.GridPanel,
{

	initComponent: function () {
		
		this.createAppointmentAction = new Ext.Action({
			text: 'Create Appointment',
			handler: this.createAppointment,
			scope: this,
			cls: 'x-btn-text-icon',
			iconCls: 'page_add',
			disabled: true
		});
		
		var store = new Ext.data.DirectStore({
			directFn: Server.Referrals.direct_index,
			fields: [{
				name: 'Referral.id', type: 'int'
			},{
				name: 'Person.full_name'
			},{
				name: 'Person.id', type: 'int'
			},{
				name: 'Referral.case_nature'
			},{
				name: 'PatientStatus.status'
			},{
				name: 'ReferralReason.reason'
			},{
				name: 'Referral.referral_reason_id'
			},{
				name: 'Referral.case_reference_number'
			}],
			root: 'data',
			idProperty: 'Referral.id'
		}); 
		
		var pagingToolbar = new Ext.PagingToolbar({
			pageSize: 50,
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying referrals {0} - {1} of {2}',
			emptyMsg: "No referrals found."
		});
		
		var cfg = {
			store: store,
			
			columns: [{
				dataIndex: 'Referral.case_reference_number',
				header: 'Ref.No.',
				width: 60
			},{
				dataIndex: 'Person.full_name',
				header: 'Patient',
				id: 'full-name'
			},{
				dataIndex: 'PatientStatus.status',
				header: 'Status',
				width: 50
			},{
				dataIndex: 'ReferralReason.reason',
				header: 'Reason',
				width: 150
			}],
			
			viewConfig: {
				forceFit: true,
				autoExpandColumn: 'full-name'
			},
			
			loadMask: true,
			
			sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
			
			tbar: [this.createAppointmentAction],
			
			bbar: pagingToolbar
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.AppointmentRequests.superclass.initComponent.apply(this, arguments);
		
		this.on('render', this.store.load, this.store);
		this.on('eventadd', this.onEventAdd, this);
		this.getSelectionModel().on('selectionchange', this.selectionChange, this);
	},

	createAppointment: function () {
		var rec = this.getSelectionModel().getSelected();
		
		if (!rec) {
			return;
		}
		
		var appointment = new Ext.ensible.cal.EventRecord();

		appointment.data[Ext.ensible.cal.EventMappings.ReferralId.name] = rec.id;	
		appointment.data[Ext.ensible.cal.EventMappings.PersonName.name] = rec.get('Person.full_name');	
		appointment.data[Ext.ensible.cal.EventMappings.PersonId.name] = rec.get('Person.id');	
//		appointment.data[Ext.ensible.cal.EventMappings.StartDate.name] = rec.get('Referral.preferred_date');	
//		appointment.data[Ext.ensible.cal.EventMappings.EndDate.name] = rec.get('Referral.preferred_date');	
		appointment.data[Ext.ensible.cal.EventMappings.Type.name] = '';	
		appointment.data[Ext.ensible.cal.EventMappings.DiagnosisId.name] = '';	
		//appointment.data[Ext.ensible.cal.EventMappings.ReferralReasonId.name] = rec.get('Referral.referral_reason_id');
		//appointment.data[Ext.ensible.cal.EventMappings.CaseNature.name] = rec.get('Referral.case_nature');
		//appointment.data[Ext.ensible.cal.EventMappings.CaseRefNo.name] = rec.get('Referral.case_reference_number');
		
		this.showEventEditor(appointment);
	},
	
	selectionChange: function (sm) {
		this.createAppointmentAction.setDisabled(sm.getSelections().length == 0);
	},
	
	onEventAdd: function(cal, rec) {
//		if (rec.get(Ext.ensible.cal.EventMappings.EventId.name)) {
//			IOH.APP.feedback('Success', 'Appointment created');
//			this.store.reload();
//		} else {
//			IOH.APP.feedback('Could not save appointment', 'Please check that all required conditions are met!');
//		}
	}
});
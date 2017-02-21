Ext.ns('IOH.Appointments.Referral');

IOH.Appointments.Referral.Form = Ext.extend(Ext.form.FormPanel,
{
	initComponent: function () {
		var personCombo = new IOH.PersonCombo({
            fieldLabel: 'Person',
            name: 'PersonName',
            hiddenName: 'Referral.person_id'
	    });
		
		var cfg = {
			api: {
				submit: Server.Referrals.direct_save
			},
			layout: 'form',
			labelAlign: 'right',
			defaults: {
				anchor: '-20px',
				xtype: 'textfield'
			},
			items: [{
				xtype: 'hidden',
				name: 'Referrer.full_name'
			},{
				xtype: 'hidden',
				name: 'Referrer.referrer_type_id'
			},personCombo,{
				xtype: 'combo',
				hiddenName: 'Referral.patient_status_id',
				mode: 'remote',
				triggerAction: 'all',
				store: {
					xtype: 'directstore',
					directFn: Server.PatientStatus.direct_index,
					fields: [{
						name: 'id', mapping: 'PatientStatus.id', type: 'int'
					},{
						name: 'status', mapping: 'PatientStatus.status', type: 'string'
					}],
					root: 'data'
				},
				displayField: 'status',
				valueField: 'id',
				fieldLabel: 'Status'
			},{
				xtype: 'textarea',
				name: 'Referral.case_nature',
				fieldLabel: 'Case Nature'
			},{
				xtype: 'textarea',
				name: 'Referral.job_information',
				fieldLabel: 'Job Information'
			},{
				xtype: 'textarea',
				name: 'Referral.history',
				fieldLabel: 'History'
			},{
				xtype: 'compositefield',
				fieldLabel: 'Sickness Period',
				defaults: {
					flex: 1
				},
				items: [{
					xtype: 'xdatefield',
					name: 'Referral.sickness_started'
				},{
					xtype: 'xdatefield',
					name: 'Referral.sicknote_expires'
				}]
			},{
				xtype: 'combo',
				hiddenName: 'Referral.referral_reason_id',
				mode: 'remote',
				triggerAction: 'all',
				store: {
					xtype: 'directstore',
					directFn: Server.ReferralReason.direct_index,
					fields: [{
						name: 'id', mapping: 'ReferralReason.id', type: 'int'
					},{
						name: 'reason', mapping: 'ReferralReason.reason', type: 'string'
					}],
					root: 'data'
				},
				displayField: 'reason',
				valueField: 'id',
				fieldLabel: 'Referral Reason'
			},{
				xtype: 'compositefield',
				items: [{
					xtype: 'combo',
					hiddenName: 'Referral.operational_priority_id',
					mode: 'remote',
					triggerAction: 'all',
					store: {
						xtype: 'directstore',
						directFn: Server.OperationalPriority.direct_index,
						fields: [{
							name: 'id', mapping: 'OperationalPriority.id', type: 'int'
						},{
							name: 'operational_priority', mapping: 'OperationalPriority.operational_priority', type: 'string'
						}],
						root: 'data'
					},
					displayField: 'operational_priority',
					valueField: 'id',
					fieldLabel: 'Operational Priority',
					flex: 1
				},{
					xtype: 'checkbox',
					name: 'Referral.private',
					boxLabel: 'Private',
					width: 100,
					anchor: '0px',
					style: 'text-align: right;'
				}]
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Appointments.Referral.Form.superclass.initComponent.apply(this, arguments);
		
		this.on('save', this.onSave, this);
	},
	
	onSave: function () {
		var f = this.getForm(), o = this.options;
		
		f.findField('Referrer.referrer_type_id').setValue(o.referrerTypeId);
		f.findField('Referrer.full_name').setValue(o.referrerName);
		
		this.getForm().submit({
			success: function(form, action) {
				var referralData = form.getValues();

				referralData['Referral.id']                    = action.result.id; 
				referralData['Referral.case_reference_number'] = action.result.case_reference_number
				
				this.fireEvent('saved', referralData);
				
				IOH.APP.feedback('Success', 'Referral created successfully');
			},
			scope: this
	    });
	},
	
	setPerson: function (Person) {
		var f = this.form, p = f.findField('Referral.person_id');
		
		f.reset();
		
		p.getStore().loadData({
			rows: [{
				Person: {
					id: Person.id,
					full_name: Person.name
				},
				Patient: {
					Organisation: {}
				},
				Employee: {}
			}]
		});
		
		p.setValue(Person.id);
	}
});
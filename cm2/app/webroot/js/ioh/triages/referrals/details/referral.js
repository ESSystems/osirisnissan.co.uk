/**
 *
 */

Ext.ns('IOH.Triages.Referrals.Details');

IOH.Triages.Referrals.Details.Referral = Ext.extend(Ext.TabPanel,
{
	type: '',

	height: 300,
	split: true,

	border: true,

	initComponent: function () {
		var cfg = {
			activeTab: 0,
			plain:true,
	        layoutOnTabChange : true,
	        defaults: {
				xtype: 'panel',
				data: {},
				autoScroll: true,
				padding: 10
			},
			items: [{
				title: 'Referral Details',
				layout: {
					type: 'hbox'
				},
				defaults: {
					xtype: 'container',
					flex: 1,
					style: 'padding-right: 10px;',
					data: {},
					autoHeight: true
				},
				items: [{
					tpl: [
						'<h1>',
							'<tpl if="values.Person" for="Person">{full_name}',
							'</tpl>',
							'<tpl if="values.PatientStatus" for="PatientStatus"> ({status})</tpl>',
							'<tpl if="!values.Person">Select a referral ...</tpl>',
						'</h1><hr />',
						'<p>Reason: ',
						'<tpl if="values.ReferralReason" for="ReferralReason">',
							'{reason}',
						'</tpl>',
						'<tpl if="!values.ReferralReason">',
							'<span class="quiet">N/A</span>',
						'</tpl>',
						'</p>',
						'<p>',
							'Case Referrence Number: ',
							'<tpl if="values.Referral && values.Referral.case_reference_number">',
								'<tpl for="Referral">{case_reference_number}</tpl>',
							'</tpl>',
							'<tpl if="!values.Referral || !values.Referral.case_reference_number">',
								'<span class="quiet">N/A</span>',
							'</tpl>',
						'</p>',
						'<p>',
							'Date sickness absence started: ',
						'<tpl if="values.Referral && values.Referral.sickness_started && values.Referral.sickness_started != \'0000-00-00\'">',
							'<tpl for="Referral">{[ fm.date(Date.parseDate(values.sickness_started, \'Y-m-d\'), Ext.form.DateField.prototype.format ) ]}</tpl>',
						'</tpl>',
						'<tpl if="!values.Referral || !values.Referral.sickness_started || values.Referral.sickness_started == \'0000-00-00\'">',
							'<span class="quiet">N/A</span>',
						'</tpl>',
						'</p>',
						'<p>',
							'Date current sicknote expires: ',
						'<tpl if="values.Referral && values.Referral.sicknote_expires && values.Referral.sicknote_expires != \'0000-00-00\'">',
							'<tpl for="Referral">{[ fm.date(Date.parseDate(values.sicknote_expires, \'Y-m-d\'), Ext.form.DateField.prototype.format ) ]}</tpl>',
						'</tpl>',
						'<tpl if="!values.Referral || !values.Referral.sicknote_expires || values.Referral.sicknote_expires == \'0000-00-00\'">',
							'<span class="quiet">N/A</span>',
						'</tpl>',
						'</p>',
						'<p>',
							'Operational Priority: ',
							'<tpl if="values.OperationalPriority && values.Referral.operational_priority_id">',
								'<tpl for="OperationalPriority">{operational_priority}</tpl>',
							'</tpl>',
							'<tpl if="!values.OperationalPriority || !values.Referral.operational_priority_id">',
								'<span class="quiet">N/A</span>',
							'</tpl>',
						'</p>',
						'<p>',
							'Created at: ',
							'<tpl if="values.Referral && values.Referral.created_at">',
								'<tpl for="Referral">{created_at}</tpl>',
							'</tpl>',
							'<tpl if="!values.Referral || !values.Referral.created_at">',
								'<span class="quiet">N/A</span>',
							'</tpl>',
						'</p>',
						'<p>',
							'Private: ',
							'<tpl if="values.Referral && values.Referral.private">',
								'<tpl for="Referral" if="values.Referral.private == 1">Yes</tpl>',
								'<tpl for="Referral" if="values.Referral.private == 0">No</tpl>',
							'</tpl>',
							'<tpl if="!values.Referral || !values.Referral.private">',
								'<span class="quiet">N/A</span>',
							'</tpl>',
						'</p>'
					]
				},{
					ref: 'caseNature',
					tpl: [
					    '<h3>Case Nature</h3>',
					    '<hr />',
					    '<tpl if="values.Referral && values.Referral.case_nature">',
				      		'<tpl for="Referral">{case_nature}</tpl>',
				        '</tpl>',
				        '<tpl if="!values.Referral || !values.Referral.case_nature">',
				      	'<span class="quiet">N/A</span>',
				        '</tpl>'
					]
				},{
					tpl: [
					    '<h3>Job Information</h3>',
					    '<hr />',
					    '<tpl if="values.Referral && values.Referral.job_information">',
				      		'<tpl for="Referral">{job_information}</tpl>',
				        '</tpl>',
				        '<tpl if="!values.Referral || !values.Referral.job_information">',
				      	'<span class="quiet">N/A</span>',
				        '</tpl>',
					]
				},{
					style: 'padding-right: 0;',
					tpl: [
				      '<h3>History</h3>',
					    '<hr />',
					    '<tpl if="values.Referral && values.Referral.history">',
				      		'<tpl for="Referral">{history}</tpl>',
				        '</tpl>',
				        '<tpl if="!values.Referral || !values.Referral.history">',
				      	'<span class="quiet">N/A</span>',
				        '</tpl>'
				    ]
				}]
		    },{
				title: 'Employee',
				tpl: [
					'<tpl if="values.Person" for="Person">',
						'<tpl if=="values.Employee.sap_number || values.Employee.salary_number" for="Employee">',
							'<table class="details">',
								'<thead><tr>',
									'<th>SAP</th>',
									'<th>Salary Number</th>',
									'<th>Employment Start Date</th>',
								'</tr></thead>',
								'<tr>',
									'<td>{sap_number}</td>',
									'<td>{salary_number}</td>',
									'<td>{employment_start_date}</td>',
								'</tr>',
							'</table>',
						'</tpl>',
						'<tpl if="!values.Employee.sap_number && !values.Employee.salary_number">',
							'<p class="quiet">No information about the employee records!</p>',
						'</tpl>',
					'</tpl>',
					'<tpl if="!values.Person">',
						'<p class="quiet">No information about the employee records!</p>',
					'</tpl>'
				]
			},{
				title: 'Referrer & Followers',
				tpl: [
					'<div class="half left"><h2>Referrer</h2>',
					'<tpl if="values.Referrer && values.Referrer.id">',
						'<table class="details">',
							'<thead><tr>',
								'<th>Name</th>',
								'<th>Source</th>',
								'<th>Organisation</th>',
							'</tr></thead>',
								'<tpl for="Referrer"><tr>',
								'<td>{values.Person.full_name}</td>',
								'<td>{values.ReferrerType.type}</td>',
								'<td>{values.Organisation.OrganisationName}</td>',
							'</tr></tpl>',
						'</table>',
					'</tpl>',
					'</div>',
					'<div class="half right"><h2>Followers</h2>',
						'<tpl if="values.Follower && values.Follower.length &gt; 0">',
						'<table class="details">',
							'<thead><tr>',
								'<th>Name</th>',
								'<th>Source</th>',
								'<th>Organisation</th>',
							'</tr></thead>',
							'<tpl for="Follower"><tr>',
								'<td>{values.Person.full_name}</td>',
								'<td>{values.ReferrerType.type}</td>',
								'<td>{values.Organisation.OrganisationName}</td>',
							'</tr></tpl>',
						'</table>',
						'</tpl>',
						'<tpl if="!values.Follower || values.Follower.length === 0">',
							'<p class="quiet">No followers for this referral yet!</p>',
						'</tpl>',
					'</div>'
				]
			},{
		        title: 'Appointments',
	        	tpl: [
	        	    '<tpl if="values.Appointment && values.Appointment.length &gt; 0">',
	        	    '<table class="details">',
	        	    	'<thead><tr>',
	        	    		'<th>Type</th>',
	        	    		'<th>When</th>',
        	    			'<th>Diary</th>',
        	    			'<th>Notes</th>',
	        	    	'</tr></thead>',
	        	    	'<tpl for="Appointment"><tr class="appointment {state}">',
	        	    		'<td>{[values.new_or_review || \'new\']}</td>',
	        	    		'<td>{period}</td>',
	        	    		'<td>{values.Diary.name}</td>',
	        	    		'<td>',
	        	    		'Status: {[ values.state == \'new\' ? \'pending\' : values.state ]}',
	    					'<tpl if="state == \'deleted\'">',
	    						' on {[ fm.date(Date.parseDate(values.deleted_on, \'Y-m-d H:i:s\'), Ext.form.DateField.prototype.format + \', \' + Ext.form.TimeField.prototype.format ) ]} by {values.Deleter.full_name}<br/>{deleted_reason}',
        	    			'</tpl>',
	        	    		'</td>',
	        	    	'</tr></tpl>',
					'</table></tpl>',
					'<tpl if="!values.Appointment || values.Appointment.length === 0">',
			      		'<p class="quiet">No appointments for this referral yet!</p>',
			        '</tpl>'
	        	]
		    },{
		        title: 'Declinations',
	        	tpl: [
	        	    '<tpl if="values.Declination && values.Declination.length &gt; 0">',
	        	    '<table class="details">',
	        	    	'<thead><tr>',
	        	    		'<th>When</th>',
        	    			'<th>By</th>',
        	    			'<th>Reason</th>',
	        	    	'</tr></thead>',
	        	    	'<tpl for="Declination"><tr>',
	        	    		'<td>{created}</td>',
	        	    		'<td>{values.Person.full_name}</td>',
	        	    		'<td>{reason}</td>',
	        	    	'</tr></tpl>',
					'</table></tpl>',
					'<tpl if="!values.Declination || values.Declination.length === 0">',
			      		'<p class="quiet">No declinations for this referral!</p>',
			        '</tpl>'
	        	]
		    },{
		        title: 'Cancelation',
	        	tpl: [
	        		'<tpl if="values.Referral && values.Referral.canceled_reason">',
	        	    '<table class="details">',
	        	    	'<thead><tr>',
	        	    		'<th>Reason</th>',
        	    			'<th>Date</th>',
	        	    	'</tr></thead>',
	        	    	'<tpl for="Referral"><tr>',
	        	    		'<td>{canceled_reason}</td>',
	        	    		'<td>{canceled_on}</td>',
	        	    	'</tr></tpl>',
					'</table></tpl>',
					'<tpl if="!values.Referral || !values.Referral.canceled_reason">',
			      		'<p class="quiet">This referral is not canceled!</p>',
			        '</tpl>'
	        	]
		    }],
		    tbar: [{
				text: 'Print referral',
	            handler: function () {
	            	window.open(String.format('/referrals/printPreview/' + this.bindRec.id));
	            },
	            scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'printer'
			},{
				itemId: 'make_private',
				text: 'Make private',
				disabled: true,
	            handler: function () {
	            	referral = this;
	            	Ext.MessageBox.confirm(
	            		'Confirm action',
	            		'Are you sure you want to make this referral private? This action will remove all the followers associated to the referral.',
	            		function(btn) {
	            			if(btn == "yes") {
	            				referral.fireEvent('make_private', referral.bindRec.id);
	            			}
	            		}
	            	);
	            },
	            scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'private'
			}],
		    bbar: [{xtype: 'tbtext', 'text': 'Attachments: '}],
			buttonAlign: 'left',

			ref: 'details',
			autoScroll: true
		};


		if (this.type == 'new') {
			this.actionAccept = new Ext.Action({
				text: 'Accept',
				cls: 'x-btn-text-icon',
				iconCls: 'tick icon',
				scale: 'medium',
				handler: this.onAccept,
				scope: this,
				disabled: true
			});

			this.actionDecline = new Ext.Action({
				text: 'Decline',
				cls: 'btn-text-icon',
				iconCls: 'cross',
				scale: 'medium',
				handler: this.onDecline,
				scope: this,
				disabled: true
			});

			cfg.buttons = [this.actionAccept, '->', this.actionDecline];
		}

		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);

		IOH.Triages.Referrals.Details.Referral.superclass.initComponent.apply(this, arguments);

		this.on('bind', this.onBind, this);
		this.on('unbind', this.onUnbind, this);
		this.on('make_private', this.onMakePrivate, this);

		this.bindRec = null;
	},

	onBind: function (id, rec) {
		date = new Date.parseDate(rec.json.Referral.created_at,"Y-m-d H:i:s");
		if(typeof(date.format) !== 'undefined') {
			rec.json.Referral.created_at = date.format("d/m/Y H:i");
		}

		this.bindRec = rec;
		this._update(rec.json);
	},

	_update: function (json) {
		this.items.each(function (t) {
			t.show(); // show to allow update template
			if (t.items.length) {
				t.items.each(function (i) {
					i.update(json);
				});
			} else {
				t.update(json);
			}
		});

		this.setActiveTab(0);

		if (this.actionAccept) {
			this.actionAccept.setDisabled(json ? false : true);
			this.actionDecline.setDisabled(json ? false : true);
		}

		this._attachments(json);

		tbar = this.getTopToolbar();
		if(json != null && json.Referral.private == "1") {
			tbar.getComponent('make_private').disable();
		} else {
			tbar.getComponent('make_private').enable();
		}
	},

	onAccept: function () {
		if (!this.bindRec) {
			return;
		}
		this.fireEvent('accept', this.bindRec);
	},

	onDecline: function () {
		if (!this.bindRec) {
			return;
		}
		this.fireEvent('decline', this.bindRec);
	},

	onMakePrivate: function(id) {
		Server.Referrals.direct_make_private(
			{ id: id },
			function(result) {
				if (result.success) {
					IOH.APP.feedback("Success", "The referral was changed as private. All registered followers were removed.");
					this.fireEvent('referral_changed');
				} else {
				   IOH.APP.feedback("Error", "The referral could not be marked as private");
				}
			},
			this
		);
	},

	onUnbind: function () {
		this._update(null);
		this.bindRec = null;
	},

	addAttachmentButton: function(tbar, a) {
		tbar.addButton({
			text: a.title || a.document_file_name,
			handler: function () {
				window.open(a.document_url);
			},
			cls: 'x-btn-text-icon',
			iconCls: 'attach icon'
		});

		tbar.doLayout();
	},

	_attachments: function (json) {
		var i, a, tbar = this.getBottomToolbar();

		tbar.removeAll();

		if (json && json.Attachment && json.Attachment.length) {
			tbar.addText(String.format('Attachments ({0}): ', json.Attachment.length));
			for (i = 0; i < json.Attachment.length; i++) {
				a = json.Attachment[i];

				this.addAttachmentButton(tbar, a);
			}
		} else {
			tbar.addText('No Attachments');
			tbar.addFill();
		}

		tbar.doLayout();
	}
});


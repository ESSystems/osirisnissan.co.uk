/**
 * 
 */

Ext.ns('IOH.Triages.Referrals.Details');

IOH.Triages.Referrals.Details.New = Ext.extend(Ext.Panel,
{
	title: 'Referrals Form',
	
	initComponent: function () {
		this.actionAccept = new Ext.Action({
			text: 'Accept',
			cls: 'x-btn-text-icon',
			iconCls: 'tick icon',
			scale: 'medium',
			handler: this.onAccept,
			scope: this,
			disabled1: true
		});
		
		this.actionDecline = new Ext.Action({
			text: 'Decline',
			cls: 'btn-text-icon',
			iconCls: 'cross',
			scale: 'medium',
			handler: this.onDecline,
			scope: this,
			disabled1: true
		});
		
		var cfg = {
			layout: 'hbox',
			padding: 10,
			defaults: {
				xtype: 'container',
				flex: 1,
				style: 'padding-right: 10px;',
				data: {}
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
				      '<tpl if="values.Referral && values.Referral.sickness_started">',
				      	'<tpl for="Referral">{sickness_started}</tpl>',
				      '</tpl>',
				      '<tpl if="!values.Referral || !values.Referral.sickness_started">',
				      	'<span class="quiet">N/A</span>',
				      '</tpl>',
			      '</p>',
			      '<p>',
		      	  	  'Date current sicknote expires: ',
				      '<tpl if="values.Referral && values.Referral.sicknote_expires">',
				      	'<tpl for="Referral">{sicknote_expires}</tpl>',
				      '</tpl>',
				      '<tpl if="!values.Referral || !values.Referral.sicknote_expires">',
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
			}],
			bbar: [{xtype: 'tbtext', 'text': 'Attachments: '}, '->', {text: ' '}],
			buttons: [this.actionAccept, '->', this.actionDecline],
			buttonAlign: 'left'
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Triages.Referrals.Details.New.superclass.initComponent.apply(this, arguments);
		
		this.on('bind', this.onBind, this);
		this.on('unbind', this.onUnbind, this);
		
		this.bindRec = null;
		this.actionAccept.disable();
		this.actionDecline.disable();
	},

	onBind: function (id, rec) {
		this.items.each(function (i) {
			date = new Date.parseDate(rec.json.Referral.created_at,"Y-m-d H:i:s");
			rec.json.Referral.created_at = Ext.util.Format.date(date, "d/m/Y H:i");
			i.update(rec.json);
		});
		this.actionAccept.enable();
		this.actionDecline.enable();
		this._attachments(rec.json);
		this.bindRec = rec;
	},
	
	onUnbind: function () {
		this.items.each(function (i) { i.update(null); });
		this.actionAccept.disable();
		this.actionDecline.disable();
		this.bindRec = null;
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
	
	downloadAttachment: function(id, fingerprint, file_name) {
		file_url = String.format(
			'/documents/download/{0}/{1}/{2}/', 
			id, fingerprint, file_name 
		);
		
		Ext.Ajax.request({
		   url: file_url,
		   success: function (result, request) {
			   var jsonData = Ext.util.JSON.decode(result.responseText);
			   if(jsonData.success) {
				   window.open(jsonData.data);
			   } else {
				   IOH.APP.feedback("Download error", "The file you requested could not be found on the server");
			   }
		   },
		   failure: function () {
			   IOH.APP.feedback("Download error", "There was a problem when contacting the server");
		   }
		});
		
	},
	
	addAttachmentButton: function(tbar, a) {
		file_url = String.format(
			'/documents/download/{0}/{1}/{2}/', 
			a.id, a.document_fingerprint || " ", a.document_file_name 
		);

		Ext.Ajax.request({
			url: file_url,
			success: function (result, request) {
				var jsonData = Ext.util.JSON.decode(result.responseText);
				if(jsonData.success) {
					tbar.addButton({
						text: a.title || a.document_file_name,
						handler: function () {
							window.open(jsonData.data);
						},
						cls: 'x-btn-text-icon',
						iconCls: 'attach icon'
					});
					
					tbar.addButton({
						text: ' '
					});
					tbar.doLayout();
				}
			},
			failure: function () {
				IOH.APP.feedback("Download error", "There was a problem when contacting the server");
			}
		});
	},
	
	_attachments: function (json) {
		var i, a, tbar = this.getBottomToolbar();
		
		tbar.removeAll();
		
		if (json.Attachment && json.Attachment.length) {
			tbar.addText(String.format('Attachments ({0}): ', json.Attachment.length));
			for (i = 0; i < json.Attachment.length; i++) {
				a = json.Attachment[i];
				
				this.addAttachmentButton(tbar, a);
			}
		} else {
			tbar.addText('No Attachments');
			tbar.addFill();
			tbar.addButton({
				text: ' '
			});
		}
		
		tbar.doLayout();
	}
});


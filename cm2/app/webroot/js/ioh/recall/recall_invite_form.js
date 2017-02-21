IOH.RecallInviteForm = Ext.extend(Ext.form.FormPanel,
{
	title: 'Add / Edit Invite',
	
	initComponent: function () {
		var cfg = {
			defaults: {
				xtype: 'textfield',
				anchor: '-25px'
			},
			labelAlign: 'right',
			items: [{
				xtype: 'hidden',
				name: 'RecallListItemEvent.id'
			},{
				xtype: 'hidden',
				name: 'RecallListItemEvent.recall_list_item_id'
			},{
				hiddenName: 'RecallListItemEvent.contact_type',
				fieldLabel: 'Contact Type',
				xtype: 'combo',
				mode: 'local',
				triggerAction: 'all',
				store: {
					xtype: 'arraystore',
					idIndex: 0,
					fields: ['contact_type'],
			        data: [['Email 1'],['Email 2'],['Email 3'],['Informed HR'],['Informed HandS'],['Advised by OH Staff'],['Creating List'],['Appointment Made']]
				},
				displayField: 'contact_type',
				valueField: 'contact_type'
			},{
				name: 'RecallListItemEvent.invite_date',
				fieldLabel: 'Contact Date',
				xtype: 'xdatefield',
				value: (new Date())
			},{
				xtype: 'xdatefield',
				name: 'RecallListItemEvent.recall_date',
				fieldLabel: 'Recall Date'
			},{
				xtype: 'xdatefield',
				name: 'RecallListItemEvent.due_date',
				fieldLabel: 'Due Date'
			},(_userCombo = new Ext.form.ComboBox({
            	xtype: 'combo',
            	hiddenName: 'RecallListItemEvent.created_by',
            	store: new Ext.data.JsonStore({
                	url: '/users/combo/A.json',
                	root: 'rows',
                	fields: [
                		'id', 'name'
                	],
                	autoLoad: true
            	}),
            	mode: 'remote',
            	forceSelection: true,
			    triggerAction: 'all',
	   	        loadingText: 'Loading ...',
                displayField: 'name',
                valueField: 'id',
                fieldLabel: 'Sent by',
            	cls: 'user icon',
            	value: IOH.USER.id
			})),{
				name: 'RecallListItemEvent.comments',
				fieldLabel: 'Comments',
				xtype: 'textarea'
			}],
			api: {
				submit: Server.RecallListItemEvents.direct_save
			},
			buttons: [{
				text: 'Save',
				handler: function () {
					this.getForm().submit({
						success: function (form, resp) {
							this.fireEvent('recall_scheduled', resp.result.id);
							IOH.APP.feedback('Success', 'Recall is scheduled.');
						},
						scope: this
					});
				},
				scope: this
			},{
				text: 'Reset',
				handler: this.reset,
				scope: this
			}],
			bodyStyle: {
				padding: '10px'
			}
		};
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.RecallInviteForm.superclass.initComponent.apply(this, arguments);
		
		this.on('reset', this._onReset, this);
		
		_userCombo.store.on('load', function () {
			var v = _userCombo.getValue();
			_userCombo.setValue(0);
			_userCombo.setValue(v);
		});
	},
	
	setDefaultRec: function (inviteRec) {
		this.defaultRec = inviteRec;
	},
	
	loadRec: function (inviteRec) {
		this.getForm().reset();
		this.getForm().loadRecord(inviteRec);
		
		if (this.getForm().findField('RecallListItemEvent.id').getValue()) {
			this.setTitle('Edit Invite');
		} else {
			this.setTitle('Add Invite');
		}
		
		// Keep recall_date and due date - as requested in http://projects.tripledub.net/projects/54/tickets/105
		// and in http://projects.tripledub.net/projects/54/comments/7975
		this.defaultRec.set('RecallListItemEvent.recall_date', inviteRec.get('RecallListItemEvent.recall_date'));
		this.defaultRec.set('RecallListItemEvent.due_date', inviteRec.get('RecallListItemEvent.due_date'));
	},
	
	reset: function () {
		// Notify everyone interested (including this form itself)
		this.fireEvent('reset');
	},
	
	_onReset: function () {
		// Keep the id of the recall list item, otherwise next form submit will post data nowhere.
		//var i = this.getForm().findField('RecallListItemEvent.recall_list_item_id').getValue();
		
		// Keep the due date as well - as requested in http://projects.tripledub.net/projects/54/tickets/105
		// Note that now the semantics of 'due_date' is transferred to 'recall_date' 
		// @link http://projects.tripledub.net/projects/54/tickets/138
		//var recallDate = this.getForm().findField('RecallListItemEvent.recall_date').getValue();
		
		this.loadRec(this.defaultRec);
		//this.getForm().findField('RecallListItemEvent.recall_list_item_id').setValue(i);
		//this.getForm().findField('RecallListItemEvent.due_date').setValue(dueDate);
	}
});
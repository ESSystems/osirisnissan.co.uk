IOH.RecallLists = Ext.extend(Ext.Container, 
{
	initComponent: function () {
		var config = {
			layout: 'border',
			border: false,
			hideMode: 'offsets',
			items: [(
				this._members = new IOH.RecallListMembers({
					region: 'center', 
					split: true
				})
			),{
				xtype: 'panel',
				border: false,
				region: 'east',
				width: 400,
				split: true,
				collapseMode: 'mini',
				collapsed: true,
				layout: 'border',
				ref: 'rightPanel',
				items: [(this._inviteForm = new IOH.RecallInviteForm({
					region: 'north',
					disabled1: true,
					autoHeight: true,
					split: true
				})), (this._history = new IOH.RecallHistory({
					region: 'center',
					disabled1: true
				}))]
			}]
		};
			
		Ext.apply(this, config);
			
		IOH.RecallLists.superclass.initComponent.apply(this, arguments);
		
		this._history.relayEvents(this._inviteForm, ['recall_scheduled', 'reset']);
		this._members.relayEvents(this._inviteForm, ['recall_scheduled']);
		
		this._members.getSelectionModel().on('selectionchange', this._onMemberSelectionChange, this);
		this._history.getSelectionModel().on('selectionchange', this._onHistorySelectionChange, this);
	},

	_onHistorySelectionChange: function (sm) {
		if (sm.getCount() == 1) {
			this._inviteForm.loadRec(sm.getSelected());
		} else {
			this._inviteForm.reset();
		}
		
	},
	
	_onMemberSelectionChange: function (sm) {
		var sel;
		
		if (sm.getCount() == 1) {
			sel = sm.getSelected();
		}
		
		if (sel) {
			this._history.loadRec(sel.id);
			
			var rec = this.createInviteRecord({
				'RecallListItemEvent.recall_list_item_id': sel.id,
				'RecallListItemEvent.due_date': sel.get('NextSchedule.due_date')
			});
			
			this._inviteForm.setDefaultRec(rec);
			this._inviteForm.reset();

			this.rightPanel.expand();			
		} else {
			this._history.store.removeAll();
			this._inviteForm.reset();
		}
		
		this.rightPanel.setDisabled(!sel);
	},
	
	createInviteRecord: function (data, id) {
		return new this._history.store.recordType(data, id);
	}
});

Ext.reg('IOH.RecallLists', IOH.RecallLists);
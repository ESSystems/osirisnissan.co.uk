IOH.RecallHistory = Ext.extend(Ext.grid.GridPanel,
{
	title: 'Recall History',
	initComponent: function () {
		var store = new Ext.data.DirectStore({
			xtype: 'directstore',
			directFn: Server.RecallListItems.get_history,
			fields: []
		});
		
		var cfg = {
			id: 'recall-history-grid',
			store: store,
			columns: [{
				header: 'Id',
				dataIndex: 'RecallListItemEvent.id',
				hidden: true
			},{
				header: '#',
				dataIndex: 'RecallListItemEvent.call_no',
				width: 10
			},{
				header: 'Contact',
				dataIndex: 'RecallListItemEvent.invite_date',
				renderer: Ext.util.Format.dateRenderer('d/m/y'),
				width1: 30
			},{
				id: 'recalllistitemevent-contact_type',
				header: 'Type',
				dataIndex: 'RecallListItemEvent.contact_type',
				width: 140
			},{
				header: 'Recall',
				dataIndex: 'RecallListItemEvent.recall_date',
				renderer: Ext.util.Format.dateRenderer('d/m/y'),
				width1: 30
			},{
				header: 'Due',
				dataIndex: 'RecallListItemEvent.due_date',
				renderer: Ext.util.Format.dateRenderer('d/m/y')
			},{
				header: 'Attended On',
				dataIndex: 'RecallListItemEvent.attended_date',
				renderer: function (v, meta, rec) {
					if (!v) {
						if (rec.get('RecallListItemEvent.due_date') && rec.get('RecallListItemEvent.due_date').format && rec.get('RecallListItemEvent.due_date').format('Y-m-d') < (new Date().format('Y-m-d'))) {
							return '<b>overdue</b>';
						}
						
						return 'pending';
					}
					
					return v.format('d/m/y');
				},
				width1: 30
			}],
			viewConfig: {
				forceFit: true,
				emptyText: 'No recalls found.',
				autoExpandColumn1: 'recalllistitemevent-contact_type'
			},
			loadMask: {
				store: store
			}
		};
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.RecallHistory.superclass.initComponent.apply(this, arguments);
		
		//this.on('selectionchange', this._onSelectionChange, this);
		this.on('recall_scheduled', function (id) {
			this.store.reload({
				callback: function () {
					var sel = this.store.getById(id);
					
					if (sel) {
						this.getSelectionModel().selectRecords([sel], false);
					}
				},
				scope: this
			});
		}, this);
		
		this.on('reset', function () { this.getSelectionModel().clearSelections(); }, this);
	},
	
	loadRec: function (recall_list_item_id) {
		this.store.load({
			params: {'0': recall_list_item_id}
		});
	},
	
	/*
	_onSelectionChange: function (sm) {
		if (sm.getCount() != 1) {
			this.setDisabled(true);
			this.store.removeAll();
		} else {
			var sel = sm.getSelected();
			this.loadRec(sel.id);
			this.setDisabled(false);
		}
	}
	*/
});
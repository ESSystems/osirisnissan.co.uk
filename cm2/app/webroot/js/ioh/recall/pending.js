IOH.PendingRecalls = Ext.extend(Ext.grid.EditorGridPanel,
{
	initComponent: function () {
		var store = new Ext.data.DirectStore({
			xtype: 'directstore',
			directFn: Server.persons.get_pending_recalls,
			fields: []
		});
		
		var sm = new Ext.grid.CheckboxSelectionModel({
			singleSelect: true,
			moveEditorOnEnter: false,
			checkOnly: true
		});
		
		this._listCombo = new Ext.form.ComboBox({
			mode: 'remote',
			triggerAction: 'all',
			store: {
				xtype: 'directstore',
				directFn: Server.RecallLists.lookup,
				fields: [],
				autoLoad: true
			},
			displayField: 'title',
			valueField: 'id',
			forceSelection: true,
			emptyText: 'Click to select list',
			listeners: {
				beforequery: function (e) {
					e.query = (new Date().format('U'));
				},
				// required for the first render event
			    afterrender: function(combo) {
			        combo.onTriggerClick();
			    },
			    // required after render during activating an other combo in the column
			    show: function(combo) {
			        combo.onTriggerClick();
			    }
			}
		});
		
		var _delButton;
		
		var cfg = {
			store: store,
			columns: [sm, {
				id: 'title',
				header: 'List',
				dataIndex: 'RecallList.id',
				editor: this._listCombo,
				renderer: function (v, meta, rec) {
					return rec.get('RecallList.title');
				}
			},{
				header: 'Recall',
				dataIndex: 'PendingEvent.recall_date',
				editor: {
					xtype: 'datefield',
					minValue: new Date().add(Date.DAY, 0)
				},
				renderer: function(value, metaData, record) { 
					if (value) {
						return value.format('d/m/y') + ' (' + record.get('PendingEvent.call_no') + ')';
					}
					return '';
				},
				width: 50,
				align: 'right'
			}],
			sm: sm,
			viewConfig: {
				forceFit: true,
				emptyText: 'Select Person ...',
				deferEmptyText: false,
				getRowClass: function (rec) {
					if (rec.get('PendingEvent.recall_date') < (new Date())) {
						return 'overdue';
					}
				}
			},
			hideHeaders: true,
			autoExpandColumn: 'title',
			clicksToEdit: 1,
			disabled: true,
			loadMask: {
				store: store
			},
			tbar: [{
				text: 'Add',
				handler: this._onAddRecall,
				scope: this,
				cls: 'x-btn-text-icon',
				iconCls: 'add'
			}/*,'->', (_delButton = new Ext.Button({
				text: 'Delete',
				handler: this._onDeleteRecall,
				scope: this,
				disabled: true,
				cls: 'x-btn-text-icon',
				iconCls: 'delete'
			}))*/]
		};
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.PendingRecalls.superclass.initComponent.apply(this, arguments);
		
		this.on('valuechange', this.loadRec, this);
		this.getSelectionModel().on('selectionchange', function (sm) {
			var id;
			if (sm.getCount() == 1 && sm.getSelected().get('PendingEvent.id')) {
				id = sm.getSelected().get('PendingEvent.id');
//				_delButton.setDisabled(false);
			} else {
//				_delButton.setDisabled(true);
			}
			this.fireEvent('pendingselchange', id);
		}, this);
		this.on('afteredit', this._onAfterEdit, this);
		this.on('beforeedit', this._onBeforeEdit, this);
	},
	
	loadRec: function (personId) {
		this.personId = personId;
		if (personId) {
			this.setDisabled(false);
			this.getView().emptyText = 'No pending recalls.';
			this.store.load({
				params: {'0': personId}
			});
		} else {
			this.getView().emptyText = 'Select Person ...';
			this.store.removeAll();
			this.setDisabled(true);
		}
	},
	
	_onAddRecall: function () {
		var rec = new this.store.recordType({
			'RecallList.title': '',
			'PendingEvent.recall_date': null,
			'PendingEvent.call_no': ''
		});
		this.store.add([rec]);
		var row = this.store.indexOf(rec);
		this.getView().getRow(row).scrollIntoView();
//		this.getSelectionModel().selectRow(row, false);
		this.startEditing(row, 1);
	},
	
	_onDeleteRecall: function () {
		Ext.MessageBox.confirm('Confirm', 'Are you sure you want to delete the selected pending recall record?', function (confirm) {
			if (confirm != 'yes') {
				return;
			}
			
			var sm = this.getSelectionModel();
			if (sm.getCount() == 1 && sm.getSelected().get('PendingEvent.id')) {
				Server.RecallListItems.del({
					data: {
						RecallListItemEvent: { id : sm.getSelected().get('PendingEvent.id') }
					}
				}, function (result) {
					if (result.success) {
						this.store.reload();
						IOH.APP.feedback('Success', 'Entry was deleted successfully.');
					}
				}, this);
			}
		}, this);
	},
	
	_onBeforeEdit: function (e) {
		if (e.record.get('PendingEvent.id') && e.field == 'RecallList.id') {
			e.cancel = true;
		}
	},
	
	_onAfterEdit: function (e) {
		if (e.field == 'RecallList.id') {
			var rec = this._listCombo.findRecord(this._listCombo.valueField, e.value);
			e.record.set('RecallList.title', rec.get(this._listCombo.displayField));
		};
		
		if (e.record.get('RecallList.id') && e.record.get('PendingEvent.recall_date') && this.personId) {
			Server.Nemployees.add_recall({
				data: {
					'Person':{'id': this.personId},
					'RecallList': {'id': e.record.get('RecallList.id')},
					'PendingEvent':{
						'id': e.record.get('PendingEvent.id'), 
						'recall_list_item_id': e.record.get('PendingEvent.recall_list_item_id'), 
						'recall_date': e.record.get('PendingEvent.recall_date').format('Y-m-d')
					}
				}
			}, function (result) {
				if (result.success) {
					IOH.APP.feedback('Success', 'Success');
					this.store.reload({
						callback: function () {
//							var row = e.grid.getView().getRow(e.row);
//							if (row) {
//								row.scrollIntoView();
//								e.grid.getSelectionModel().selectRow(e.row, false);
//							}
						}
					});
				} else {
					IOH.APP.feedback('Error', 'Error');
				}
			}, this);
		}
	}
});
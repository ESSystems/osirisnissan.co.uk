IOH.RecallListMembersStore = Ext.extend(Ext.data.JsonStore, 
{
	constructor: function (config) {
		IOH.RecallListMembersStore.superclass.constructor.call(this, Ext.apply({
			url: '/recallListItems/index.json',
			totalProperty: 'total',
			successProperty: 'success',
	        root: 'rows',
	        id: 'RecallListItem.id',
	        fields: [{
	        	name: 'Person.full_name'
	        },{
	        	name: 'Employee.Supervisor.full_name', 
	        	mapping: 'Employee',
	        	convert: function (v) {
	        		if (v.Supervisor && v.Supervisor.full_name) {
	        			return v.Supervisor.full_name;
	        		}
	        		return '-';
	        	}
	        },{
	        	name: 'Patient.Organisation.OrganisationName', 
	        	mapping: 'Patient',
	        	convert: function (v) {
	        		if (v.Organisation && v.Organisation.OrganisationName) {
	        			return v.Organisation.OrganisationName;
	        		}
	        		return '-';
	        	}
	        },{
	        	name: 'Employee.Department.DepartmentDescription', 
	        	mapping: 'Employee',
	        	convert: function (v) {
	        		if (v.Department && v.Department.DepartmentDescription) {
	        			return v.Department.DepartmentDescription;
	        		}
	        		return '-';
	        	}
	        },
	            {name: 'Employee.salary_number'},
	            {name: 'Employee.sap_number'},
	            {name: 'Employee.person_id'},
	            {name: 'ZNextSchedule.id', type: 'int'},
	            {name: 'ZNextSchedule.due_date', type: 'date', dateFormat: 'Y-m-d'},
	            {name: 'ZNextSchedule.recall_date', type: 'date', dateFormat: 'Y-m-d'},
	            {name: 'ZNextSchedule.call_no', type: 'int'},
	            {name: 'ZNextSchedule.contact_type'},
	            {name: 'ZNextSchedule.attended_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
	            {name: 'RecallListItem.last_attended_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
	            {name: 'RecallListItem.modified', type: 'date', dateFormat: 'Y-m-d H:i:s'}
	        ],
			remoteSort: true
		}, config));
	}
});

IOH.RecallListMembers = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var store =  new IOH.RecallListMembersStore({});
		
		var pagingToolbar = new Ext.PagingToolbar({
			pageSize: 50,
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying recall list people {0} - {1} of {2}',
			emptyMsg: "No recall list people found."
		});
		
		var config = {
			store: store,
			columns: [{
				id: 'person',
				header: 'Person',
				dataIndex: 'Person.full_name',
				renderer: function (v, meta, rec) {
					var cssClass = 'icon';
					if (rec.get('ZNextSchedule.contact_type') == 'Informed HandS') {
						cssClass += ' error';
					} else if (rec.get('ZNextSchedule.id')) {
						if (rec.get('ZNextSchedule.attended_date')) {
							
						} else if (rec.get('ZNextSchedule.recall_date') < new Date()) {
							cssClass += ' clock_red';
						} else {
							cssClass += ' clock';
						}
					}
					
					return '<div class="' + cssClass + '">' + v + '</div>';
				}
			},{
				header: 'Client',
				dataIndex: 'Patient.Organisation.OrganisationName',
				width: 50
			},{
				header: 'Salary No',
				dataIndex: 'Employee.salary_number',
				sortable: true,
				width: 20
			},{
				header: 'SAP No',
				dataIndex: 'Employee.sap_number',
				sortable: true,
				width: 20
			},{
				header: 'Department',
				dataIndex: 'Employee.Department.DepartmentDescription',
				sortable: true,
				width: 50
			},{
				header: 'Supervisor',
				dataIndex: 'Employee.Supervisor.full_name',
				sortable: true,
				width: 50
			},{
				header: 'Last Test',
				sortable: true,
				width: 20,
				dataIndex: 'RecallListItem.last_attended_date',
				renderer: function (v) {
					if (v) {
						return Ext.util.Format.date(v, 'd/m/y');
					}
					
					return '<i>never</i>';
				}
			},{
				header: 'Recall',
				dataIndex: 'ZNextSchedule.recall_date',
				id: 'recall_date',
				sortable: true,
				width: 50,
				renderer: Ext.util.Format.dateRenderer('d/m/y')
			},{
				header: 'Due',
				dataIndex: 'ZNextSchedule.due_date',
				id: 'due',
				sortable: true,
				width: 50,
				renderer: Ext.util.Format.dateRenderer('d/m/y')
			}],
			autoExpandColumn: 'person',
			viewConfig: {
				forceFit: true,
				getRowClass: function (rec) {
					var dueDate = rec.get('ZNextSchedule.due_date');
					if (!dueDate) {
						return rec.get('ZNextSchedule.recall_date') ? '': 'gray';
					}
					if (rec.get('ZNextSchedule.due_date') < (new Date())) {
						return 'overdue';
					}
				},
				emptyText: 'Please select a recall list',
				deferEmptyText: false
				
			},
			sm: new Ext.grid.RowSelectionModel({
				listeners: {
					selectionchange: this._onSelectionChange,
					scope: this
				}
			}),
			clicksToEdit: 1,
			loadMask: true,
			tbar: new Ext.Container({
				items: [{
					xtype: 'toolbar',
					items: [{
						id: 'recall-lists-combo',
						xtype: 'combo',
						triggerAction: 'all',
						mode: 'remote',
						store: {
							xtype: 'jsonstore',
							url: '/recallLists/index.json',
							totalProperty: 'total',
							successProperty: 'success',
					        root: 'rows',
					        id: 'RecallList.id',
					        fields: [
				 	            {name: 'id', mapping: 'RecallList.id'},
				 	            {name: 'title', mapping: 'RecallList.title'},
					            {name: 'RecallList.recall_list_item_count', type: 'int'},
					            {name: 'RecallList.created', type: 'date', dateFormat: 'Y-m-d H:i:s'},
					            {name: 'RecallList.modified', type: 'date', dateFormat: 'Y-m-d H:i:s'}
					        ],
							remoteSort: true
						},
						displayField: 'title',
						valueField: 'id',
						emptyText: 'Select List ...',
						forceSelection: true,
						listeners: {
							select: this._onListChange,
							beforequery: function(qe){
					            delete qe.combo.lastQuery;
					        },
							scope: this
						}
					},'-',{
						id: 'add-people-button',
						text: 'Add',
						disabled: true,
						handler: this._addItems,
						scope: this,
						cls: 'x-btn-text-icon',
						iconCls: 'group_add'
					}, {
						id: 'delete-people-button',
						text: 'Delete',
						disabled: true,
						handler: this._delItems,
						scope: this,
						cls: 'x-btn-text-icon',
						iconCls: 'group_delete'
					}, '->', {
						id: 'export-csv-button',
						text: 'Export CSV',
						disabled: true,
						handler: this.exportCSV,
						scope: this,
						cls: 'x-btn-text-icon',
						iconCls: 'page_excel'
					}]
				},{
					xtype: 'toolbar',
					items: ['Filter: ',
					    (this._personCombo = new IOH.PersonCombo({
					    	hideTrigger: true,
					    	width: 250,
					    	emptyText: 'Person',
					    	minChars: 1,
					    	listeners: {
					    		select: function () { this.load(); },
					    		scope: this
					    	}
					    })), ' ',
					    (this._dueFrom = new Ext.form.DateField({
						emptyText: 'Due From',
						listeners: {
							select: function () { this.load(); },
							scope: this
						}
					})), ' ', (this._dueTo = new Ext.form.DateField({
						emptyText: 'Due To',
						listeners: {
							select: function () { this.load(); },
							scope: this
						}
					})), {
						handler: function () { this.load(); },
						scope: this,
						cls: 'x-btn-icon',
						iconCls: 'control_play_blue',
						tooltip: 'Apply filter'
					},{
						handler: this._onResetDateFilter,
						scope: this,
						cls: 'x-btn-icon',
						iconCls: 'cross',
						tooltip: 'Reset filter'
					}]
				}]
			}),
			bbar: pagingToolbar
		};
		
		Ext.apply(this, config);
		
		IOH.RecallListMembers.superclass.initComponent.apply(this, arguments);
		
		this.on('rowdblclick', this._onRowDblclick, this);
		this.on('afteredit', this._onAfterEdit, this);
		this.store.on('load', function () {
			this.getView().emptyText = 'The recall list is empty';
		}, this);
		
		this.on('recall_scheduled', function () {
			this.store.reload();
		}, this);
	},
	
	load: function (recallListId) {
		this.getFilter(this.getStore().baseParams);
		
		if (recallListId) {
			this.getStore().baseParams.recall_list_id = recallListId;
		}
		if (this.getStore().baseParams.recall_list_id) {
			this.getStore().reload({
				params: {start: 0, limit: 50}
			});
		
			this.setDisabled(false);
		}
	},
	
	getFilter: function (filter) {
		if (typeof filter == 'undefined') {
			filter = {};
		}

		filter.due_from = null;
		filter.due_to = null;
		filter.person_id = null;

		if (this._getListCombo().getValue()) {
			filter.recall_list_id = this._getListCombo().getValue();
		}
		if (this._dueFrom.getValue()) {
			filter.due_from = this._dueFrom.getValue().format('Y-m-d');
		}
		if (this._dueTo.getValue()) {
			filter.due_to = this._dueTo.getValue().format('Y-m-d');
		}
		if (this._personCombo.getValue()) {
			filter.person_id = this._personCombo.getValue();
		}
	},
	
	exportCSV: function () {
		var filter = {}, query = '';
		
		this.getFilter(filter);
		
		if (filter.recall_list_id) {
			if (filter.person_id) {
				query += '/person_id:' + filter.person_id;
			}
			if (filter.due_from) {
				query += '/due_from:' + filter.due_from;
			}
			if (filter.due_to) {
				query += '/due_to:' + filter.due_to;
			}
			window.location = (String.format('/recallListItems/index/limit:0/start:0/recall_list_id:{0}{1}.csv', filter.recall_list_id, query));
		}
	},
	
	_addItems: function () {
		var self = this;
		
		IOH.APP.showPeopleWindow({
			skipLeavers: true,
			employeesOnly: true,
			onSelect: function (records) {
				var peopleIds = [];
				
				Ext.each(records, function (rec) {
					peopleIds.push(rec.id);
				});
				
				Ext.Ajax.request({
					url: '/recallLists/addItems.json',
					params: {
						id: self.getStore().baseParams.recall_list_id,
						'peopleIds[]': peopleIds
					},
					success: function (response) {
						var json = Ext.util.JSON.decode(response.responseText);
						
						if (json.peopleIds && json.peopleIds.length > 0) {
							IOH.APP.feedback('Success', 'Employees added');
							self.getStore().reload();
						}
					}
				});
				
			}
		});
	},
	
	/**
	 * Delete selected rows (if any)
	 */
	_delItems: function () {
		var sm   = this.getSelectionModel();
		
		if (sm.getCount() < 1) {
			return;
		}
		
		var selectedRecords = sm.getSelections(); // An array of records being selected
		var ids             = [];                 // Build array of people ids here
		var self            = this;
		
		Ext.each(selectedRecords, function (rec) {
			ids.push(rec.id);
		});

		//
		// Send delete request to the server
		//
		Ext.Ajax.request({
			url: '/recallLists/delItems.json',
			params: {
				id: self.getStore().baseParams.recall_list_id,
				'ids[]': ids
			},
			success: function (response) {
				var json = Ext.util.JSON.decode(response.responseText);
				
				if (json.ids.length > 0) {
					IOH.APP.feedback('Success', 'Employees deleted');
					self.getStore().reload();
				}
			}
		});
	},
	
	_onListChange: function (combo, rec, index) {
		if (rec) {
			this._getAddButton().setDisabled(false);
			this._getExportCSVButton().setDisabled(!IOH.USER.belongsToGroup(['Export']));
			this.load(rec.id);
		}
	},
	
	_onRowDblclick: function (grid, rowIndex, event) {
		var rec = this.store.getAt(rowIndex);
		if (rec) {
			IOH.APP.showPeopleWindow({
				personId: rec.get('Employee.person_id')
			});			
		}
	},
	
	_onAfterEdit: function (e) {
		if (e.value) {
			Server.RecallListItems.schedule([e.record.id, Ext.util.Format.date(e.value, 'Y-m-d')], function (result) {
				if (result.success) {
					e.record.commit();
					this.fireEvent('recall_scheduled', e.record.id);
					IOH.APP.feedback('Success', 'Recall is scheduled.');
				} else {
					IOH.APP.feedback('Error', 'Recall is NOT scheduled.');
				}
			}, this);
		}
	},
	
	_onSelectionChange: function (sm) {
		var selCount = sm.getCount(); 
	
		this._getDeleteButton().setDisabled(selCount < 1);
	},
	
	_onResetDateFilter: function () {
		this._dueFrom.reset();
		this._dueTo.reset();
		this._personCombo.reset();
		this.load();
	},
	
	_getButton: function (i) {
		return this.getTopToolbar().items.get(i);
	},
	
	_getListCombo: function () {
		return Ext.getCmp('recall-lists-combo');
		return this._getButton(0);
	},
	
	_getAddButton: function () {
		return Ext.getCmp('add-people-button');
		return this._getButton(2);
	},
	
	_getDeleteButton: function () {
		return Ext.getCmp('delete-people-button');
		return this._getButton(3);
	},
	
	_getExportCSVButton: function () {
		return Ext.getCmp('export-csv-button');
		return this._getButton(10);
	}
});
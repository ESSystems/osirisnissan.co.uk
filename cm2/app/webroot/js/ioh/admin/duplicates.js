Ext.ns('IOH.System');

IOH.System.DuplicatesGroups = Ext.extend(Ext.grid.GridPanel, {
	initComponent: function () {
		var allStore = new Ext.data.DirectStore({
			directFn: Server.persons.alldup,
			fields: []
		});
		
		var pagingToolbar = new Ext.PagingToolbar({
	        store: allStore,       // grid and PagingToolbar using same store
	        displayInfo: true,
	        pageSize: 40,
	        prependButtons: true
	    });
		
		var config = {
			store: allStore,
			bbar: pagingToolbar,
			columns: [{
				header: 'First name',
				dataIndex: 'Person.first_name',
				renderer: function (v) {
					if (!v) {
						return '<span style="color: #ccc; font-style: italic;">not set</span>';
					}
					return v;
				}
			},{
				header: 'Last name',
				dataIndex: 'Person.last_name',
				renderer: function (v) {
					if (!v) {
						return '<span style="color: #ccc; font-style: italic;">not set</span>';
					}
					return v;
				}
			},{
				
				header: 'Birth Date',
				dataIndex: 'Person.date_of_birth',
				renderer: function (v) {
					if (!v) {
						return '<span style="color: #ccc; font-style: italic;">not set</span>';
					}
					
					return v.format('d/m/Y');
				}
			},{
				
				header: 'Organisation',
				dataIndex: 'Patient.Organisation.OrganisationName',
				renderer: function (v) {
					if (!v) {
						return '<span style="color: #ccc; font-style: italic;">not set</span>';
					}
					return v;
				}
			},{
				
				header: 'Count',
				dataIndex: 'Count'
			}]
		};

		Ext.apply(this, config);
		Ext.apply(this.initialConfig, config);
		
		IOH.System.DuplicatesGroups.superclass.initComponent.apply(this, arguments);
		
		this.getSelectionModel().on('rowselect', this._onRowSelect, this);
		
		pagingToolbar.doRefresh();
	},
	
	_onRowSelect: function (sm, idx, rec) {
		this.fireEvent('duplicates.groupselect', rec);
	}
});

IOH.System.DuplicateEntries = Ext.extend(Ext.grid.GridPanel, {
	initComponent: function () {
		var store = new Ext.data.DirectStore({
			directFn: Server.persons.entriesdup,
			fields: []
		});
		
		var config = {
			store: store,
			columns: [{
				header: 'Id',
				dataIndex: 'Person.id'
			},{
				header: 'Gender',
				dataIndex: 'Person.gender'
			},{
				header: 'Title',
				dataIndex: 'Person.title'
			},{
				header: 'Address',
				dataIndex: 'Person.address1'
			},{
				header: 'Email',
				dataIndex: 'Person.email_address'
			},{
				header: 'Salary #',
				dataIndex: 'Employee.salary_number'
			},{
				header: 'SAP #',
				dataIndex: 'Employee.sap_number'
			},{
				header: 'Empl.Start',
				dataIndex: 'Employee.employment_start_date',
				renderer: function (v) {
					if (!v) {
						return '<span style="color: #ccc; font-style: italic;">not set</span>';
					}
					
					return v.format('d/m/Y');
				}
			},{
				header: 'Empl.End',
				dataIndex: 'Employee.employment_end_date',
				renderer: function (v) {
					if (!v) {
						return '<span style="color: #ccc; font-style: italic;">not set</span>';
					}
					
					return v.format('d/m/Y');
				}
			},{
				header: 'Supervisor',
				dataIndex: 'Employee.Supervisor.Person.full_name'
			},{
				header: 'Department',
				dataIndex: 'Employee.Department.DepartmentDescription'
			},{
				header: 'Attendances',
				dataIndex: 'AttendanceCount',
				renderer: function (v) {
					if (!v) {
						return '<span style="color: #ccc; font-style: italic;">0</span>';
					}
					
					return v;
				}
			},{
				header: 'Absences',
				dataIndex: 'AbsenceCount',
				renderer: function (v) {
					if (!v) {
						return '<span style="color: #ccc; font-style: italic;">0</span>';
					}
					
					return v;
				}
			}]
		};

		Ext.apply(this, config);
		Ext.apply(this.initialConfig, config);
		
		IOH.System.DuplicateEntries.superclass.initComponent.apply(this, arguments);
		
		this.on('duplicates.groupselect', this._onGroupSelect, this);
	},
	
	_onGroupSelect: function (rec) {
		this.store.load({
			params: {
				data: rec.data
			}
		});
	}
});


IOH.System.Duplicates = Ext.extend(Ext.Container, {
	initComponent: function () {
		var groupsGrid = new IOH.System.DuplicatesGroups({
			title: 'Possibly Duplicated Records',
			region: 'center'
		});
		var entriesGrid = new IOH.System.DuplicateEntries({
			title: 'Details',
			region: 'south',
			split: true,
			height: 300
		});
		
		var config = {
			layout: 'border',
			border: false,
			items: [groupsGrid, entriesGrid]
		};
	
		Ext.apply(this, config);
		Ext.apply(this.initialConfig, config);
		
		IOH.System.Duplicates.superclass.initComponent.apply(this, arguments);
		
		entriesGrid.relayEvents(groupsGrid, 'duplicates.groupselect');
	}
});

Ext.reg('IOH.System.Duplicates', IOH.System.Duplicates);
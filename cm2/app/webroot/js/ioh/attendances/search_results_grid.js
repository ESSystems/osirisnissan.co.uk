IOH.Attendances.SearchResultsGrid = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var attendancesStore = new Ext.data.JsonStore({
			url: '/attendances/page.json',
			totalProperty: 'totalRows',
			successProperty: 'success',
			root: 'rows',
			id: 'Attendance.id',
			fields: [
				{name: 'person', mapping: 'Person.full_name'},
				{name: 'clinic', mapping: 'Clinic.clinic_name'},
				{name: 'reason', mapping: 'AttendanceReason.description'},
				{name: 'result', mapping: 'AttendanceResult.description'},
				{name: 'attendance_date_time', mapping: 'Attendance.attendance_date_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
				{name: 'seen_at_time', mapping: 'Attendance.seen_at_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
				{name: 'organisation_name', type: 'string', convert: function (v, data) {try { return data.Person.Patient.Organisation.OrganisationName; } catch (e) {} return 'n/a';}},
				{name: 'salary_number', type: 'string', convert: function (v, data) {try { return data.Employee.salary_number; } catch (e) {} return 'n/a';}},
				{name: 'sap_number', type: 'string', convert: function (v, data) {try { return data.Employee.sap_number; } catch (e) {} return 'n/a';}},
				{name: 'department', type: 'string', convert: function (v, data) {try { return data.Employee.Department.DepartmentDescription; } catch (e) {} return 'n/a';}}
			],
			remoteSort: true,
			_dirty: true,
			
			setDirty: function () {
				this._dirty = true;
			},
			
			isDirty: function () {
				return this._dirty;
			}
		});
	
		attendancesStore.on('beforeload', function (s, options) {
			options.params = Ext.apply(options.params, attendancesStore.filterParams);
		});
		
		var _printBtn, _exportBtn, _pager;
	
		var config = {
			title: 'Search Results',
		    store: attendancesStore,
			columns:[
		    	{
		           header: "Full Name",
		           dataIndex: 'person',
		           sortable: true
		        },{
		           header: "Organisation",
		           dataIndex: 'organisation_name'
		        },{
		        	header: "Department",
		        	dataIndex: 'department'
		        },{
		           header: "Salary No",
		           dataIndex: 'salary_number',
		           align: 'right'
		        },{
		        	header: "SAP No",
		        	dataIndex: 'sap_number',
		           align: 'right'
		        },{
		           header: "Seen at Date",
		           dataIndex: 'seen_at_time',
		           renderer: Ext.util.Format.dateRenderer('d/m/y H:i'),
		           sortable: true
		        },{
		           header: "Attendance Date",
		           dataIndex: 'attendance_date_time',
		           renderer: Ext.util.Format.dateRenderer('d/m/y H:i'),
		           sortable: true
		        },{
		           header: "Clinic",
		           dataIndex: 'clinic',
		           sortable: true
		        },{
		           header: "Attendance Reason",
		           dataIndex: 'reason',
		           sortable: true
		        },{
		           header: 'Attendance Result',
		           dataIndex: 'result',
		           sortable: true
		        }
			],
		    loadMask: true,
			viewConfig: {
		        forceFit:true,
		        enableRowBody:true,
		        showPreview:true
		    },
		    tbar: [{
		    	text: 'Delete',
		    	handler: function () { this.fireEvent('delete', this); },
		    	scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'cross'
		    },(_printBtn = new Ext.Button({
				text: 'Print',
				handler: function () {
					window.open(String.format('/attendances/printPreview'));
				},
				cls: 'x-btn-text-icon',
				iconCls: 'printer',
				disabled: true
			})), (_exportBtn = new Ext.Button({
				text: 'Export CSV',
				handler: function () {
					window.location = (String.format('/attendances/export.csv'));
				},
				cls: 'x-btn-text-icon',
				iconCls: 'page_excel',
				disabled: true
			}))],
			bbar: (_pager = new Ext.PagingToolbar({
		        pageSize: 25,
		        store: attendancesStore,
		        displayInfo: true,
		        displayMsg: 'Displaying attendances {0} - {1} of {2}',
		        emptyMsg: "No attendances"
		    })),
		    iconCls: 'page_find'
		};
		
		Ext.apply(this, config);
		IOH.Attendances.SearchResultsGrid.superclass.initComponent.apply(this, arguments);
		
		this.on('reset', function () {
			this.store.removeAll();
		}, this);
		this.store.on('load', function (store, recs) {
			_printBtn.setDisabled(!IOH.USER.belongsToGroup(['Export']) || recs.length == 0);
			_exportBtn.setDisabled(!IOH.USER.belongsToGroup(['Export']) || recs.length == 0);
		});
		this.store.on('clear', function () {
			_printBtn.setDisabled(true);
			_exportBtn.setDisabled(true);
		});
	
		this.getSelectionModel().singleSelect = true;
	},
	
	reload: function (filter) {
		for (var i in filter) {
			if (!filter[i]) {
				filter[i] = undefined;
			}
		}
		this.getStore().filterParams = filter;
		this.getStore().load({params:{start:0, limit:25}});
	}
});

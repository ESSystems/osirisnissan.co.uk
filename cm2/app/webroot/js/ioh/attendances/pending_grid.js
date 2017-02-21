IOH.Attendances.PendingGrid = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var pendingAttendancesStore = new Ext.data.JsonStore({
			url: '/attendances/pending.json',
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
				{name: 'seen_at_time', mapping: 'Attendance.seen_at_time'},
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
			},
			
			listeners: {
				beforeload: function () {
					this._dirty = false;
					return true;
				}
			}
		});
		
		var config = {
			title: 'Pending Attendances',
		    store: pendingAttendancesStore,
			columns:[
		    	{
		           header: "Full Name",
		           dataIndex: 'person',
		           sortable: true,
		           width: 160
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
		           sortable: true,
		           width: 160
		        }
			],
		    loadMask: true,
			viewConfig: {
		        forceFit:true
		    },
		    tbar: [{
		    	text: 'Delete',
		    	handler: function () { this.fireEvent('delete', this); },
		    	scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'cross'
		    }],
			bbar: new Ext.PagingToolbar({
		        pageSize: 25,
		        store: pendingAttendancesStore,
		        displayInfo: true,
		        displayMsg: 'Displaying pending attendances {0} - {1} of {2}',
		        emptyMsg: "No attendances"
		    }),
		    iconCls: 'time'
		};
	
		Ext.apply(this, config);
		
		IOH.Attendances.PendingGrid.superclass.initComponent.apply(this, arguments);
		
		this.getSelectionModel().singleSelect = true;
		
		var reloadTask = {
			run: function () {
				this.getStore().setDirty();
				if (deepIsVisible(this)) {
					this.getSelectionModel().suspendEvents();
					this.getStore().reload({
						callback: function () {
							this.getSelectionModel().resumeEvents();
						},
						scope: this
					});
				}
			},
		    interval: 1000*60, //1 minute
		    scope: this
		}
		
		this.reload = function (bDontStop) {
			if (!bDontStop) {
				Ext.TaskMgr.stop(reloadTask);
			}
			Ext.TaskMgr.start(reloadTask);
		}

/*		
		this.reload(true);
		
		this.on('show', function () {
			if (this.getStore().isDirty()) {
				this.reload()
			}
		}, this);
*/
	}
});
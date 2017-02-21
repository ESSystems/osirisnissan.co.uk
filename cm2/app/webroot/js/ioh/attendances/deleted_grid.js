IOH.Attendances.DeletedGrid = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var store = new Ext.data.JsonStore({
			url: '/attendances/deleted.json',
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
			title: 'Deleted Attendances',
		    store: store,
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
		        }
			],
		    loadMask: true,
			viewConfig: {
		        forceFit:true
		    },
		    tbar: [{
		    	text: 'Restore',
		    	handler: this._undeleteAttendance,
		    	scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'arrow_rotate_clockwise'
		    },'->', {
		    	text: 'Erase',
		    	handler: this._eraseAttendance,
		    	scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'delete'
		    }],
			bbar: new Ext.PagingToolbar({
		        pageSize: 25,
		        store: store,
		        displayInfo: true,
		        displayMsg: 'Displaying deleted attendances {0} - {1} of {2}',
		        emptyMsg: "No attendances"
		    }),
		    iconCls: 'bin_closed'
		};
	
		Ext.apply(this, config);
		
		IOH.Attendances.DeletedGrid.superclass.initComponent.apply(this, arguments);
		
		this.getSelectionModel().singleSelect = true;
		
//		this.on('render', this.store.reload, this.store);
	},
	
	_undeleteAttendance: function () {
		var sel = this.getSelectionModel().getSelections();
		if (sel.length == 0) {
			return;
		}
		
		var ids = [];
		
		Ext.each(sel, function (rec) {
			ids.push(rec.id);
		});
		
		Server.Attendances.direct_unhide({
			data: {
				Attendance: { ids : ids }
			}
		}, function () {
			this.store.reload();
		}, this);
	},
	
	_eraseAttendance: function () {
		var sel = this.getSelectionModel().getSelections();
		if (sel.length == 0) {
			return;
		}
		
		if (!confirm('This action is not recoverable!!! Are you sure you want do completely erase selected attendance record(s)?')) {
			return false;
		}
		
		var ids = [];
		
		Ext.each(sel, function (rec) {
			ids.push(rec.id);
		});
		
		Server.Attendances.direct_erase({
			data: {
				Attendance: { ids : ids }
			}
		}, function () {
			this.store.reload();
		}, this);
	}

});
IOH.Attendances.DidNotAttendReport = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var fromDate = new Ext.ux.form.XDateField({
			id: 'created-from',
			xtype: 'datefield',
			name: 'cf',
			value: weekStart()
		});
		var toDate = new Ext.ux.form.XDateField({
			id: 'created-to',
			xtype: 'datefield',
			name: 'ct',
			value: weekEnd()
		});
		
		var store = new Ext.data.GroupingStore({
			proxy: new Ext.data.DirectProxy({
				directFn: Server.Attendances.didnotattend
			}),
			reader: new Ext.data.JsonReader({
				successProperty: 'success',
				root:'rows',
				idProperty:'Attendance.id',
				totalProperty: 'totalRows',
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
					{name: 'department', type: 'string', convert: function (v, data) {try { return data.Employee.Department.DepartmentDescription; } catch (e) {} return 'n/a';}},
					{name: 'supervisor', type: 'string', convert: function (v, data) {try { return data.Employee.Supervisor.full_name; } catch (e) { } return 'n/a';}}
				]
			}),
			sortInfo:{field: 'person', direction: "ASC"},
			groupField:'department'
		});
		
		var config = {
			title: 'Did Not Attend Report',
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
		           dataIndex: 'department',
		           groupRenderer: function (v, u, rec) {
		        	   v = 'Department: ' + v +
		        	   		', Supervisor: ' + rec.get('supervisor');
		        	   
		        	   return v;
		           }
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
		    view: new Ext.grid.GroupingView({
	            forceFit:true,
	            groupTextTpl: '{group}'
	        }),
		    tbar: [{
				xtype: 'tbtext',
				text: 'From Date'
			},
			fromDate,
			{
				xtype: 'tbtext',
				text: 'To Date'
			},
			toDate,
			{
				text: 'Show',
				handler: function () {
					this.store.load({
						params: {
							data: {
								Attendance: {
									'created_from': fromDate.getValue().format("Y-m-d"),
									'created_to': toDate.getValue().format("Y-m-d")
								}
							}
						}
					});
				},
				scope: this,
				cls: 'x-btn-text-icon',
				iconCls: 'page_find'
			},'-', {
				text: 'Print',
				handler: function () {
					var from = fromDate.getValue().format("Y-m-d");
					var to   = toDate.getValue().format("Y-m-d");
					window.open(String.format('/attendances/didnotattend/{0}/{1}.html', from, to));
				},
				cls: 'x-btn-text-icon',
				iconCls: 'printer',
				disabled: !IOH.USER.belongsToGroup(['Export'])
			}, {
				text: 'Export CSV',
				handler: function () {
					var from = fromDate.getValue().format("Y-m-d");
					var to   = toDate.getValue().format("Y-m-d");
					window.location = (String.format('/attendances/didnotattend/{0}/{1}.csv', from, to));
				},
				cls: 'x-btn-text-icon',
				iconCls: 'page_excel',
				disabled: !IOH.USER.belongsToGroup(['Export'])
			}],
			bbar: new Ext.PagingToolbar({
		        pageSize: 25,
		        store: store,
		        displayInfo: true,
		        displayMsg: 'Displaying attendances {0} - {1} of {2}',
		        emptyMsg: "No attendances"
		    })
		};
	
		Ext.apply(this, config);
		
		IOH.Attendances.DidNotAttendReport.superclass.initComponent.apply(this, arguments);
	}
});

Ext.reg('IOH.Attendances.DidNotAttendReport', IOH.Attendances.DidNotAttendReport);
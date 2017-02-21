IOH.AllAbsences = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var fromDate = new Ext.ux.form.XDateField({
			xtype: 'datefield',
			name: 'cf',
			value: weekStart()
		});
		var toDate = new Ext.ux.form.XDateField({
			xtype: 'datefield',
			name: 'ct',
			value: weekEnd()
		});
		
		var store = new Ext.data.DirectStore({
			fields: [],
			directFn: Server.Absences.direct_all,
//			sortInfo:{field: 'Employee.salary_number', direction: "ASC"},
			listeners: {
				beforeload: function (s, options) {
					options.params = Ext.apply(options.params, this.filterParams);
				}
			}
		});
		
		var pagingToolbar = new Ext.PagingToolbar({
			pageSize: 25,
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying absences {0} - {1} of {2}',
			emptyMsg: "No absences found."
		});

		var config = {
	    	title: 'All Absences',
	        store: store,
	    	columns:[{
	    		header: 'Id',
	    		dataIndex: 'Absence.id',
	    		width: 70,
	    		hidden: true
	    	},{
	    		header: "Employee",
	    		dataIndex: 'Person.full_name',
	    		width: 140
	        },{
	    		header: "Pers.Id",
	    		dataIndex: 'Person.id',
	    		hidden: true
	        },{
	           header: "SN",
	           dataIndex: 'Employee.salary_number',
	           width: 50
	        },{
	           header: "SAP No.",
	           dataIndex: 'Employee.sap_number',
	           width: 60
	        },{
	           header: "Supervisor",
	           dataIndex: 'Employee.Supervisor.full_name',
	        	renderer: function (v, m, rec) {
	        		if (rec.json.Employee.Supervisor) {
	        			v = rec.json.Employee.Supervisor.full_name;
	        		}
	        		
	        		return v;
	        	},
	           width: 130
	        },{
	           header: "Period",
	           dataIndex: 'Absence.start_date',
	           renderer: function (v, m, rec) {
	        	   return Ext.util.Format.date(v, Ext.form.DateField.prototype.format) +
	        	   		' - ' + 
	        	   		Ext.util.Format.date(rec.get('Absence.end_date'), Ext.form.DateField.prototype.format)
	           },
	           width: 100
	        },{
	           header: "Start",
	           dataIndex: 'Absence.start_date',
	           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
	           width: 60,
	           hidden: true
	        },{
	           header: "End",
	           dataIndex: 'Absence.end_date',
	           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
	           width: 60,
	           hidden: true
	        },{
	           header: "Ret.",
	           dataIndex: 'Absence.returned_to_work_date',
	           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
	           width: 60
	        },{
	        	header: 'Department',
	        	dataIndex: 'Employee.Department.DepartmentDescription',
	        	renderer: function (v, m, rec) {
	        		if (rec.json.Employee.Department) {
	        			v = rec.json.Employee.Department.DepartmentDescription;
	        		}
	        		
	        		return v;
	        	},
	        	width: 110
	        },{
	           header: 'Cur.Dept',
	           dataIndex: 'Person.Employee.Department.DepartmentDescription',
	           renderer: function (v, m, rec) {
	        		if (rec.json.Person.Employee && rec.json.Person.Employee.Department) {
	        			v = rec.json.Person.Employee.Department.DepartmentDescription;
	        		}
	        		
	        		return v;
	        	},
        		width: 110
	        },{
	        	header: 'Job Class',
	        	dataIndex: 'Employee.JobClass.JobClassDescription',
	        	renderer: function (v, m, rec) {
	        		if (rec.json.Employee.JobClass) {
	        			v = rec.json.Employee.JobClass.JobClassDescription;
	        		}
	        		
	        		return v;
	        	},
	        	width: 90
	        },{
	           header: 'Sick Days',
	           dataIndex: 'Absence.sick_days',
	           width: 35
	        },{
	           header: 'Calc. Sick Days',
	           dataIndex: 'Absence.calc_sick_days',
	           width: 35
	        },{
	           header: "Work Related",
	           dataIndex: 'Absence.work_related_absence',
	           renderer: booleanRenderer,
	           width: 25
	        },{
	           header: "Diagnosis",
	           dataIndex: 'MainDiagnosis.description',
	           width: 150
	        }],
			
			loadMask: true,
	        autoScroll: true,
			viewConfig: {
	            emptyText: 'No absences found.',
	            forceFit: true
	        },
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
					this.load({
						data: {
							Absence: {
								'created_from': fromDate.getValue().format("Y-m-d"),
								'created_to': toDate.getValue().format("Y-m-d")
							}
						}
					});
				},
				scope: this,
				cls: 'x-btn-text-icon',
				iconCls: 'page_find'
			},'-', {
				text: 'Export CSV',
				handler: function () {
					var from = fromDate.getValue().format("Y-m-d");
					var to   = toDate.getValue().format("Y-m-d");
					window.location = (String.format('/absences/export/{0}/{1}.csv', from, to));
				},
				cls: 'x-btn-text-icon',
				iconCls: 'page_excel',
				disabled: !IOH.USER.belongsToGroup(['Export'])
			}],
			bbar: pagingToolbar,
			
			load: function (params) {
				this.store.filterParams = params;
				this.store.load({
					params: {start: 0, limit: 25}
				});
			}
		};
		
	
		Ext.apply(this, config);
		IOH.AllAbsences.superclass.initComponent.apply(this, arguments);
	}
});

Ext.reg('IOH.AllAbsences', IOH.AllAbsences);
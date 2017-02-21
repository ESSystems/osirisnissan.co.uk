IOH.WorkRelatedAbsences = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var absencesReader = new Ext.data.JsonReader({
			url: '/sicknotes/workRelatedPage.json',
			totalProperty: 'totalRows',
			successProperty: 'success',
			root: 'rows'
		}, [
			{name: 'Employee.salary_number', type: 'string'},
			'Person.first_name', 'Person.last_name',
			'Supervisor.first_name', 'Supervisor.last_name', 'Supervisor.extension',
			'Sicknote.type_code',
			{name: 'Sicknote.start_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			{name: 'Sicknote.end_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			'Sicknote.symptoms_description', 
			'Sicknote.comments',
			{name: 'Absence.returned_to_work_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
			'Absence.sick_days', 
			{name: 'Absence.work_related_absence', type: 'boolean'},
			{name: 'Absence.accident_report_completed', type: 'boolean'}, 
			{name: 'Absence.discomfort_report_completed', type: 'boolean'}, 
			'Absence.department_code',
			{name: 'Department.DepartmentDescription', convert: function (v) { if (!v) { v = 'N/A'; } return v; } }
		]);
		
		var absencesStore = new Ext.data.GroupingStore({
			reader: absencesReader,
			url: '/sicknotes/workRelatedPage.json',
			groupField: 'Department.DepartmentDescription',
			sortInfo:{field: 'Employee.salary_number', direction: "ASC"},
			listeners: {
				beforeload: function (s, options) {
					options.params = Ext.apply(options.params, this.filterParams);
				}
			}
		});
		
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
		
		var config = {
	    	title: 'Work Related Absences',
	        store: absencesStore,
	    	columns:[
		    	{
		           header: "Employee",
		           dataIndex: 'Person.first_name',
		           renderer: function (v, meta, r) {
		           		return r.json.Person.first_name + ' ' + r.json.Person.last_name;
		           },
		           width: 100
		        },{
		           header: "SN",
		           dataIndex: 'Employee.salary_number',
		           width: 70
		        },{
		           header: "Supervisor",
		           dataIndex: 'Supervisor.first_name',
		           renderer: function (v, meta, r) {
		        	   if (r.json.Supervisor.first_name && r.json.Supervisor.last_name) {
		        		   v = r.json.Supervisor.first_name + ' ' + r.json.Supervisor.last_name;
		        	   }
		        	   
		        	   return v;
		           },
		           width: 100
		        },{
		           header: "Start",
		           dataIndex: 'Sicknote.start_date',
		           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
		           width: 60
		        },{
		           header: "End",
		           dataIndex: 'Sicknote.end_date',
		           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
		           width: 60
		        },{
		           header: "RTW",
		           dataIndex: 'Absence.returned_to_work_date',
		           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
		           width: 60
		        },{
		           header: "Symptoms",
		           dataIndex: 'Sicknote.symptoms_description',
		           width: 60
		        },{
		           header: "AR",
		           dataIndex: 'Absence.accident_report_completed',
		           renderer: booleanRenderer,
		           width: 25
		        },{
		           header: "WD",
		           dataIndex: 'Absence.discomfort_report_completed',
		           renderer: booleanRenderer,
		           width: 25
		        },{
		           header: 'Department',
		           dataIndex: 'Department.DepartmentDescription',
		           width: 35
		        }
			],
			
	        view: new Ext.grid.GroupingView({
	        	hideGroupedColumn: true,
	            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})',
	            forceFit: true,
	            emptyText: 'No absences found.'
	        }),
	
			loadMask: true,
	        autoScroll: true,
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
						'Sicknote.created_from': fromDate.getValue().format("Y-m-d"),
						'Sicknote.created_to': toDate.getValue().format("Y-m-d")
					});
				},
				scope: this,
				cls: 'x-btn-text-icon',
				iconCls: 'page_find'
			},'-',{
				text: 'Print',
				handler: function () {
					var from = fromDate.getValue().format("Y-m-d");
					var to   = toDate.getValue().format("Y-m-d");
					window.open(String.format('/sicknotes/workRelatedPage/{0}/{1}', from, to));
				},
				cls: 'x-btn-text-icon',
				iconCls: 'printer',
				disabled: !IOH.USER.belongsToGroup(['Export'])
			}],
			
			load: function (params) {
				this.store.filterParams = params;
				this.store.load({
					params: {start: 0, limit: 25}
				});
			}
		};
		
	
		Ext.apply(this, config);
		IOH.WorkRelatedAbsences.superclass.initComponent.apply(this, arguments);
		
		this.load({
			'Sicknote.created_from': fromDate.getValue().format("Y-m-d"),
			'Sicknote.created_to': toDate.getValue().format("Y-m-d")
		});
	}
});

Ext.reg('IOH.WorkRelatedAbsences', IOH.WorkRelatedAbsences);
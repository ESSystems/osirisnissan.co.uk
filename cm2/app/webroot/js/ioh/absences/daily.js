IOH.DailyAbsences = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var absencesStore = new Ext.data.JsonStore({
			url: '/sicknotes/dailyPage.json',
			totalProperty: 'totalRows',
			successProperty: 'success',
			root: 'rows',
			fields: [
				{name: 'Employee.salary_number', type: 'string'},
				{name: 'Employee.sap_number', type: 'string'},
				'Person.first_name', 'Person.last_name',
				{name: 'Supervisor.id', type: 'int'},
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
				'Department.DepartmentDescription'
			],
			remoteSort: true,
			listeners: {
				beforeload: function (s, options) {
					options.params = Ext.apply(options.params, this.filterParams);
				}
			}
		});
		
		var dateField = new Ext.form.DateField({
			id: 'daily-date',
			xtype: 'datefield',
			name: 'date', 
			value: new Date()
		});
		
		var config = {
	    	title: 'Daily Sicknotes',
	    	region: 'center',
	        store: absencesStore,
	    	columns:[{
		           header: "Employee",
		           dataIndex: 'Person.first_name',
		           renderer: function (v, meta, r) {
		           		return r.json.Person.first_name + ' ' + r.json.Person.last_name;
		           },
		           width: 100
		        },{
		           header: "Salary #",
		           dataIndex: 'Employee.salary_number',
		           width: 65
		        },{
		           header: "SAP #",
		           dataIndex: 'Employee.sap_number',
		           width: 65
		        },{
		           header: "Dept",
		           dataIndex: 'Department.DepartmentDescription',
		           width: 40
		        },{
		           header: "Sicknote",
		           dataIndex: 'Sicknote.type_code',
		           width: 50
		        },{
		           header: "Start",
		           dataIndex: 'Sicknote.start_date',
		           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
		           width: 50
		        },{
		           header: "End",
		           dataIndex: 'Sicknote.end_date',
		           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
		           width: 50
		        },{
		           header: "RTW",
		           dataIndex: 'Absence.returned_to_work_date',
		           renderer: Ext.util.Format.dateRenderer(Ext.form.DateField.prototype.format),
		           width: 50
		        },{
		           header: "Days",
		           dataIndex: 'Absence.sick_days',
		           width: 35
		        },{
		           header: "Symptoms",
		           dataIndex: 'Sicknote.symptoms_description',
		           width: 60
		        },{
		           header: "Supervisor",
		           dataIndex: 'Supervisor.first_name',
		           renderer: function (v, meta, r) {
		        	   if (r.get('Supervisor.id')) {
		        		   v =  r.json.Supervisor.first_name + ' ' + r.json.Supervisor.last_name;
		        	   }
		        	   
		        	   return v;
		           },
		           width: 100
		        },{
		           header: "Ext",
		           dataIndex: 'Supervisor.extension',
		           width: 30
		        },{
		           header: "WR",
		           dataIndex: 'Absence.work_related_absence',
		           renderer: booleanRenderer,
		           width: 25
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
		           header: "Comments",
		           dataIndex: 'Sicknote.comments',
		           width: 140
		        }
			],
	        loadMask: true,
	        autoScroll: true,
			viewConfig: {
	            emptyText: 'No absences found.',
	            forceFit: true
	        },
			tbar: [{
				xtype: 'tbtext',
				text: 'Date'
			},
			dateField,
			{
				id: 'show-button',
				text: 'Show',
				handler: function () {
					this.load({
						'Sicknote.created': dateField.getValue().format("Y-m-d")
					});
				},
				scope: this,
				cls: 'x-btn-text-icon',
				iconCls: 'page_find'
			},'-', {
				text: 'Print',
				handler: function () {
					var d = dateField.getValue().format("Y-m-d");
					window.open(String.format('/sicknotes/dailyPage/{0}', d));
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
		IOH.DailyAbsences.superclass.initComponent.apply(this, arguments);
		
		this.load({
			'Sicknote.created': dateField.getValue().format("Y-m-d")
		});
	}
});

Ext.reg('IOH.DailyAbsences', IOH.DailyAbsences);
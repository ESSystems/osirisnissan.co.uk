IOH.AllSicknotes = Ext.extend(Ext.grid.GridPanel, 
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
			directFn: Server.Sicknotes.direct_all,
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
			displayMsg: 'Displaying sicknotes {0} - {1} of {2}',
			emptyMsg: "No sicknotes found."
		});

		var config = {
	    	title: 'All Sicknotes',
	        store: store,
	    	columns:[{
	    		header: 'Id',
	    		dataIndex: 'Sicknote.id',
	    		width: 70,
	    		hidden: true
	    	},{
	    		header: "Employee",
	    		dataIndex: 'Absence.Person.full_name',
	    		width: 150
	        },{
	           header: "SN",
	           dataIndex: 'Absence.Employee.salary_number',
	           renderer: function (v, m, rec) {
	        		if (rec.json.Absence.Employee) {
	        			v = rec.json.Absence.Employee.salary_number;
	        		}
	        		
	        		return v;
	           },
	           width: 70
	        },{
	           header: "SAP No.",
	           dataIndex: 'Absence.Employee.sap_number',
	           renderer: function (v, m, rec) {
	        		if (rec.json.Absence.Employee) {
	        			v = rec.json.Absence.Employee.sap_number;
	        		}
	        		
	        		return v;
	           },
	           width: 70
	        },{
	    		header: "Pers.Id",
	    		dataIndex: 'Absence.Person.id',
	    		hidden: true
	        },{
	           header: "Type",
	           dataIndex: 'SicknoteType.description',
	           width: 150
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
	           header: "Sick days",
	           dataIndex: 'Sicknote.sick_days',
	           width: 60
	        },{
	           header: 'Symptoms',
	           dataIndex: 'Sicknote.symptoms_description',
	           width: 200
	        },{
	           header: 'Department',
	           dataIndex: 'Absence.Employee.Department.DepartmentDescription',
	           renderer: function (v, m, rec) {
	        		if (rec.json.Absence.Employee && rec.json.Absence.Employee.Department) {
	        			v = rec.json.Absence.Employee.Department.DepartmentDescription;
	        		}
	        		
	        		return v;
	           },
	           width: 120
	        },{
	           header: 'Cur. Dept',
	           dataIndex: 'Absence.Person.Employee.Department.DepartmentDescription',
	           renderer: function (v, m, rec) {
	        		if (rec.json.Absence.Person.Employee && rec.json.Absence.Person.Employee.Department) {
	        			v = rec.json.Absence.Person.Employee.Department.DepartmentDescription;
	        		}
	        		
	        		return v;
	           },
	           width: 120
	        },{
				header: 'Job Class',
				dataIndex: 'Absence.Employee.JobClass.JobClassDescription',
				renderer: function (v, m, rec) {
					if (rec.json.Absence.Employee && rec.json.Absence.Employee.JobClass) {
						v = rec.json.Absence.Employee.JobClass.JobClassDescription;
					}
					
					return v;
				},
				width: 120
	        }],
			
//			loadMask: true,
	        autoScroll: true,
			viewConfig: {
	            emptyText: 'No sicknotes found.',
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
							Sicknote: {
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
					window.location = (String.format('/sicknotes/export/{0}/{1}.csv', from, to));
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
		IOH.AllSicknotes.superclass.initComponent.apply(this, arguments);
	}
});

Ext.reg('IOH.AllSicknotes', IOH.AllSicknotes);
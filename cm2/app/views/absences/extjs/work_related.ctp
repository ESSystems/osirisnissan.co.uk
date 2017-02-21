<script type="text/javascript">
function onReady() {
	var absencesReader = new Ext.data.JsonReader({
		url: '<?=$html->url('/sicknotes/workRelatedPage.json')?>',
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
		'Department.DepartmentDescription'
	]);
	var absencesStore = new Ext.data.GroupingStore({
		reader: absencesReader,
		url: '<?=$html->url('/sicknotes/workRelatedPage.json')?>',
		groupField: 'Department.DepartmentDescription',
		sortInfo:{field: 'Employee.salary_number', direction: "ASC"},
		listeners: {
			beforeload: function (s, options) {
				options.params = Ext.apply(options.params, this.filterParams);
			}
		}
	});
	
	var absencesGrid = {
		id: 'absences-grid',
		xtype: 'grid',
    	title: 'Work Related Absences',
    	region: 'center',
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
	           		return r.json.Supervisor.first_name + ' ' + r.json.Supervisor.last_name;
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
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'
        }),

		loadMask: true,
        autoExpandColumn: 6,
        autoScroll: true,
		viewConfig: {
            forceFit1:true,
            autoFill1: true,
            emptyText: 'No absences found.'
        },
		bbar1: new Ext.PagingToolbar({
            pageSize: 25,
            store: absencesStore,
            displayInfo: true,
            displayMsg: 'Displaying absences {0} - {1} of {2}',
            emptyMsg: "No absences found."
        }),
		tbar: [{
			xtype: 'tbtext',
			text: 'From Date'
		},{
			id: 'created-from',
			xtype: 'datefield',
			name: 'cf',
			value: weekStart()
		},{
			xtype: 'tbtext',
			text: 'To Date'
		},{
			id: 'created-to',
			xtype: 'datefield',
			name: 'ct',
			value: weekEnd()
		},{
			text: 'Show',
			handler: function () {
				var from = Ext.getCmp('created-from').getRawValue();
				var to   = Ext.getCmp('created-to').getRawValue();
				Ext.getCmp('absences-grid').load({
					'Sicknote.created_from': from,
					'Sicknote.created_to': to
				});
			}
		},'-',{
			text: 'Print',
			disabled1: true,
			handler: function () {
				var from = Ext.getCmp('created-from').getRawValue();
				var to   = Ext.getCmp('created-to').getRawValue();
				window.open('/sicknotes/workRelatedPage/' + from.replace(/\//g, '@') + '/' + to.replace(/\//g, '@'));
			}
		}],
		
		tools1: [{
			id: 'left',
			tooltip: 'Previous Week',
			tooltipType: 'title',
			handler: function () {
				alert('prev');
			}
		},{
			id: 'right',
			tooltip: 'Next Week'
		}
		],
		
		load: function (params) {
			this.store.filterParams = params;
			this.store.load({
				params: {start: 0, limit: 25}
			});
		}
	};
	
    IOH.contentPanel.replace({
    	layout: 'border',
    	hideBorders: true,
    	items: absencesGrid
    });

	Ext.getCmp('absences-grid').load({
		'Sicknote.created_from': Ext.getCmp('created-from').getRawValue(),
		'Sicknote.created_to': Ext.getCmp('created-to').getRawValue()
	});
    
}

Ext.onReady(function () {
	try {
		onReady();
	} catch (e) {
		alert(e.message);
	}
});
</script>
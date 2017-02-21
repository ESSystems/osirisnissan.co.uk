<?php $readOnly = ($disableAttendances !== false?'true':'false') ?>

<script type="text/javascript">

function onReady() {
	
	// Create data store
	var attendancesStore = new Ext.data.JsonStore({
		url: '<?=$html->url('/attendances/page.json')?>',
		totalProperty: 'totalRows',
		successProperty: 'success',
		root: 'rows',
		id: 'id',
		fields: [
			'person',
			'clinic',
			'reason',
			'result',
			'attendance_date_time',
			'seen_at_time',
			{name: 'salary_number', type: 'string'}
		],
		remoteSort: true
	});

	var pendingAttendancesStore = new Ext.data.JsonStore({
		url: '<?=$html->url('/attendances/pending.json')?>',
		totalProperty: 'totalRows',
		successProperty: 'success',
		root: 'rows',
		id: 'id',
		fields: [
			'person',
			'clinic',
			'reason',
			'result',
			'attendance_date_time',
			'seen_at_time',
			{name: 'salary_number', type: 'string'}
		],
		remoteSort: true
	});

	attendancesStore.on('beforeload', function (s, options) {
		options.params = Ext.apply(options.params, attendancesStore.filterParams);
	});
//	pendingAttendancesStore.on('beforeload', function (s, options) {
//		options.params = Ext.apply(options.params, pendingAttendancesStore.filterParams);
//	});

	// Helper handlers

	var reloadAttendancesGrid = function (filter) {
		for (var i in filter) {
			if (!filter[i]) {
				filter[i] = undefined;
			}
		}
		attendancesStore.filterParams = filter;
	    attendancesStore.load({params:{start:0, limit:25}});
	}

	var reloadPendingGrid = function (filter) {
		pendingGrid.getSelectionModel().suspendEvents();
	    pendingAttendancesStore.load({
	    	params:{start:0, limit:25},
	    	callback: function () {
				pendingGrid.getSelectionModel().resumeEvents();
	    	}
	    });
	}

    // create the grid
    var grid = new Ext.grid.GridPanel({
    	title: 'Search Results',
        store: attendancesStore,
    	columns:[
	    	{
	           header: "Full Name",
	           dataIndex: 'person',
	           sortable: true
	        },{
	           header: "Salary No",
	           dataIndex: 'salary_number'
	        },{
	           header: "Seen at Date",
	           dataIndex: 'seen_at_time',
	           sortable: true
	        },{
	           header: "Attendance Date",
	           dataIndex: 'attendance_date_time',
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
		bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: attendancesStore,
            displayInfo: true,
            displayMsg: 'Displaying attendances {0} - {1} of {2}',
            emptyMsg: "No attendances"
        })
    });

    grid.getSelectionModel().singleSelect = true;    
    
    var pendingGrid = new Ext.grid.GridPanel({
    	title: 'Pending Attendances',
        store: pendingAttendancesStore,
    	columns:[
	    	{
	           header: "Full Name",
	           dataIndex: 'person',
	           sortable: true
	        },{
	           header: "Salary No",
	           dataIndex: 'salary_number'
	        },{
	           header: "Attendance Date",
	           dataIndex: 'attendance_date_time',
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
		bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: pendingAttendancesStore,
            displayInfo: true,
            displayMsg: 'Displaying attendances {0} - {1} of {2}',
            emptyMsg: "No attendances"
        })
    });

    pendingGrid.getSelectionModel().singleSelect = true;
    
    var attendanceFormColumn1 = { // Column 1
        width:380,
        labelWidth: 110,
        layout: 'form',
		items: [
			{
                xtype:'trigger',
                triggerClass: 'x-form-search-trigger',
                fieldLabel: 'Person',
                name: 'Person.full_name',
                allowBlank:false,
                readOnly: true,
                onTriggerClick: function () {
                	IOH.showPeopleWindow({
	            		targetForm: form,
	            		personId: form.getForm().findField('Attendance.person_id').getValue(),
	            		nameField: 'Person.full_name',
	            		valueField: 'Attendance.person_id'
	            	});
                }
            },{
                xtype:'datefield',
            	fieldLabel: 'Attendance Date',
            	name: 'Attendance.attendance_date_ext'
            },{
            	xtype: 'combo',
            	mode: 'remote',
			    editable: false,
			    triggerAction: 'all',
	   	        loadingText: 'Loading ...',
                store: new Ext.data.JsonStore({
                	url: '<?=$html->url('/attendanceReasons.json')?>',
                	root: 'rows',
                	fields: [
                		'code', 'description'
                	],
                	autoLoad: true
                }),
                displayField: 'description',
                valueField: 'code',
            	fieldLabel: 'Attendance Reason',
            	hiddenName: 'Attendance.attendance_reason_code',
                allowBlank:false
            },{
                xtype:'datefield',
            	fieldLabel: 'Seen at Date',
            	name: 'Attendance.seen_at_date_ext'
            }, {
            	xtype: 'combo',
            	store: new Ext.data.JsonStore({
                	url: '<?=$html->url('/users/combo.json')?>',
                	root: 'rows',
                	fields: [
                		'id', 'name'
                	],
                	autoLoad: true
            	}),
            	mode: 'remote',
			    triggerAction: 'all',
	   	        loadingText: 'Loading ...',
                displayField: 'name',
                valueField: 'id',
            	fieldLabel: 'Seen By',
            	hiddenName: 'Attendance.clinic_staff_id'
			}, {
            	xtype: 'combo',
            	mode: 'remote',
			    editable: false,
			    triggerAction: 'all',
	   	        loadingText: 'Loading ...',
                store: new Ext.data.JsonStore({
                	url: '<?=$html->url('/attendanceResults.json')?>',
                	root: 'rows',
                	fields: [
                		'code', 'description'
                	],
                	autoLoad: true
                }),
                displayField: 'description',
                valueField: 'code',
            	fieldLabel: 'Attendance Result',
            	hiddenName: 'Attendance.attendance_result_code'
            }, {
            	xtype: 'hidden',
            	name: 'Attendance.diagnosis_id'
            }, {
                xtype:'trigger',
                width: 240,
                triggerClass: 'x-form-search-trigger',
                fieldLabel: 'Diagnosis',
                name: 'Diagnosis.description',
                readOnly: true,
                onTriggerClick: function () {
                	IOH.showDiagnosesWindow({
	            		targetForm: form,
	            		diagnosisId: form.getForm().findField('Attendance.diagnosis_id').getValue()
	            	});
                }
            },{
				layout: 'column',
    			border: false,
    			hideBorders: true,
                style: 'padding-left: 110px; margin-right: -60px;',
				items: [
					{
		                columnWidth:.36,
		                layout: 'form',
		                labelWidth: 1,
		                items: [
		                	{
				            	xtype: 'checkbox',
				            	boxLabel: 'Work Related',
						        labelSeparator: '',
						        inputValue: 'Y',
				            	name: 'Attendance.work_related_absence',
				            	listeners: {
				            		check: function (c, checked) {
				            			var f = form.getForm().findField('Attendance.accident_report_complete');
				            			checked?f.enable():f.disable();
				            		}
				            	}
				            },{
				            	xtype: 'checkbox',
				            	boxLabel: 'Work Discomfort',
						        inputValue: 'Y',
						        labelSeparator: '',
				            	name: 'Attendance.work_discomfort'
				            }
		                ]
					}, {
		                columnWidth:.64,
		                layout: 'form',
		                labelWidth: 1,
						items: [{
				            	xtype: 'checkbox',
				            	boxLabel: 'Review Attendance',
						        inputValue: 'Y',
						        labelSeparator: '',
				            	name: 'Attendance.review_attendance'
				            },{
				            	xtype: 'checkbox',
				            	boxLabel: 'Accident Report Complete',
						        inputValue: 'Y',
						        labelSeparator: '',
				            	name: 'Attendance.accident_report_complete',
				            	disabled: true
				            }
						]
					}
				]
			}
        ]
	};
	
	var attendanceFormColumn2 = { // Column 2
        width:350,
        layout: 'form',
        labelWidth: 60,
        hideBorders: true,
		items: [{
            	xtype: 'combo',
            	mode: 'remote',
			    editable: false,
			    triggerAction: 'all',
	   	        loadingText: 'Loading ...',
                store: new Ext.data.JsonStore({
                	url: '<?=$html->url('/clinics.json')?>',
                	root: 'rows',
                	fields: [
                		'id', 'clinic_name'
                	],
                	autoLoad: true
                }),
                displayField: 'clinic_name',
                valueField: 'id',
        		fieldLabel: 'Clinic',
		        hiddenName: 'Attendance.clinic_id',
                allowBlank:false
            }, {
            	xtype: 'timefield',
                fieldLabel: 'Time',
                width: 80,
                name: 'Attendance.attendance_time_ext'
            }, {
            	xtype: 'textfield',
            	style: 'visibility: hidden;',
            	hideLabel: true
            }, {
            	xtype: 'timefield',
                fieldLabel: 'Time',
                width: 80,
                name: 'Attendance.seen_at_time_ext'
            }, {
            	xtype: 'textarea',
            	fieldLabel: 'Comments',
				labelAlign: 'top',
            	name: 'Attendance.comments',
            	height: 120,
            	width: '95%'
			}
    	]
	};


    // Create attendance form
    var form = new Ext.FormPanel({
    	id: 'attendance-form',
        labelAlign: 'left', // label settings here cascade unless overridden
        url:'<?=$html->url('/attendances/save.json')?>',
        title: 'Attendance Form',
        bodyStyle:'padding:5px 5px 0;',
		split: true,
		region: 'north',
		height: 305,
		autoScroll: true,
		hideBorders: true,
		collapsible: true,
		items: [
        	{
        		xtype:'hidden',
        		name: 'Attendance.id'
        	},{
        		xtype:'hidden',
        		name: 'Attendance.person_id'
        	},{
        		layout: 'column',
				width: 770,
				hideBorders: true,
        		items: [
        			attendanceFormColumn1,
        			attendanceFormColumn2
				]
        	}
        ],

        buttons: [
        {
            text: 'Save',
            disabled: <?php echo $readOnly ?>,
            handler: function () {
            	var isUpdate = form.getForm().findField('Attendance.id').getValue();
				form.getForm().waitMsgTarget = 'attendance-form';
            	form.getForm().submit({
            		url: '<?=$html->url('/attendances/save.json')?>',
            		success: function () {
            			if (true || !isUpdate) {
		            		form.getForm().reset();
            			}
            			reloadPendingGrid();
            		},
					waitMsg: (isUpdate?'Updating ...':'Saving ...')
            	});
            }
        },
        {
            text: 'Reset',
            handler: function () {
            	form.getForm().reset();
            	attendancesStore.removeAll();
            }
        }, new Ext.Toolbar.Fill(), {
        	text: 'Find',
        	handler: function () {
        		var tabPanel = IOH.contentPanel.findById('attendances-tab-panel');
        		tabPanel.activate(grid);
        		reloadAttendancesGrid(form.getForm().getValues());
        	}
        }],
        
        loadAttendance: function (attendanceId) {
//        	if (this.getForm().isDirty() && !confirm('Record changed. If you continue, you will loose the changes.')) {
//        		return;
//        	}
			this.getForm().waitMsgTarget = this.getEl();
			this.load({
				url: '<?=$html->url('/attendances/load')?>/' + attendanceId + '.json',
				scripts: false,
				waitMsg: 'Loading attendance record ...',
				scope: this
			});
        }
        
    });

    grid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
    	form.loadAttendance(record.id);
	});
    pendingGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
    	form.loadAttendance(record.id);
	});
	
	IOH.contentPanel.replace({
    	layout: 'border',
		defaults: {
			border: false
		},
		hideBorders: true,
    	items: [
    		form,
    		{
    			id: 'attendances-tab-panel',
    			region: 'center',
		    	xtype: 'tabpanel',
		    	activeTab: 0,
    			bodyBorder: true,
    			minHeight: 200,
		    	items: [pendingGrid, grid]
    		}
    	]
    });
    
	var updatePendingAttendancesTask = {
	    run: function(){
	        reloadPendingGrid();
	    },
	    interval: 1000*60 //1 minute
	}
	Ext.TaskMgr.start(updatePendingAttendancesTask);    
}

Ext.onReady(function () {
	try {
		onReady();
	} catch (e) {
		alert(e.message);
	}
});
</script>
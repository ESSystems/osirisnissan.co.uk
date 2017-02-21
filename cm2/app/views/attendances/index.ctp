<script type="text/javascript">
Ext.onReady(function () {
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
			'seen_at_time'
		],
		remoteSort: true
	});

	attendancesStore.on('beforeload', function (s, options) {
		options.params = Ext.apply(options.params, attendancesStore.filterParams);
	});

	// Helper handlers
	var reloadPeopleGrid = function (filter) {
		for (var i in filter) {
			if (!filter[i]) {
				filter[i] = undefined;
			}
		}
		peopleStore.filterParams = filter;
	    peopleStore.load({params:{start:0, limit:25}});
	}

	var reloadAttendancesGrid = function (filter) {
		for (var i in filter) {
			if (!filter[i]) {
				filter[i] = undefined;
			}
		}
		attendancesStore.filterParams = filter;
	    attendancesStore.load({params:{start:0, limit:25}});
	}

	var peopleStore = new Ext.data.JsonStore({
						url: '<?=$html->url('/persons/page.json')?>',
						totalProperty: 'totalRows',
						successProperty: 'success',
						root: 'rows',
						id: 'id',
						fields: [
							'title',
							'first_name',
							'middle_name',
							'last_name'
						],
						remoteSort: true
					});

	peopleStore.on('beforeload', function (s, options) {
		options.params = Ext.apply(options.params, peopleStore.filterParams);
	});

	// Create people grid
	var peopleGrid = new Ext.grid.GridPanel({
		el1: 'people-grid',
		store: peopleStore,
		columns: [
			{
				header: 'Title',
				dataIndex: 'title',
				width: 30
			}, {
				header: 'First Name',
				dataIndex: 'first_name',
				sortable: true
			}, {
				header: 'Middle Name',
				dataIndex: 'middle_name',
				sortable: true
			}, {
				header: 'Last Name',
				dataIndex: 'last_name',
				sortable: true
			}
		],
		border: false,
		bodyBorder: false,
		height: 120,
		autoScroll:true,
		loadMask: true,
        viewConfig: {
            autoFill:true
        },
        bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: peopleStore,
            displayInfo: true,
            displayMsg: 'Displaying matches {0} - {1} of {2}',
            emptyMsg: "No people found."
        }),
        collapsible: true
	});

	var peopleForm = new Ext.form.FormPanel({
        frame:false,
        border: false,
        bodyBorder: false,
        labelWidth: 70,
		layout: 'form',
		items: [
			{
				xtype: 'hidden',
				name: 'Person.id'
			},
			{
				layout: 'column',
				labelAlign: 'top',
				border: false,
				items: [{
					columnWidth: 0.33,
					border: false,
					layout: 'form',
					items: [
						{
			                xtype:'textfield',
			                fieldLabel: 'First Name',
			                name: 'Person.first_name'
			            }
					]
				}, {
					columnWidth: 0.33,
					border: false,
					layout: 'form',
					items: [
						{
			                xtype:'textfield',
			                fieldLabel: 'Middle Name',
			                name: 'Person.middle_name'
			            }
					]
				}, {
					columnWidth: 0.33,
					border: false,
					layout: 'form',
					items: [
						{
			                xtype:'textfield',
			                fieldLabel: 'Last Name',
			                name: 'Person.last_name'
						}
					]
				}]
			}, new Ext.Panel(
			{
				colapsible: true,
				title: 'Details',
				autoScroll: true,
				layout: 'column',
				labelAlign: 'left',
				bodyStyle: 'padding: 5px;',
				items: [
					{
						columnWidth: 0.50,
						layout: 'form',
			            defaultType: 'textfield',
						border: false,
						items: [
							{
								fieldLabel: 'Title',
								name: 'Person.title'	
							},
							{
								xtype: 'datefield',
								fieldLabel: 'Date of Birth',
								name: 'Person.date_of_birth'	
							},
							{
								fieldLabel: 'Address 1',
								name: 'Person.address1'	
							},
							{
								fieldLabel: 'Address 2',
								name: 'Person.address2'	
							},
							{
								fieldLabel: 'Address 3',
								name: 'Person.address3'	
							},
							{
								fieldLabel: 'Email',
								name: 'Person.email_address'	
							}
						]
					},{
						columnWidth: 0.50,
						layout: 'form',
			            defaultType: 'textfield',
						border: false,
						items: [
							{
								fieldLabel: 'Gender',
								name: 'Person.gender'	
							},
							{
								fieldLabel: 'Tel. Number',
								name: 'Person.telephone_number'	
							},
							{
								fieldLabel: 'Extension',
								name: 'Person.extension'	
							},
							{
								fieldLabel: 'County',
								name: 'Person.county_id'	
							},
							{
								fieldLabel: 'Postcode',
								name: 'Person.postcode'	
							},
							{
								fieldLabel: 'Area Code',
								name: 'Person.area_code'	
							}
						]
					}
				]
	        })
		],
		bodyStyle: 'padding: 10px',
		buttons: [{
			text: 'Save',
            handler: function () {
            	peopleForm.getForm().submit({
            		url: '<?=$html->url('/persons/save.json')?>',
            		success: function (f, action) {
            			var result = action.result;
            			f.findField('Person.id').setValue(result.id);
            			reloadPeopleGrid({'Person.id':result.id});
//            			alert('Saved ' + result.id);
            		}
            	});
            }
		}, {
			text: 'Reset',
			handler: function () {
				peopleForm.getForm().reset();
			}
		}, new Ext.Toolbar.Fill(),
		{
			text: 'Find',
			handler: function () {
				reloadPeopleGrid(peopleForm.getForm().getValues());
			}
		}
		]
	});
	
	peopleForm.loadPerson = function (personId) {
		this.load({
			url: '<?=$html->url('/persons/load')?>/' + personId + '.json',
			scripts: false,
			mask: true
		})
	}
	
    // create the grid
    var grid = new Ext.grid.GridPanel({
        store: attendancesStore,
    	columns:[
	    	{
	           header: "Full Name",
	           dataIndex: 'person',
	           sortable: true
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
        collapsible: true,
        title:'Browse Attendances',
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

    // render it
//    grid.render();

	var peopleWindow;

    // Create attendance form
    var form = new Ext.FormPanel({
        labelAlign: 'left', // label settings here cascade unless overridden
        labelWidth: 120,
        url:'<?=$html->url('/attendances/save.json')?>',
        frame:true,
        title: 'Attendance Form',
        bodyStyle:'padding:5px 5px 0',
        autoScroll: true,
        collapsible: true,
        items: [
        	{
        		xtype:'hidden',
        		name: 'Attendance.id'
        	},{
        		xtype:'hidden',
        		name: 'Person.id'
        	},{
        		layout: 'column',
        		items: [
	        		{ // Column 1
		                columnWidth:.35,
		                layout: 'form',
		        		items: [
							{
			                    xtype:'textfield',
				                fieldLabel: 'Person',
				                name: 'Person.full_name',
				                allowBlank:false,
				                readOnly: true
				            },
				            new Ext.Button({
				            	text: 'Select Person',
				            	handler: function () {
						            if (!peopleWindow) {
							            peopleWindow = new Ext.Window({
							            	title: 'Select Person',
							                el:'people-window',
							                layout: 'border',
							                width:550,
							                height:500,
							                closeAction:'hide',
							                plain: false,
							                items: [
												{
							                		region: 'center',
							                		items: peopleForm
							                	}, {
							                		region: 'south',
							                		height: 120,
									                border: false,
							                		items: peopleGrid
							                }],
							                buttons: [{
							                    text:'Select',
							                    handler: function () {
							                    	var v = peopleForm.getForm().getValues();
							                    	var id = v['Person.id'];
							                    	if (!id) {
							                    		alert('Nothing selected');
							                    		return false;
							                    	}
							                    	var fn = v['Person.title'] + ' ' + 
							                    		v['Person.first_name'] + ' ' + 
							                    		v['Person.last_name'];
							                    	var f = form.getForm();
							                    	f.findField('Person.full_name').setValue(fn);
							                    	f.findField('Person.id').setValue(id);
							                    	peopleWindow.hide();
							                    }
							                },{
							                    text: 'Close',
							                    handler: function(){
							                        peopleWindow.hide();
							                    }
							                }]
							            });
						            }
						            var personId = form.getForm().findField('Person.id').getValue();
						            if (personId) {
						            	peopleForm.loadPerson(personId);
						            }
						            peopleWindow.show();
					            },
					            style: 'margin: 0 0 5px 125px;'
				            }),
				            new Ext.form.ComboBox({
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
				            }) ,{
			                    xtype:'datefield',
				            	fieldLabel: 'Seen at Date',
				            	name: 'Attendance.seen_at_date_ext'
				            }, new Ext.form.TimeField({
				                fieldLabel: 'Time',
				                width: 80,
				                name: 'Attendance.seen_at_time_ext',
				                minValue: '8:00',
				                maxValue: '18:00',
				                format: 'H:i'
				            }),{
			                    xtype:'datefield',
				            	fieldLabel: 'Attendance Date',
				            	name: 'Attendance.attendance_date_ext'
				            }, new Ext.form.TimeField({
				                fieldLabel: 'Time',
				                width: 80,
				                name: 'Attendance.attendance_time_ext',
				                minValue: '8:00',
				                maxValue: '18:00',
				                format: 'H:i'
				            }),new Ext.form.ComboBox({
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
				            }), new Ext.form.ComboBox({
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
				            	hiddenName: 'Attendance.attendance_result_code',
				                allowBlank:false
				            })
				        ]
	        		}, { // Column 2
		                columnWidth:.45,
		                layout: 'form',
		                labelWidth: 60,
		        		items: [
		        			{
		        				layout: 'column',
		        				items: [
		        					{
						                columnWidth:.5,
						                layout: 'form',
						                items: [
						                	new Ext.form.Checkbox({
								            	boxLabel: 'Work Related Absence',
										        labelSeparator: '',
								            	name: 'Attendance.work_related_absence'
								            }),new Ext.form.Checkbox({
								            	boxLabel: 'Work Discomfort',
										        labelSeparator: '',
								            	name: 'Attendance.work_discomfort'
								            })
						                ]
		        					}, {
						                columnWidth:.5,
						                layout: 'form',
						                labelWidth: 10,
		        						items: [
		        							new Ext.form.Checkbox({
								            	boxLabel: 'Review Attendance',
										        labelSeparator: '',
								            	name: 'Attendance.review_attendance'
								            }),new Ext.form.Checkbox({
								            	boxLabel: 'Accident Report Complete',
										        labelSeparator: '',
								            	name: 'Attendance.accident_report_complete'
								            })
		        						]
		        					}
		        				]
		        			}, new Ext.form.TextArea({
				            	fieldLabel: 'Comments',
		        				labelAlign: 'top',
				            	name: 'Attendance.comments',
				            	height: 140,
				            	width: '95%'
							})
		            	]
	        		}
        		]
        	}
        ],

        buttons: [
        {
            text: 'Save',
            handler: function () {
            	form.getForm().submit({
            		url: '<?=$html->url('/attendances/save.json')?>',
            		success: function () {
            			alert('Saved');
            		}
            	});
            }
        },{
            text: 'Reset',
            handler: function () {
            	form.getForm().reset();
            }
        }, new Ext.Toolbar.Fill(
        ), {
        	text: 'Find',
        	handler: function () {
        		reloadAttendancesGrid(form.getForm().getValues())
        	}
        }]
    });

    grid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
		form.load({
			url: '<?=$html->url('/attendances/load')?>/' + record.id + '.json',
			scripts: false,
			mask: true
		})
	});

    peopleGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
    	peopleForm.loadPerson(record.id);
	});

//    form.render('attendances-form');

	IOH.contentPanel.clear();
	IOH.contentPanel.add({
    	layout: 'border',
		border: false,
		defaults: {
			border: false
		},
    	items: [
    		{
    			region: 'center',
    			items: form,
    			layout: 'fit'
    		}, {
    			split: true,
    			region: 'south',
    			layout: 'fit',
    			items: grid,
    			minHeight: 150,
    			maxHeight: 300,
    			height: 200
    		}
    	]
    });
    IOH.contentPanel.doLayout();

});
</script>
<div id="people-window" class="x-hidden">
</div>
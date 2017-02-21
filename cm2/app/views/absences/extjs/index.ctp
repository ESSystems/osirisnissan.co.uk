<?php $readOnly = ($disableAbsences !== false)?'true':'false'; ?>

<script type="text/javascript">

function onReady() {
	// Create data store
	var absencesStore = new Ext.data.JsonStore({
		url: '<?=$html->url('/absences/page.json')?>',
		totalProperty: 'totalRows',
		successProperty: 'success',
		root: 'rows',
		id: 'id',
		fields: [
			'full_name',
			'start_date',
			'end_date',
			'returned_to_work_date',
			'sick_days',
			'department_name',
			'main_diagnosis',
			'person_id'
		],
		remoteSort: true
	});
	
	var sicknotesStore = new Ext.data.JsonStore({
		url: '<?=$html->url('/sicknotes/page.json')?>',
		totalProperty: 'totalRows',
		successProperty: 'success',
		root: 'rows',
		id: 'id',
		fields: [
			'id',
			'type_code',
			'type_name',
			'start_date',
			'end_date',
			'symptoms_description',
			'comments',
			'created'
		],
		remoteSort: true
	});
	
	absencesStore.on('beforeload', function (s, options) {
		options.params = Ext.apply(options.params, absencesStore.filterParams);
	});
	
    var absencesGrid = new Ext.grid.GridPanel({
    	id: 'absences-grid',
    	title: 'Search Results',
        store: absencesStore,
    	columns:[
	    	{
	           header: "Employee",
	           dataIndex: 'full_name',
	           sortable: true,
	           width: 60
	        },{
	           header: "From",
	           dataIndex: 'start_date',
	           sortable: true,
	           width: 30
	        },{
	           header: "To",
	           dataIndex: 'end_date',
	           sortable: true,
	           width: 30
	        },{
	           header: "Returned",
	           dataIndex: 'returned_to_work_date',
	           sortable: true,
	           width: 30
	        },{
	           header: "Sick Days",
	           dataIndex: 'sick_days',
	           sortable: true,
	           width: 30,
	           align: 'right'
	        },{
	           header: 'Department',
	           dataIndex: 'department_name',
	           width: 60
	        },{
	           header: 'Diagnosis',
	           dataIndex: 'main_diagnosis',
	           width: 160
	        }
		],
        loadMask: true,
		viewConfig: {
            forceFit:true,
            autoFill: true,
            emptyText: 'No absences found.'
        },
		bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: absencesStore,
            displayInfo: true,
            displayMsg: 'Displaying absences {0} - {1} of {2}',
            emptyMsg: "No absences found."
        }),
        region: 'center',
        split: true,
        
        reload: function () {
        	this.getStore().reload();
        }
    });

    absencesGrid.getSelectionModel().singleSelect = false; 

    absencesGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
    	var form = IOH.contentPanel.findById('add-absences-form');
    	form.loadAbsence(record.id);
	});

	var reloadAbsencesGrid = function (filter) {
		for (var i in filter) {
			if (!filter[i]) {
				filter[i] = undefined;
			}
		}
		absencesStore.filterParams = filter;
	    absencesStore.load({params:{start:0, limit:25}});
	}
	
	var sicknotesGrid = new Ext.grid.GridPanel({
		id: 'sicknotes-grid',
    	xtype: 'grid',
    	store: sicknotesStore,
    	width: 300,
    	height: 200,
    	border: true,
    	columns: [{
			header: 'ID',
			dataIndex: 'id',
			sortable: true,
			width: 50
    	},{
			header: 'Type',
			dataIndex: 'type_name',
			sortable: true
    	}, {
			header: 'From',
			dataIndex: 'start_date',
			sortable: true,
			width: 60
    	}, {
			header: 'To',
			dataIndex: 'end_date',
			sortable: true,
			width: 60
    	}],
        bbar: [{
    		text: 'Add',
    		id: 'sicknote-add-button',
    		disabled: <?php echo $readOnly ?>,
    		handler: function () {
    			var form = Ext.getCmp('add-absences-form').getForm();
    			var options = {
    				absenceId: form.findField('Absence.id').getValue(),
    				personId: form.findField('Person.id').getValue()
    			};
    			
    			if (!options.personId) {
    				alert('Select an employee first.');
    				return;
    			}
    			
    			IOH.showSicknotesWindow(options);
    		}
    	},{
        	id: 'sicknote-edit-button',
    		text: 'Edit',
    		disabled: true,
    		handler: function () {
    			var options = {
					absenceId: Ext.getCmp('add-absences-form').getForm().findField('Absence.id').getValue()
    			};
    			var selected = sicknotesGrid.getSelectionModel().getSelected();
    			if (selected) {
    				options.sicknoteId = selected.id;
    			}
				IOH.showSicknotesWindow(options);
    		}
    	}, {
        	id: 'sicknote-remove-button',
    		text: 'Remove',
    		disabled: true,
    		handler: function () {
    			var selected = sicknotesGrid.getSelectionModel().getSelected();
    			if (!selected) {
    				return;
    			}
    			if (sicknotesStore.getTotalCount() <= 1) {
    				alert('You cannot remove all sick notes of an absence.');
    				return;
    			}
    			if (!confirm('Are you sure you want to delete this sicknote?')) {
    				return;
    			}
    			Ext.Ajax.request({
					url: '<?=$html->url('/sicknotes/delete.json')?>',
					params: {
						'Sicknote.id': selected.id
					},
					success: function () {
						sicknotesStore.reload();
						addAbsencesForm.reload();
						absencesGrid.reload();
					},
					failure: function () {
						alert('Remove failed.');
					}
				});
    		}
    	}]
    });
    
    sicknotesGrid.getSelectionModel().on('selectionchange', function (selModel) {
    	Ext.getCmp('sicknote-edit-button').setDisabled(<?php echo $readOnly?> || !selModel.hasSelection());
    	Ext.getCmp('sicknote-remove-button').setDisabled(<?php echo $readOnly?> || !selModel.hasSelection());
    });
    
	var addAbsencesForm = {
		xtype: 'form',
		id: 'add-absences-form',
		title1: 'Add / Edit',
		bodyStyle: 'padding: 5px;',
		hideBorders: true,
		autoScroll: true,
		items: [
			{
				xtype: 'hidden',
				name: 'Absence.id'
			}, {
				xtype: 'hidden',
				name: 'Person.id'
			}, {
				layout: 'column',
				hideBorders: true,
				items: [
					{
						width: 340,
						layout: 'form',
						labelWidth: 80,
						items: [{
			                xtype:'trigger',
			                width: 235,
			                triggerClass: 'x-form-search-trigger',
			                fieldLabel: 'Employee',
			                name: 'Person.full_name',
			                readOnly: true,
			                onTriggerClick: function () {
			                	var form = IOH.contentPanel.findById('add-absences-form');
			                	IOH.showPeopleWindow({
				            		targetForm: form,
				            		personId: form.getForm().findField('Person.id').getValue()
				            	});
			                }
			            }, {
							layout: 'column',
							hideBorders: true,
							border: false,
							items: [{
								width: 180,
								layout: 'form',
								items: [{
									xtype: 'datefield',
									fieldLabel: 'From date',
									name: 'Absence.start_date',
									width: 90,
									listeners: {
										change: function () {
											Ext.getCmp('add-absences-form').updateSickDays();
										}
									}
								}, {
									xtype: 'textfield',
									fieldLabel: 'Sick Days',
									name: 'Absence.sick_days',
									width: 90
								}]
							}, {
								width: 160,
								layout: 'form',
								labelWidth: 45,
								items: [{
									xtype: 'datefield',
									fieldLabel: 'To date',
									name: 'Absence.end_date',
									width: 90,
									listeners: {
										change: function () {
											Ext.getCmp('add-absences-form').updateSickDays();
										}
									}
								}, {
									xtype: 'textfield',
									fieldLabel: 'Calc',
									name: 'Absence.calc_sick_days',
									width: 90,
									readOnly: true
								}]
							}]
						}, {
							xtype: 'datefield',
							fieldLabel: 'Returned',
							name: 'Absence.returned_to_work_date'
						}, {
							xtype: 'hidden',
							name: 'Absence.main_diagnosis_code'
						},{
							xtype: 'combo',
							width: 235,
							name: 'MainDiagnosis.description',
							fieldLabel: 'Main Diagnosis',
							mode: 'remote',
							triggerAction: 'all',
							editable: false,
							store: new Ext.data.JsonStore({
								url: '<?=$html->url('/absences/mainDiagnosis.json')?>',
								method: 'post',
			                	root: 'rows',
			                	fields: [
			                		'id', 'description'
			                	]
							}),
							queryParam: 'Absence.id',
							displayField: 'description',
							valueField: 'id',
							listeners: {
								beforequery: function (q) {
									this.lastQuery = '';
									q.query = Ext.getCmp('add-absences-form').getForm().findField('Absence.id').getValue();
								},
								beforeselect: function (combo, record) {
									Ext.getCmp('add-absences-form').getForm().findField('Absence.main_diagnosis_code').setValue(record.data.id);
								}
							}
						},{
			            	xtype: 'checkbox',
			            	boxLabel: 'Work Related',
					        labelSeparator: '',
			            	name: 'Absence.work_related_absence',
			            	inputValue: '1'
			            },{
			            	xtype: 'checkbox',
			            	boxLabel: 'Accident Report Complete',
					        labelSeparator: '',
			            	name: 'Absence.accident_report_completed',
			            	inputValue: '1'
			            },{
			            	xtype: 'checkbox',
			            	boxLabel: 'Work Discomfort',
					        labelSeparator: '',
			            	name: 'Absence.discomfort_report_completed',
			            	inputValue: '1'
			            },{
			            	xtype: 'checkbox',
			            	boxLabel: 'Neither',
					        labelSeparator: '',
			            	name: 'Absence.tickbox_neither',
			            	inputValue: '1'
			            }]
					}, {
						layout: 'form',
						width: 300,
						labelWidth: 0,
						hideBorders: true,
						items: [{
							html: 'Sicknotes', style: 'font: Icon;'
						},sicknotesGrid]
					}
				]
			}], 
		tbar: [{
			id: 'save-absence-button',
			text: 'Save',
			disabled: true,
            handler: function () {
				var form = IOH.contentPanel.findById('add-absences-form');
            	var isUpdate = form.getForm().findField('Absence.id').getValue();
				form.getForm().waitMsgTarget = 'add-absences-form';
            	form.getForm().submit({
            		url: '<?=$html->url('/absences/save.json')?>',
            		success: function () {
            			if (!isUpdate) {
		            		form.getForm().reset();
            			}
            			absencesGrid.getSelectionModel().clearSelections();
            			absencesGrid.reload();
            		},
					waitMsg: (isUpdate?'Updating ...':'Saving ...')
            	});
            },
            cls: 'x-btn-text-icon',
			icon: '/img/save-button.gif'
		},{
			text: 'Find',
			cls: 'x-btn-text-icon',
			icon: '/img/search-button.gif',
        	handler: function () {
            	var form = IOH.contentPanel.findById('add-absences-form');
        		reloadAbsencesGrid(form.getForm().getValues());
        	}
		}, {
			text: 'Reset',
			cls: 'x-btn-text-icon',
            handler: function () {
            	var form = IOH.contentPanel.findById('add-absences-form');
            	form.getForm().reset();
            	sicknotesStore.removeAll();
            	absencesGrid.getSelectionModel().clearSelections();
        		reloadAbsencesGrid(form.getForm().getValues());
        		Ext.getCmp('sicknote-edit-button').disable();
        		Ext.getCmp('save-absence-button').disable();
            }
		},{
        	text: 'Merge',
			cls: 'x-btn-text-icon',
			disabled: <?php echo $readOnly ?>,
        	handler: function () {
        		var selection = absencesGrid.getSelectionModel().getSelections();
        		var params = {
        			person_id: undefined
        		};
        		
        		if (selection.length < 2) {
        			alert('Please select two or more absences from the list.');
        			return false;
        		}
        		for (var i = 0; i < selection.length; i++) {
        			if (params.person_id == undefined) {
        				params.person_id = selection[i].data.person_id;
        			} else if (params.person_id != selection[i].data.person_id) {
        				alert('Selected absences are not for a single employee.');
        				return false;
        			}
        			params['id['+i+']'] = selection[i].id;
        		}
        		
        		if (!confirm('Do you want to merge the selected ' + selection.length + ' absences?')) {
        			return false;
        		}
        		
        		new Ext.data.JsonStore({
    				url: '<?=$html->url('/absences/merge.json')?>',
    				root: '',
    				fields: [
    					'success',
    					'errors',
    					'new_id'
    				],
    				listeners: {
    					load: function (store) {
    						var resp = store.reader.jsonData[0];
    						if (!resp.success) {
    							alert(resp.errors);
    							return;
    						}
    						
    						Ext.getCmp('add-absences-form').loadAbsence(resp.new_id);
    						absencesGrid.reload();
    					},
    					loadexception: function () {
    						alert('load exception');
    					}
    				}
        		}).load({
	        		params: params
        		});
        	}
        }],
		
        loadAbsence: function (absenceId, silent) {
        	var form = IOH.contentPanel.findById('add-absences-form');
        	if (!silent) {
				form.getForm().waitMsgTarget = form.getEl();
        	}
			form.getForm().load({
				url: '<?=$html->url('/absences/load')?>/' + absenceId + '.json',
				scripts: false,
				waitMsg: 'Loading absence record ...',
				success: function () {
	        		Ext.getCmp('save-absence-button').setDisabled(<?php echo $readOnly ?>);
				}
			});
			this.loadSicknotes(absenceId);
       },
       
		reload: function () {
			var id = Ext.getCmp('add-absences-form').getForm().findField('Absence.id').getValue();
			if (id) {
				this.loadAbsence(id);
			}
		},
        
        loadSicknotes: function (absenceId) {
		    sicknotesStore.load({
		    	params: {
					'Sicknote.absence_id' : absenceId
				},
				callback: function () {
					Ext.getCmp('sicknote-add-button').setDisabled(<?php echo $readOnly ?>);
				}
		    });
        },
        
		updateSickDays: function () {
			var form      = this.getForm();
			var startDate = form.findField('Absence.start_date').getValue();
			var endDate   = form.findField('Absence.end_date').getValue();
			
			if (!startDate || !endDate) {
				return;
			}
			
			var sickDays = daysBetween(startDate, endDate) + 1;
			
			form.findField('Absence.sick_days').setValue(sickDays);
			form.findField('Absence.calc_sick_days').setValue(sickDays);
		}

	}
    
    IOH.contentPanel.replace({
    	layout: 'border',
    	hideBorders: true,
    	items: [
	    	{
       			deferredRender:false,
		    	border: false,
	    		xtype: 'panel',
				region: 'north',
				split: true,
				height: 280,
				activeTab: 1,
		    	hideBorders: true,
				items: [
					addAbsencesForm
				]
	    	},
    		absencesGrid
    	]
    	
    });
    
    reloadAbsencesGrid({});
}

Ext.onReady(function () {
	try {
		onReady();
	} catch (e) {
		alert(e.message);
	}
});
</script>
<?php
	$jsonStatusCodesList = array();
	foreach ($statuses as $code=>$desc) {
		$jsonStatusCodesList[] = array($code, $desc);
	}
?>

<script type="text/javascript">
Ext.onReady(function () {
	
	var firstColumn = {
		layout: 'form',
		labelWidth: 100,
		minWidth: 350,
		width: 350,
		style: 'margin-right: 10px;',
		items: [
			{
        		xtype:'hidden',
        		name: 'User.id'
        	}, {
        		xtype:'hidden',
        		name: 'Person.id'
        	}, {
                xtype:'trigger',
                triggerClass: 'x-form-search-trigger',
                fieldLabel: 'Person',
                name: 'Person.full_name',
                allowBlank:false,
                readOnly: true,
                onTriggerClick: function () {
                	IOH.showPeopleWindow({
	            		targetForm: userForm,
	            		personId: userForm.getForm().findField('Person.id').getValue()
	            	});
                },
                width: 160
			}, /*{
                xtype:'textfield',
                fieldLabel: 'Diary?',
                name: 'User.diary_id'
			}, {
                xtype:'textfield',
                fieldLabel: 'Clinic Department',
                name: 'User.clinic_department_id'
			},*/{
				xtype: 'combo',
				fieldLabel: 'Status',
				hiddenName: 'User.sec_status_code',
			    mode: 'local',
			    triggerAction: 'all',
			    editable: false,
				store: new Ext.data.SimpleStore({
					fields: ['status_code', 'status_description'],
					data: <?=$javascript->object($jsonStatusCodesList)?>
				}),
				displayField: 'status_description',
				valueField: 'status_code'
			},{
				xtype: 'fieldset',
				title: 'Password',
				labelWidth: 90,
				collapsible: true,
	            collapsed: false,
				items: [{
					id: 'password-change-hint',
					hidden: true,
					html: '<p>Leave this fields blank if you don\'t want to change password.<br/>&nbsp;</p>'
				},
				{
					xtype: 'textfield',
					fieldLabel: 'Password',
					inputType: 'password',
					readonly: true,
					name: 'User.sec_password'
				},
				{
					xtype: 'textfield',
					fieldLabel: 'Repeat It',
					inputType: 'password',
					readonly: true,
					name: 'User.password_again'
				}]
			}
		]
	};
	
	var userGroupsStore = new Ext.data.JsonStore ({
		url: '<?=$html->url('/admin/users/groups.json')?>',
		totalProperty: 'totalRows',
		successProperty: 'success',
		root: 'rows',
		id: 'id',
		fields: [
			'group_name'
		],
		remoteSort: true
	});
	
	var groupsDelButton = new Ext.Button({
    	text: 'Delete',
    	handler: function () {
    		var userId  = userForm.getForm().findField('User.id').getValue();
    		var group = groupsGrid.getSelectionModel().getSelected();
    		
    		if (!userId || !group) {
    			return;
    		}
    		
    		Ext.Ajax.request({
				url: '<?=$html->url('/admin/users/delGroup')?>',
				success: function () {
					userGroupsStore.reload();
				},
				failure: function () {
					alert('Delete failed');
				},
				params: { 'Ug.user_id': userId, 'Ug.group_id': group.id }
			});
    	}
    });
    
    var groupsCombo = new Ext.form.ComboBox({
    	mode: 'remote',
	    editable: false,
	    triggerAction: 'all',
		loadingText: 'Loading ...',
        store: new Ext.data.JsonStore({
        	url: '<?=$html->url('/groups.json')?>',
        	root: 'rows',
        	fields: [
        		'id', 'group_name'
        	]
        }),
        displayField: 'group_name',
        valueField: 'id',
		fieldLabel: 'New Group',
        hiddenName: 'User.group_id',
        width: 120
    });
	
	var groupsBBar = new Ext.Toolbar([
        groupsCombo,
        {
        	text: 'Add',
        	handler: function () {
        		var userId  = userForm.getForm().findField('User.id').getValue();
        		var groupId = groupsCombo.getValue();
        		if (!userId || !groupId) {
        			return;
        		}
        		
	    		Ext.Ajax.request({
					url: '<?=$html->url('/admin/users/addGroup')?>',
					success: function () {
						userGroupsStore.reload();
					},
					failure: function () {
						alert('Assign failed');
					},
					params: { 'Ug.user_id': userId, 'Ug.group_id': groupId }
				});
        	}
        },
        '-',
        groupsDelButton
	]);
				
	var groupsGrid = new Ext.grid.GridPanel({
		store: userGroupsStore,
		title: 'Groups',
		header: false,
		columns: [
	        {
	           id:'group_name',
	           header: "Assigned to Groups",
	           dataIndex: 'group_name'
	        }
	    ],
		border: true,
		bodyBorder: true,
		autoScroll:true,
		loadMask: true,
        viewConfig: {
            autoFill:true
        },
		height: 150,
		bbar: groupsBBar
	});
	
	groupsGrid.getSelectionModel().on('selectionchange', function () {
		groupsDelButton.setDisabled(!groupsGrid.getSelectionModel().hasSelection());
	});
				
	groupsBBar.on('enable', function () {
		groupsGrid.getSelectionModel().fireEvent('selectionchange');
	});
    
	groupsBBar.disable();
    	
	var userForm = new Ext.FormPanel({
		items: [{
			layout: 'column',
			items: [
				firstColumn,
				{
					layout: 'form',
					width: 250,
					labelAlign: 'top',
					items: groupsGrid
				}
			]
		}],
        buttons: [
        {
            text: 'Save',
            handler: function () {
            	userForm.getForm().submit({
            		url: '<?=$html->url('/admin/users/save.json')?>',
            		success: function (form, action) {
            			userForm.loadUser(action.result.user_id);
            		}
            	});
            }
        },{
            text: 'Reset',
            handler: function () {
            	userForm.getForm().reset();
				userGroupsStore.removeAll();
				groupsBBar.disable();
				groupsCombo.reset();
				userForm.setTitle('User Form [Search Mode]');
				Ext.getCmp('password-change-hint').hide();
            }
        }, new Ext.Toolbar.Fill(
        ), {
        	text: 'Find',
        	handler: function () {
        		reloadUserGrid(userForm.getForm().getValues())
        	}
        }],
        labelAlign: 'left', // label settings here cascade unless overridden
        labelWidth: 120,
        frame:true,
        title: 'User Form',
        bodyStyle:'padding:5px 5px 0',
        autoScroll: true,
        loadUser: function (id) {
			this.load({
				url: '<?=$html->url('/admin/users/load')?>/' + id + '.json',
				scripts: false,
				text: 'Loading ...',
				success: function () {
					userForm.setTitle('User Form [Edit Mode]');
					Ext.getCmp('password-change-hint').show();
				}
			});
        }
	});
	
	userForm.getForm().on('actioncomplete', function (form, action) {
		if (action.type == 'load') {
			var userId = action.result.data['User.id'];
			if (userId) {
				userGroupsStore.load({params:{userId:userId}});
				groupsBBar.enable();
			} else {
				userGroupsStore.removeAll();
				groupsBBar.disable();
			}
		}
	});
	
	var usersStore = new Ext.data.JsonStore({
						url: '<?=$html->url('/admin/users/page.json')?>',
						totalProperty: 'totalRows',
						successProperty: 'success',
						root: 'rows',
						id: 'User.id',
						fields: [
							'Person.full_name',
							'Person.first_name',
							'Person.last_name',
							'User.diary_id',
							'User.clinic_department_id',
							'Status.status_description'
						],
						remoteSort: true
					});
					
    var userGrid = new Ext.grid.GridPanel({
        store: usersStore,
    	columns:[
	    	{
	           header: "Full Name",
	           dataIndex: 'Person.full_name',
	           renderer: function (v, meta, r) {
	           		return r.json.Person.first_name + ' ' + r.json.Person.last_name;
	           },
	           sortable: true
	        },/*{
	           header: "Diary ID?",
	           dataIndex: 'User.diary_id',
	           sortable: true
	        },{
	           header: "Clinic Department ID",
	           dataIndex: 'User.clinic_department_id',
	           sortable: true
	        },*/{
	           header: "Status",
	           dataIndex: 'Status.status_description',
	           sortable: true
	        }
		],
        title:'Browse Users',
        loadMask: true,
		viewConfig: {
            forceFit:true
        },
		bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: usersStore,
            displayInfo: true,
            displayMsg: 'Displaying users {0} - {1} of {2}',
            emptyMsg: "No users"
        })
    });

    userGrid.getSelectionModel().singleSelect = true;	
    
    userGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
    	userForm.loadUser(record.id);
	});

	var reloadUserGrid = function (filter) {
		for (var i in filter) {
			if (!filter[i]) {
				delete filter[i];
			}
		}
		usersStore.filterParams = filter;
	    usersStore.load({params:{start:0, limit:25}});
	}

	usersStore.on('beforeload', function (s, options) {
		options.params = Ext.apply(options.params, usersStore.filterParams);
	});

	IOH.contentPanel.replace({
    	layout: 'border',
		border: false,
		defaults: {
			border: false
		},
    	items: [
    		{
    			region: 'center',
    			items: userForm,
    			layout: 'fit'
    		}, {
    			split: true,
    			region: 'south',
    			layout: 'fit',
    			items: userGrid,
    			minHeight: 150,
    			maxHeight: 300,
    			height: 200
    		}
    	]
	});
	
});
</script>
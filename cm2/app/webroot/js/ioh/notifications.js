IOH.Notifications = Ext.extend(Ext.Container, 
{
	initComponent: function () {
		var notificationsStore = new Ext.data.JsonStore({
			url: '/notifications/page.json',
			totalProperty: 'totalRows',
			successProperty: 'success',
			root: 'rows',
			id: 'Notification.id',
			fields: [
			    {
			    	name: 'referrer',
			    	convert: function(v, json) {
			    		return json.Person != undefined ? json.Person.full_name : '';
			    	}
			    },
			    //'Organisation.OrganisationName',
				'Notification.id',
				'Notification.message',
				'Notification.email_sent',
				{name: 'email_sent_date', mapping: 'Notification.email_sent_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
				'Notification.read',
				{name: 'read_date', mapping: 'Notification.read_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
				'Notification.problems'
			],
			remoteSort: true
		});
	    
		notificationsStore.on('beforeload', function (s, options) {
			options.params = Ext.apply(options.params, notificationsStore.filterParams);
		});
		
	    var notificationsGrid = new Ext.grid.GridPanel({
	    	id: 'notifications-grid',
	        store: notificationsStore,
	        autoExpandColumn: 'message',
	    	columns:[
		    	{
		           header: "Referrer",
		           dataIndex: 'referrer',
		           width: 60
		        },{
		           id: "message",
		           header: "Message",
		           dataIndex: 'Notification.message',
		           width: 150
		        },{
		           header: "Email sent",
		           dataIndex: 'Notification.email_sent',
		           width: 30,
		           renderer: function (v, meta, r) {
		        	   return v == 1 ? "Yes" : "No";
		           }
		        },{
		           header: "Email sent at",
		           dataIndex: 'email_sent_date',
		           width: 40,
		           renderer: Ext.util.Format.dateRenderer('d/m/Y H:i')
		        },{
		           header: "Read",
		           dataIndex: 'Notification.read',
		           width: 30,
		           renderer: function (v, meta, r) {
		        	   return v == 1 ? "Yes" : "No";
		           }
		        },{
		           header: "Read at",
		           dataIndex: 'read_date',
		           width: 40,
		           renderer: Ext.util.Format.dateRenderer('d/m/Y H:i')
		        },{
		        	header: "Problems",
		        	dataIndex: 'Notification.problems',
		        	width: 70
		        }
			],
	        title:'Active Notifications',
	        loadMask: true,
			viewConfig: {
	            forceFit:true
	        },
	        region: 'center',
			bbar: new Ext.PagingToolbar({
	            pageSize: 25,
	            store: notificationsStore,
	            displayInfo: true,
	            displayMsg: 'Displaying notifications {0} - {1} of {2}',
	            emptyMsg: "No notifications"
	        }),
			
			filter: function (filter) {
				for (var i in filter) {
					if (!filter[i]) {
						filter[i] = undefined;
					}
				}
				this.store.filterParams = filter;
			    this.store.load({params:{start:0, limit:25}});
			}
	    });
	
	    notificationsGrid.getSelectionModel().singleSelect = true;	
	    
		var notificationForm = {
			xtype: 'form',
			region: 'north',
			split: true,
			id: 'add-notification-form',
			bodyStyle: 'padding: 5px;',
			hideBorders: true,
			autoScroll: true,
			autoHeight: true,
			labelAlign: 'right',
			items: [
				{
					xtype: 'hidden',
					name: 'Notification.id'
				}, {
					layout: 'column',
					hideBorders: true,
					items: [
						{
							width: 250,
							layout: 'form',
							labelWidth: 80,
							items: [{
								xtype: 'textfield',
				                fieldLabel: 'Referrer',
				                name: 'Person.full_name',
				                readOnly: true
							},{
								xtype: 'textfield',
				                fieldLabel: 'Organisation',
				                name: 'Organisation.OrganisationName',
				                readOnly: true
							},{
								xtype: 'checkbox',
				                fieldLabel: 'Email sent',
				                name: 'Notification.email_sent',
				                inputValue: '1',
				                readOnly: true
							},{
								xtype: 'datefield',
				                fieldLabel: 'Email sent at',
				                name: 'Notification.email_sent_date',
				                readOnly: true,
				                format: 'd/m/Y H:i',
				                width: 120
							},{
								xtype: 'checkbox',
				                fieldLabel: 'Read',
				                name: 'Notification.read',
				                inputValue: '1',
				                readOnly: true
							},{
								xtype: 'datefield',
				                fieldLabel: 'Read at',
				                name: 'Notification.read_date',
				                readOnly: true,
				                format: 'd/m/Y H:i',
				                width: 120
							}]
						}, {
							layout: 'form',
							width: 440,
							hideBorders: true,
							items: [{
								xtype: 'textarea',
				                width: 335,
				                height: 180,
				                fieldLabel: 'Message',
				                name: 'Notification.message',
				                readOnly: true
				            }]
						}
					]
				}], 
			tbar: [{
				id: 'resend-notification-button',
				text: 'Resend notification',
				disabled: true,
	            handler: function () {
					var form = this.findById('add-notification-form');
					form.getForm().waitMsgTarget = 'add-notification-form';
	            	form.getForm().submit({
	            		url: '/notifications/send.json',
	            		success: function (form, r) {
	            			notificationsGrid.getSelectionModel().clearSelections();
							notificationsGrid.store.reload();
							IOH.APP.feedback('Notification', 'Notification was resent.');
	            		},
						waitMsg: 'Sending ...'
	            	});
	            },
	            scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'mail'
			}],
			
	        loadNotification: function (notificationId, silent) {
	        	var form = Ext.getCmp('add-notification-form');
	        	if (!silent) {
					form.getForm().waitMsgTarget = form.getEl();
	        	}
				form.getForm().load({
					url: '/notifications/load/' + notificationId + '.json',
					scripts: false,
					waitMsg: 'Loading notification ...',
					success: function () {
		        		Ext.getCmp('resend-notification-button').enable();
					}
				});
	       },
	       
			reload: function () {
				var id = Ext.getCmp('add-notification-form').getForm().findField('Notification.id').getValue();
				if (id) {
					this.loadNotification(id);
				}
			}
			
		}
	    
	    notificationsGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
	    	Ext.getCmp('add-notification-form').loadNotification(record.id);
		});
	
		var config = {
	    	layout: 'border',
	    	border: false,
	    	items: [
				notificationForm,
				notificationsGrid
	    	]
	    };
		
		Ext.apply(this, config);
		IOH.Notifications.superclass.initComponent.apply(this, arguments);
		
		notificationsGrid.filter({});		
	}
});

Ext.reg('IOH.Notifications', IOH.Notifications);
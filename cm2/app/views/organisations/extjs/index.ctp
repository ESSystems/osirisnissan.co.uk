<script type="text/javascript">

function onReady() {

	var organisationsStore = new Ext.data.JsonStore({
		url: '<?=$html->url('/organisations/page.json')?>',
		totalProperty: 'totalRows',
		successProperty: 'success',
		root: 'rows',
		id: 'Organisation.OrganisationID',
		fields: [
			'Organisation.OrganisationID',
			'Organisation.OrganisationName',
			'Organisation.PhysicalAddressLine1',
			'Organisation.PhysicalAddressLine2',
			'Organisation.PhysicalAddressLine3',
			'Organisation.PhysicalCounty',
			'Organisation.PhysicalPostCode',
			'Organisation.IsClient'
		],
		remoteSort: true
	});
    
	organisationsStore.on('beforeload', function (s, options) {
		options.params = Ext.apply(options.params, organisationsStore.filterParams);
	});
	
    var organisationsGrid = new Ext.grid.GridPanel({
    	id: 'organisations-grid',
        store: organisationsStore,
    	columns:[
	    	{
	           header: "Name",
	           dataIndex: 'Organisation.OrganisationName',
	           sortable: true
	        },{
	           header: "Address",
	           dataIndex: 'Organisation.PhysicalAddressLine1',
   	           renderer: function (v, meta, r) {
   	           		var address = '';
   	           		if (r.json.Organisation.PhysicalAddressLine1) {
   	           			address += r.json.Organisation.PhysicalAddressLine1 + '<br/>';
   	           		}
   	           		if (r.json.Organisation.PhysicalAddressLine2) {
   	           			address += r.json.Organisation.PhysicalAddressLine2 + '<br/>';
   	           		}
   	           		if (r.json.Organisation.PhysicalAddressLine3) {
   	           			address += r.json.Organisation.PhysicalAddressLine3 + '<br/>';
   	           		}
   	           		
   	           		if (!address) {
   	           			address = '<i>n/a</i>';
   	           		}
	           		return address;
	           }
	        },{
	           header: "County",
	           dataIndex: 'Organisation.PhysicalCounty',
	           sortable: true
	        },{
	           header: "Post Code",
	           dataIndex: 'Organisation.PhysicalPostCode',
	           sortable: true
	        },{
	           header: "Client?",
	           dataIndex: 'Organisation.IsClient',
	           sortable: true
	        }
		],
        title:'Browse Companies',
        loadMask: true,
		viewConfig: {
            forceFit:true
        },
        region: 'center',
		bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: organisationsStore,
            displayInfo: true,
            displayMsg: 'Displaying organisations {0} - {1} of {2}',
            emptyMsg: "No organisations"
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

    organisationsGrid.getSelectionModel().singleSelect = true;	
    
	var organisationForm = {
		xtype: 'form',
		id: 'add-organisation-form',
		bodyStyle: 'padding: 5px;',
		hideBorders: true,
		autoScroll: true,
		items: [
			{
				xtype: 'hidden',
				name: 'Organisation.OrganisationID'
			}, {
				layout: 'column',
				hideBorders: true,
				items: [
					{
						width: 340,
						layout: 'form',
						labelWidth: 80,
						items: [{
							xtype: 'textfield',
			                width: 235,
			                fieldLabel: 'Name',
			                name: 'Organisation.OrganisationName'
			            }, {
							xtype: 'textfield',
			                width: 235,
			                fieldLabel: 'Address 1',
			                name: 'Organisation.PhysicalAddressLine1'
						}, {
							xtype: 'textfield',
			                width: 235,
			                fieldLabel: 'Address 2',
			                name: 'Organisation.PhysicalAddressLine2'
						}, {
							xtype: 'textfield',
			                width: 235,
			                fieldLabel: 'Address 3',
			                name: 'Organisation.PhysicalAddressLine3'
						}]
					}, {
						layout: 'form',
						width: 300,
						hideBorders: true,
						items: [{
							xtype: 'textfield',
			                fieldLabel: 'County',
			                name: 'Organisation.PhysicalCounty'
						},{
							xtype: 'textfield',
			                fieldLabel: 'Post code',
			                name: 'Organisation.PhysicalPostCode'
						},{
							xtype: 'checkbox',
			                fieldLabel: 'Is Client',
			                name: 'Organisation.IsClient',
			                inputValue: '1'
						}]
					}
				]
			}], 
		tbar: [{
			id: 'save-organisation-button',
			text: 'Save',
            handler: function () {
				var form = IOH.contentPanel.findById('add-organisation-form');
				form.getForm().waitMsgTarget = 'add-organisation-form';
            	form.getForm().submit({
            		url: '<?=$html->url('/organisations/save.json')?>',
            		success: function (form, r) {
            			form.loadOrganisation(r.result.id);
            			organisationsGrid.getSelectionModel().clearSelections();
						organisationsGrid.store.reload();
            		},
					waitMsg: 'Saving ...'
            	});
            },
            cls: 'x-btn-text-icon',
			icon: '/img/save-button.gif'
		},{
			text: 'Find',
			cls: 'x-btn-text-icon',
			icon: '/img/search-button.gif',
        	handler: function () {
            	var grid = Ext.getCmp('organisations-grid');
            	var form = Ext.getCmp('add-organisation-form');
        		grid.filter(form.getForm().getValues());
        	}
		}, {
			text: 'Reset',
			cls: 'x-btn-text-icon',
            handler: function () {
            	var form = Ext.getCmp('add-organisation-form');
            	form.getForm().reset();
            }
		}],
		
        loadOrganisation: function (organisationId, silent) {
        	var form = Ext.getCmp('add-organisation-form');
        	if (!silent) {
				form.getForm().waitMsgTarget = form.getEl();
        	}
			form.getForm().load({
				url: '<?=$html->url('/organisations/load')?>/' + organisationId + '.json',
				scripts: false,
				waitMsg: 'Loading company record ...',
				success: function () {
	        		Ext.getCmp('save-organisation-button').enable();
				}
			});
       },
       
		reload: function () {
			var id = Ext.getCmp('add-organisation-form').getForm().findField('Organisation.OrganisationID').getValue();
			if (id) {
				this.loadOrganisation(id);
			}
		}
		
	}
    
    organisationsGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
    	Ext.getCmp('add-organisation-form').loadOrganisation(record.id);
	});

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
				height: 140,
				activeTab: 1,
		    	hideBorders: true,
				items: [
					organisationForm
				]
	    	},
    		organisationsGrid
    	]
    	
    });
    
	organisationsGrid.filter({});
}

Ext.onReady(function () {
	try {
		onReady();
	} catch (e) {
		alert(e.message);
	}
});
</script>
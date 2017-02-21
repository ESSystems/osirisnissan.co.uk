IOH.RecallListsStore = Ext.extend(Ext.data.JsonStore, 
{
	constructor: function (config) {
		IOH.RecallListsStore.superclass.constructor.call(this, Ext.apply({
			url: '/recallLists/index.json',
			totalProperty: 'total',
			successProperty: 'success',
	        root: 'rows',
	        id: 'RecallList.id',
	        fields: [
 	            {name: 'RecallList.title'},
	            {name: 'RecallList.recall_list_item_count', type: 'int'},
	            {name: 'RecallList.created', type: 'date', dateFormat: 'Y-m-d H:i:s'},
	            {name: 'RecallList.modified', type: 'date', dateFormat: 'Y-m-d H:i:s'}
	        ],
			remoteSort: true
		}, config));
	}
});

IOH.RecallListsIndex = Ext.extend(Ext.grid.GridPanel, 
{
	initComponent: function () {
		var store =  new IOH.RecallListsStore({
			autoLoad: {params: {start: 0, limit: 50}}
		});
		
		var pagingToolbar = new Ext.PagingToolbar({
            pageSize: 50,
            store: store,
            displayInfo: true,
            displayMsg: 'Displaying recall lists {0} - {1} of {2}',
            emptyMsg: "No recall lists found."
        });
		
		var config = {
			title: 'Recall Lists',
			store: store,
			columns: [{
				id: 'title',
				header: 'Title',
				sortable: true,
				dataIndex: 'RecallList.title'
			},{
				header: 'People',
				dataIndex: 'RecallList.recall_list_item_count',
				sortable: true,
				width: 10
			},{
				header: 'Created',
				dataIndex: 'RecallList.created',
				renderer: Ext.util.Format.dateRenderer('m/d/Y'),
				sortable: true,
				width: 20
			}],
			autoExpandColumn: 'title',
			viewConfig: {
				forceFit: true
			},
			sm: new Ext.grid.RowSelectionModel({
				listeners: {
					selectionchange: this._selectionChanged.createDelegate(this)
				}
			}),
			tbar: [{
				text: 'New',
				handler: this.newList,
				scope: this,
				cls: 'x-btn-text-icon',
				iconCls: 'page_add'
			}, {
				text: 'Rename',
				disabled: true,
				handler: this.renameList,
				scope: this,
				cls: 'x-btn-text-icon',
				iconCls: 'page_edit'
			}, {
				text: 'Delete',
				disabled: true,
				handler: this.deleteList,
				scope: this,
				cls: 'x-btn-text-icon',
				iconCls: 'page_delete'
			}],
			bbar: pagingToolbar
		};

		Ext.apply(this, config);
		
		IOH.RecallListsIndex.superclass.initComponent.apply(this, arguments);
		
		this.gotoLastPage = function () {
			pagingToolbar.onClick('last');
		}
	},
	
	newList: function () {
		Ext.MessageBox.prompt('List Title', 'Please enter list title', function (x, title) {
			if (title) {
				Ext.Ajax.request({
					url: '/recallLists/add.json',
					params: {
						'data[RecallList][title]': title
					},
					success: function () {
						this.getStore().reload();
						IOH.APP.feedback('List Created', 'Attendance List has been successfully created.');
					},
					scope: this
				});
			}
		}, this);
	},
	
	renameList: function () {
		var rec = this.getSelectionModel().getSelected();
		var id     = rec.id;
		var title  = rec.get('RecallList.title');
		
		Ext.MessageBox.prompt('List Title', 'Please enter list title', function (x, title) {
			if (title) {
				Ext.Ajax.request({
					url: '/recallLists/add.json',
					params: {
						'data[RecallList][id]': id,
						'data[RecallList][title]': title
					},
					success: function () {
						this.getStore().reload();
						IOH.APP.feedback('List Renamed', 'Attendance List has been successfully renamed.');
					},
					scope: this
				});
			}
		}, this, false /* not multiline */, title);
	},
	
	deleteList: function () {
		var rec = this.getSelectionModel().getSelected();
		var id     = rec.id;
		
		if (!id) {
			alert('No list selected.');
		}
		Ext.Ajax.request({
			url: String.format('/recallLists/del/{0}.json', id),
			success: function (r) {
				var response = eval('(' + r.responseText + ')');
				if (response.success) {
					this.getStore().reload();
					IOH.APP.feedback('List Deleted', response.message);
				} else {
					alert('List NOT Deleted: ' + response.message);
				}
			},
			scope: this
		});
	},
	
	_selectionChanged: function (sm) {
		var selCount = sm.getCount(); 
	
		this._getDeleteButton().setDisabled(selCount < 1);
		this._getEditButton().setDisabled(selCount != 1);
	},
	
	_getButton: function (i) {
		return this.getTopToolbar().items.get(i);
	},
	
	_getEditButton: function () {
		return this._getButton(1);
	},
	
	_getDeleteButton: function () {
		return this._getButton(2);
	}
});

Ext.reg('IOH.RecallListsIndex', IOH.RecallListsIndex);
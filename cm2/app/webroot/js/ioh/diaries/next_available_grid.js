IOH.NextAvailableGrid = Ext.extend(Ext.grid.GridPanel,
{
	initComponent: function () {
		var cfg = {
			store: {
				xtype: 'directstore',
				directFn: Server.Diaries.direct_availability,
				root: 'data',
				fields: [{
					name: 'Diary.id', type: 'int'
				},{
					name: 'Diary.name'
				},{
					name: 'Diary.default_appointment_type'
				},{
					name: 'Gap.avail_from', type: 'date', dateFormat: 'Y-m-d H:i:s'
				},{
					name: 'Gap.avail_to', type: 'date', dateFormat: 'Y-m-d H:i:s'
				},{
					name: 'Gap.avail_max', type: 'date', dateFormat: 'Y-m-d H:i:s'
				}],
				baseParams: {
					data: {
						after: '',
						diary_id: '',
						length: 10
					}
				}
			},
			columns: [{
				header: 'From',
				dataIndex: 'Gap.avail_from',
				renderer: Ext.util.Format.dateRenderer('d/m/y H:i')
			//},{
			//	dataIndex: 'Gap.avail_to',
			//	renderer: Ext.util.Format.dateRenderer('d/m/y H:i')
			},{
				header: 'To',
				dataIndex: 'Gap.avail_max',
				renderer: Ext.util.Format.dateRenderer('d/m/y H:i')
			},{
				header: 'Diary',
				dataIndex: 'Diary.name'
			}],
			tbar: ['From', {
				xtype: 'xdatefield',
				ref: '../afterDate'
			},{
				xtype: 'button',
				text: 'Show',
				handler: this._onShow,
				scope: this
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.NextAvailableGrid.superclass.initComponent.apply(this, arguments);

		this.on('diarychanged', function (diaryId) {
			var rds = Ext.StoreMgr.get('RegularDiaries');
			var diaryRec = rds.getById(diaryId);
			
			var diaryType = diaryRec.get(Ext.ensible.cal.CalendarMappings.DefaultType.name); 
			
			this.load({
				diary_type: diaryType
			});
		}, this);
		
		this.on('rowdblclick', function (grid, idx) {
			this.fireEvent('showeventeditor', grid.store.getAt(idx));
		}, this);
	},
	
	load: function (params) {
		if (this.el) {
			this.el.mask();
		}
		
		var p = this.store.baseParams.data;
		
		Ext.apply(p, params);
		this.store.setBaseParam('data', p);
		this.store.load({
			callback: function () {
				if (this.el) {
					this.el.unmask();
				}
			},
			scope: this
		});
	},
	
	_onShow: function () {
		var fromDate = this.afterDate.getValue();
		
		this.load({
			after: fromDate
		});
	}
});
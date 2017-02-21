/**
 * 
 */
Ext.ns('IOH.Diary.Restrictions');

IOH.Diary.Restrictions.Grid = Ext.extend(Ext.grid.GridPanel, 
{
	border: false,
	loadMask: true,
	
	initComponent: function () {
		var store = new Ext.data.DirectStore({
			xtype: 'directstore',
			directFn: Server.DiaryRestrictions.direct_index,
			fields: [{
				name: 'DiaryRestriction.id', type: 'int'
			},{
				name: 'DiaryRestriction.type', type: 'boolean'
			},{
				name: 'DiaryRestriction.title', type: 'string'
			},{
				name: 'DiaryRestriction.diary_id', type: 'int'
			},{
				name: 'DiaryRestriction.from_date', type: 'date'
			},{
				name: 'DiaryRestriction.from_time', type: 'string'
			},{
				name: 'DiaryRestriction.to_date', type: 'date'
			},{
				name: 'DiaryRestriction.to_time', type: 'string'
			},{
				name: 'DiaryRestriction.week_day', type: 'string'
			},{
				name: 'DiaryRestriction.month_day', type: 'string'
			},{
				name: 'DiaryRestriction.month_day_str', type: 'string'
			},{
				name: 'DiaryRestriction.month', type: 'string'
			}],
			root: 'data',
			idProperty: 'DiaryRestriction.id'
		});
			
		var pagingToolbar = new Ext.PagingToolbar({
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying rules {0} - {1} of {2}',
			emptyMsg: "No rules found."
		});

		var self = this;
		
		var cfg = {
			store: store,
			columns: [{
				header: 'Title',
				dataIndex: 'DiaryRestriction.title',
				width: 200,
				renderer: function (v, m, rec) {
					var icon = rec.get('DiaryRestriction.type') ? 'add' : 'delete';
					var dateRange = [];
					
					v = '<span class="icon ' + icon + '">' + v + '</span>';
					
					if (rec.get('DiaryRestriction.from_date')) {
						dateRange.push('from ');
						dateRange.push(Ext.util.Format.date(rec.get('DiaryRestriction.from_date'), Ext.form.DateField.prototype.format));
					}
					if (rec.get('DiaryRestriction.to_date')) {
						if (dateRange.length > 0) {
							dateRange.push(' ');
						}
						dateRange.push('to ');
						dateRange.push(Ext.util.Format.date(rec.get('DiaryRestriction.to_date'), Ext.form.DateField.prototype.format));
					}
					
					if (dateRange.length == 5) {
						dateRange.shift();
					}
					
					if (dateRange.length > 0) {
						v += '<span class="icon" style="font-style: italic;">' + dateRange.join('') + '</span>';
					}
					
					return v;
				} 
			},{
				header: 'Time',
				dataIndex: 'DiaryRestriction.from_time',
				renderer: function (v, meta, rec) {
					v = [];
					if (rec.get('DiaryRestriction.from_time') && rec.get('DiaryRestriction.from_time') != '00:00:00') {
						v.push(rec.get('DiaryRestriction.from_time'));
					}
					if (rec.get('DiaryRestriction.to_time') && rec.get('DiaryRestriction.to_time') != '23:59:59') {
						if (v.length > 0) {
							v.push(' ');
						}
						v.push('- ');
						v.push(rec.get('DiaryRestriction.to_time'));
					}
					
					if (v.length == 5) {
						v.shift();
					}
					
					if (v.length == 0) {
						v.push('-');
					}

					return v.join('');
				}
			},{
				header: 'Weekday',
				dataIndex: 'DiaryRestriction.week_day',
				renderer: function (v, m, rec) {
					var i, wd = rec.json.DiaryRestriction.week_day, res = [];
					
					wd = self._compactRanges(wd);
					
					for (i = 0; i < wd.length; i++) {
						if (wd[i][0] == wd[i][1]-1) {
							res.push(Date.getShortDayName((wd[i][0]+1)%7));
							res.push(Date.getShortDayName((wd[i][1]+1)%7));
						} else {
							res.push(Date.getShortDayName((wd[i][0]+1)%7) + ' - ' + Date.getShortDayName((wd[i][1]+1)%7));
						}
					}
					
					return res.join(', ');
				}
			},{
				header: 'Monthday',
				dataIndex: 'DiaryRestriction.month_day_str',
				renderer: function (v, m, rec) {
					var i, wd = rec.json.DiaryRestriction.month_day_arr, res = [];
					
					wd = self._compactRanges(wd);
					
					for (i = 0; i < wd.length; i++) {
						if (wd[i][0] == wd[i][1]-1) {
							res.push(wd[i][0]+1);
							res.push(wd[i][1]+1);
						} else {
							res.push((wd[i][0]+1) + '-' + (wd[i][1]+1));
						}
					}
					
					return res.join(', ');
				}
			},{
				header: 'Month',
				dataIndex: 'DiaryRestriction.month',
				renderer: function (v, m, rec) {
					var i, wd = rec.json.DiaryRestriction.month, res = [];
					
					wd = self._compactRanges(wd);
					
					for (i = 0; i < wd.length; i++) {
						if (wd[i][0] == wd[i][1]-1) {
							res.push(Date.getShortMonthName(wd[i][0]));
							res.push(Date.getShortMonthName(wd[i][1]));
						} else {
							res.push(Date.getShortMonthName(wd[i][0]) + ' - ' + Date.getShortMonthName(wd[i][1]));
						}
					}
					
					return res.join(', ');
				}
			}],
			viewConfig: {
				forceFit: true
			},
			sm: new Ext.grid.RowSelectionModel(),
			bbar: pagingToolbar,
			tbar: [{
				text: 'Add',
				handler: this.onAddRule,
				scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'add'
			}, {
				text: 'Edit',
				handler: this.onEditRule,
				scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'page_edit'
			}, '-', {
				text: 'Up',
				handler: this.onMoveUp,
				scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'arrow_up'
			},{
				text: 'Down',
				handler: this.onMoveDown,
				scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'arrow_down'
			}, '->', {
				text: 'Delete',
				handler: this.onDeleteRule,
				scope: this,
	            cls: 'x-btn-text-icon',
				iconCls: 'cross'
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Diary.Restrictions.Grid.superclass.initComponent.apply(this, arguments);
		
		this.on('show', this.load, this);
	},
	
	load: function () {
		this.store.setBaseParam('diary_id', this.options.calendarId);
		this.store.load();
	},
	
	onAddRule: function () {
		IOH.Diary.Restrictions.WindowForm.show(this.options, {
			success: function () {
				this.onRulesChange();
			},
			scope: this
		});
	},
	
	onEditRule: function () {
		var sel = this.getSelectionModel().getSelected();
		
		if (sel) {
			this.options.id = sel.id;
			IOH.Diary.Restrictions.WindowForm.show(this.options, {
				success: function () {
					this.onRulesChange();
				},
				scope: this
			});
		}
	},
	
	onMoveUp: function () {
		var sel = this.getSelectionModel().getSelected();
		
		if (sel) {
			Server.DiaryRestrictions.direct_move([sel.id, -1], function () {
				this.onRulesChange();
			}, this);
		}
	},
	
	onMoveDown: function () {
		var sel = this.getSelectionModel().getSelected();
		
		if (sel) {
			Server.DiaryRestrictions.direct_move([sel.id, +1], function (res) {
				if (res.success) {
					this.onRulesChange();
				}
			}, this);
		}
	},
	
	onDeleteRule: function () {
		var sel = this.getSelectionModel().getSelected();
		
		if (!sel) {
			return;
		}
		
		Ext.Msg.show({
			title:'Are you sure?',
			msg: 'Do you really want to delete selected rule?',
			buttons: Ext.Msg.YESNO,
			fn: function (buttonId) {
				if (buttonId == 'yes') {
					Server.DiaryRestrictions.direct_delete([sel.id], function (res) {
						if (res.success) {
							this.onRulesChange();
						}
					}, this);
				}
			},
			scope: this,
			icon: Ext.MessageBox.QUESTION
		});		
	},
	
	onRulesChange: function () {
		this.store.reload();
		Ext.StoreMgr.get('appointments').reload();
		
		Server.Appointments.direct_mark_collisions([this.options.calendarId], function (resp) {
			var msg = '', mc = '', c = '';
			
			if (resp.success) {
				if (resp.result.conflict) {
					msg += 'WARNING: ' + resp.result.conflict;
					if (resp.result.marked > 0) {
						mc += '+' + resp.result.marked;
						c   = ', ';
					}
					if (resp.result.cleaned > 0) {
						mc += c + '-' + resp.result.cleaned;
					}
					if (mc) {
						msg += ' (' + mc + ')'; 
					}
					
					msg += ' active appointments are in conflict with non-patient time rules!';
				}
				Ext.StoreMgr.get('ConflictAppointments').reload();
			}
			
			if (msg) {
				alert(msg);
			}
			
		}, this);
	},
	
	/**
	 * @param array arr one dimension array of booleans
	 * @return array two dimensional array; each element is array of two elements - 0: start, 1: end 
	 */
	_compactRanges: function(arr) {
		var result = [], start = false;
		
		arr.push(false);
		
		for (var i = 0; i < arr.length; i++) {
			if (arr[i]) {
				if (start === false) {
					start = i;
				}
				end = i;
			} else if (start !== false) {
				result.push([start, i-1]);
				start = false;
			}
		}
		
		return result;
	}
});
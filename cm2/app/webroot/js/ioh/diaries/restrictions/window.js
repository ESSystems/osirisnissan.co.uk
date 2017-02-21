/**
 * 
 */

Ext.ns('IOH.Diary.Restrictions');

IOH.Diary.Restrictions.Window = Ext.extend(Ext.Window, 
{
	initComponent: function () {
		/*
		this.form = new IOH.Diary.Restrictions.Form({
			bodyStyle: 'padding: 10px'
		});
		*/
		
		var cfg = {
			title: 'Diary Restrictions',
			closeAction: 'hide',
			layout: 'fit',
			width: 500,
			items: new IOH.Diary.Restrictions.Grid({
				ref: 'grid',
				boxMinHeight: 200,
				height: 300
			}),
			autoHeight: true,
			modal: true,
			buttons: [{
				text: 'Close',
				handler: function () {
					this.hide();
				},
				scope: this
			}]
		};
		
		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);
		
		IOH.Diary.Restrictions.Window.superclass.initComponent.apply(this, arguments);
		
		this.on('show', function () {
			var crec = IOH.APP.calendarStore.getById(this.options.calendarId);
			this.setTitle(crec.get(Ext.ensible.cal.CalendarMappings.Title.name) + ' - Non Patient Time')
			this.grid.options = this.options; 
		}, this);

		this.grid.relayEvents(this, ['show']);
	}
});


IOH.Diary.Restrictions.Window.show = function (options, callback) {
	var w;
	
	if (!(w = Ext.WindowMgr.get('diary-restrictions-window'))) {
		w = new IOH.Diary.Restrictions.Window({
			id: 'diary-restrictions-window'
		});
	}
	
	w.options = options;
	w.callback = callback;
	w.show();
}
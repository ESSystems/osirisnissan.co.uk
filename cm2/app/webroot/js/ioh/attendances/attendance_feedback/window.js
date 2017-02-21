/**
 * 
 */
Ext.ns('IOH.AttendanceFeedback');

var feedbackManager = new Ext.WindowGroup();
feedbackManager.zseed = 7000;

IOH.AttendanceFeedback.Window = Ext.extend(Ext.Window,
{
	attendance_id: null,
	title: 'Attendance Feedback',
	
	initComponent: function () {
		var cfg = {
			manager: feedbackManager,
			closeAction: 'hide',
			items: [new Ext.TabPanel({
				ref: 'tabs',
			    activeTab: 0,
			    height: 300,
				width: 480,
				border: false,
				layoutOnTabChange: true,
			    items: [
				    new IOH.AttendanceFeedback.Form({
						ref: 'form'
					}),
					new IOH.FileUpload({
						ref: 'file_upload',
						attachable_type: "AttendanceFeedback"
					}),
					new IOH.AttendanceFeedback.Followers({
						ref: 'followers'
					})
				]
			})],
			autoHeight: true,
			width: 495,
			modal: true,
			buttons: [{
				text: 'Save',
				handler: this.onSave,
				scope: this
			},{
				text: 'Cancel',
				handler: function () {
					this.hide();
				},
				scope: this
			}]
		};

		Ext.apply(this, cfg);
		
		IOH.AttendanceFeedback.Window.superclass.initComponent.apply(this, arguments);
	},
	
	onSave: function () {
		this.el.mask('Saving ...');
		
		params['AttendanceFeedback.attendance_id'] = this.attendance_id;
		
		this.tabs.form.getForm().submit({
			url: '/attendance_feedback/save.json',
			params: params,
			success: function () {
				this.el.unmask();
				this.close();
				if (this.callback && Ext.isFunction(this.callback.success)) {
					this.callback.success.call(this.callback.scope || this)
				}
			},
			failure: function () {
				this.el.unmask();
			},
			scope: this
		})
	}
});


IOH.AttendanceFeedback.Window.show = function (options, callback) {
	var w;
	
	aid = options, params = {};
	
	if (!(w = Ext.WindowMgr.get('attendance-feedback-window'))) {
		w = new IOH.AttendanceFeedback.Window({
			id: 'attendance-feedback-window'
		});
	}
	
	w.attendance_id = aid;
	w.tabs.setActiveTab(0);
	w.callback = callback;
	w.tabs.file_upload.attachable_type_condition = 'attendance_id';
	w.tabs.file_upload.attachable_type_condition_value = aid;
	w.tabs.file_upload.initialize();
	w.tabs.form.loadAttendanceFeedback(aid);
	w.tabs.followers.loadFollowers(aid);
	
	w.show();
}
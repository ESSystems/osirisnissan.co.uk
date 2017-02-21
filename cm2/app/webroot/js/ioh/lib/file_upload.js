IOH.FileUpload = Ext.extend(Ext.form.FormPanel, {
	attachable_id: null,
	attachable_type: null,
	attachable_type_condition: null,
	// can be used if attachable_id is not known or set but an attribute of attachable_type is
	attachable_type_condition_value: null,
	// the value that the attribute of the attachable_type has
	initComponent: function() {
		var cfg = {
			title: 'Attached documents',
			hideBorders: true,
			padding: 15,
			id: 'file-upload-form',
			labelAlign: 'right',
			autoScroll: true,
			items: [{
				id: 'attached-files',
				xtype: 'container'
			}, {
				xtype: 'component',
				autoEl: {
					tag: 'div',
					cls: 'x-form-item',
					style: 'font-size: 1em;',
					html: 'Please, select the files you wish to add.'
				}
			}, {
				ref: 'awesomeuploader',
				xtype: 'awesomeuploader',
				gridHeight: 100,
				height: 160,
				awesomeUploaderRoot: '/js/awesomeuploader_v1.3.1/',
				flashUploadUrl: '/documents/upload/',
				standardUploadUrl: '/documents/upload/',
				extraPostData: {
					'Document.attachable_id': this.attachable_id,
					'Document.attachable_type': this.attachable_type,
					'attachable_type_condition': this.attachable_type_condition,
					'attachable_type_condition_value': this.attachable_type_condition_value
				},
				standardUploadFilePostName: 'Document',
				maxFileSizeBytes: 15728640,
				// 15 * 1024 * 1024 = 15 MiB
				disableFlash: true,
				listeners: {
					scope: this,
					fileupload: function(uploader, success, result) {
						if (success) {
							Ext.Msg.alert('File Uploaded!', 'A file has been uploaded!');
							this.attached_files(this.attachable_id, this.attachable_type);
						}
					}
				}
			}]
		};

		Ext.apply(this, cfg);
		Ext.apply(this.initialConfig, cfg);

		IOH.FileUpload.superclass.initComponent.apply(this, arguments);
	},
	initialize: function(attachable_id) {
		this.attachable_id = attachable_id;
		this.awesomeuploader.extraPostData = {
			'Document.attachable_id': this.attachable_id,
			'Document.attachable_type': this.attachable_type,
			'AttendanceFeedback.attachable_type_condition': this.attachable_type_condition,
			'AttendanceFeedback.attachable_type_condition_value': this.attachable_type_condition_value
		}, this.awesomeuploader.grid.store.removeAll();
		this.attached_files(attachable_id, this.attachable_type);
	},

	attached_files: function(attachable_id, attachable_type) {
		Ext.getCmp("attached-files").removeAll();
		Ext.Ajax.request({
			url: '/documents/attached_files/' + attachable_id + '/' + attachable_type,
			params: {
				'attachable_type_condition': this.attachable_type_condition,
				'attachable_type_condition_value': this.attachable_type_condition_value
			},
			success: function(result, request) {
				var jsonData = Ext.util.JSON.decode(result.responseText);
				if (jsonData.success) {
					for (i = 0; i < jsonData.data.length; i++) {
						var a = jsonData.data[i].Document;

						var attachmentButton = new IOH.Hyperlink({
							text: a.show_name,
							href: a.document_url,
							id: "attachementButton-" + a.id
						});

						var deleteButton = new Ext.BoxComponent({
							xtype: 'box',
							html: '<div class="cross" style="width:16px;height:16px;cursor:pointer;"></div>',
							style: "margin-left:5px",
							show_name: a.show_name,
							deleteId: a.id,
							id: "deleteButton-" + a.id,
							listeners: {
								render: function(c) {
									c.getEl().on('mousedown', function() {
										Ext.Msg.confirm(
											"Delete file",
											"Are you sure that you want to delete this file: " + this.show_name,
											function(btn) {
												if(btn == "yes") {
													Ext.Ajax.request({
														url: '/documents/delete/' + this.deleteId,
														success: function(result, request) {
															var jsonData = Ext.util.JSON.decode(result.responseText);
															if (jsonData.success) {
																Ext.getCmp("deleteButton-" + jsonData.deletedId).destroy();
																Ext.getCmp("attachementButton-" + jsonData.deletedId).destroy();
																Ext.getCmp("attached-files").doLayout();
																Ext.Msg.alert('Attachement Deleted!', 'The attachement was deleted!');
															} else {
																IOH.APP.feedback("Document delete error", "The file could not be deleted");
															}
														},

														failure: function() {
															IOH.APP.feedback("Document delete error", "There was a problem when contacting the server");
														}
													});
												}
											},
											this
										);
										Ext.MessageBox.getDialog().getEl().setStyle('z-index','80000');
									}, c, {
										stopEvent: false
									});
								}
							}
						});

						Ext.getCmp("attached-files").add(new Ext.Container({
							layout: 'hbox',
							style: 'margin-top: 3px',
							items: [
								attachmentButton,
								deleteButton
							]
						}));
					}
					Ext.getCmp("attached-files").doLayout();
				}
			},

			failure: function() {
				IOH.APP.feedback("Attached files error", "There was a problem when contacting the server");
			}
		});
	}
});

Ext.reg('IOH.FileUpload', IOH.FileUpload);
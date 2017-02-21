IOH.Attendances.AttendanceForm = Ext.extend(Ext.form.FormPanel,
{
  initComponent: function () {
    var form = this;
    var self = this;

      var pendingRecalls = new IOH.PendingRecalls({
      title: false,
      border: true,
      fieldLabel: 'Recalls',
      height: 100,
          anchor: '95%'
    });

      var feedbackButton = new Ext.Button({
          xtype: 'button',
          text: 'Feedback',
          disabled: true,
          id: 'feedback-button',
          onClick: function() {
            if(!this.disabled) {
              IOH.AttendanceFeedback.Window.show(form.getForm().findField('Attendance.id').getValue(), {
                success: function () {
                  IOH.APP.feedback('Attendance Feedback', 'Report was saved.');
                },
                scope: this
              });
            }
          }
    });

      var followupAppointmentButton = new Ext.Button({
          xtype: 'button',
          text: 'Create Followup Appointment',
          disabled: true,
          style: 'margin-left:5px',
          id: 'followup-appointment-button',
          onClick: function() {
            if(!this.disabled) {
              IOH.Appointments.FollowupWindow.show(form.getForm().findField('Attendance.id').getValue(), {
                success: function () {
                  IOH.APP.feedback('Followup Appointment', 'Appointment was saved.');
                  Ext.Msg.alert('Success', 'Please select the date of the follow up appointment.');
                  IOH.APP.navigator().go('diary-section');
                },
                scope: this
              });
            }
          }
    });

      var personCombo = new IOH.PersonCombo({
        id: 'attendance-person',
        fieldLabel: 'Person',
        name: 'Person.full_name',
        hiddenName: 'Attendance.person_id',
        anchor: '95%',
        onTriggerClick: function () {
          var combo = this;
          IOH.APP.showPeopleWindow({
            onSelect: function (peopleRecs, peopleWindow) {
              peopleWindow.hide();

              var json = [];
              Ext.each(peopleRecs, function (rec) {
                json.push(rec.json);
              });

              combo.getStore().loadData({rows: json});

              var fst = combo.store.getAt(0);
              if (fst && fst.get('id')) {
                combo.setValue(fst.get('id'));
              }
            },
            personId: form.getForm().findField('Attendance.person_id').getValue()
          });
        }
      });

      var attendanceFormColumn1 = { // Column 1
        labelWidth: 100,
        items: [
            personCombo,{
          xtype:'xdatetime'
          ,name: 'Attendance.attendance_date_time'
          ,fieldLabel:'Attendance Date'
          ,anchor:'95%'
          ,timeFormat:'H:i'
          ,timeConfig: {
            altFormats:'H:i:s'
            ,allowBlank:true
          }
          ,dateFormat:'d/m/Y'
          ,dateConfig: {
            altFormats:'Y-m-d|Y-n-d'
            ,allowBlank:true
          }
            },{
              xtype: 'combo',
              mode: 'remote',
            editable: false,
            triggerAction: 'all',
            lastQuery: '',
                loadingText: 'Loading ...',
                store: new Ext.data.JsonStore({
                  url: '/attendanceReasons.json',
                  root: 'rows',
                  fields: [
                    {name: 'code', mapping: 'AttendanceReason.code'},
                    {name: 'description', mapping: 'AttendanceReason.description'}
                  ],
                  autoLoad: true
                }),
                displayField: 'description',
                valueField: 'code',
              fieldLabel: 'Attendance Reason',
              hiddenName: 'Attendance.attendance_reason_code',
              emptyText: 'Select',
                allowBlank:true,
                forceSelection: true,
                anchor: '95%'
            },{
          xtype:'xdatetime'
          ,name: 'Attendance.seen_at_time'
          ,fieldLabel:'Seen at Date'
          ,anchor:'95%'
          ,timeFormat:'H:i'
          ,timeConfig: {
            altFormats:'H:i:s'
            ,allowBlank:true
          }
          ,dateFormat:'d/m/Y'
          ,dateConfig: {
            altFormats:'Y-m-d|Y-n-d'
            ,allowBlank:true
          }
            }, {
              xtype: 'combo',
              store: new Ext.data.JsonStore({
                  url: '/users/combo.json',
                  root: 'rows',
                  fields: [
                    'id', 'name'
                  ],
                  autoLoad: true
              }),
              mode: 'remote',
            triggerAction: 'all',
                loadingText: 'Loading ...',
                displayField: 'name',
                valueField: 'id',
                lastQuery: '',
              fieldLabel: 'Seen By',
              hiddenName: 'Attendance.clinic_staff_id',
              emptyText: 'Select',
                forceSelection: true,
              anchor: '95%'
        }, {
              xtype: 'combo',
              mode: 'remote',
            editable: false,
            triggerAction: 'all',
            lastQuery: '',
                loadingText: 'Loading ...',
                store: new Ext.data.JsonStore({
                  url: '/attendanceResults.json',
                  root: 'rows',
                  fields: [
                    'code', 'description'
                  ],
                  autoLoad: true
                }),
                displayField: 'description',
                valueField: 'code',
              fieldLabel: 'Attendance Result',
              hiddenName: 'Attendance.attendance_result_code',
                forceSelection: true,
              anchor: '95%'
            },{
              xtype: 'treecombo',
                treeWidth: 255,
                fieldLabel: 'Diagnosis',
                name: 'Attendance.diagnosis_id',
                hiddenName: 'Attendance.diagnosis_id',
                readOnly: true,
              anchor: '95%',
              dataUrl:'/diagnoses/view.json',
              rootVisible:false,
              root: new Ext.tree.AsyncTreeNode({
                  text:'',
                  id: '0'
              })

            },{
          layout: 'column',
          xtype: 'container',
          border: false,
          hideBorders: true,
                style: 'padding-left: 105px;',
              anchor: '50px',
          items: [
            {
                      columnWidth:.46,
                      layout: 'form',
                      xtype: 'container',
                      defaults: {
                        hideLabel: true
                      },
                      items: [
                        {
                        xtype: 'checkbox',
                        boxLabel: 'Work Related',
                      labelSeparator: '',
                      inputValue: 'Y',
                        name: 'Attendance.work_related_absence',
                        listeners: {
                          check: function (c, checked) {
                            var f = form.getForm().findField('Attendance.accident_report_complete');
                            checked?f.enable():f.disable();
                          }
                        }
                      },{
                        xtype: 'checkbox',
                        boxLabel: 'Work Discomfort',
                      inputValue: 'Y',
                      labelSeparator: '',
                        name: 'Attendance.work_discomfort'
                      },{
                        xtype: 'hidden',
                        name: 'Attendance.private'
                      },{
                  xtype: 'checkbox',
                  boxLabel: 'Private',
                  inputValue: 'Y',
                  labelSeparator: '',
                  name: 'Attendance.no_work_contact',
                  listeners: {
                    check: function(c, checked) {
                      private = form.getForm().findField('Attendance.private').getValue();
                      if (checked && private != 1) {
                        Ext.MessageBox.confirm(
                                'Confirm action',
                                'Are you sure you want to make this attendance private? This action will also make any associated referral private and remove all the followers associated to the referral.',
                                function(btn) {
                                  if(btn == "yes") {
                                    form.fireEvent('make_private');
                                  } else {
                                    c.reset();
                                  }
                                }
                              );
                      }
                    }
                  }
                }
                      ]
            }, {
                      columnWidth:.54,
                      layout: 'form',
                      labelWidth: 1,
              items: [{
                        xtype: 'checkbox',
                        boxLabel: 'Review Attendance',
                      inputValue: 'Y',
                      labelSeparator: '',
                        name: 'Attendance.review_attendance'
                      },{
                        xtype: 'checkbox',
                        boxLabel: 'Accident Report Complete',
                      inputValue: 'Y',
                      labelSeparator: '',
                        name: 'Attendance.accident_report_complete',
                        disabled: true
                      }
              ]
            }
          ]
        }
        ]
    };

    var attendanceFormColumn2 = { // Column 2
        labelWidth: 55,
        flex: 0.8,
      items: [{
              xtype: 'combo',
              mode: 'remote',
            editable: false,
            triggerAction: 'all',
                loadingText: 'Loading ...',
                store: new Ext.data.JsonStore({
                  url: '/clinics.json',
                  root: 'rows',
                  fields: [
                    {name: 'id', mapping: 'Clinic.id'},
                    {name: 'clinic_name', mapping: 'Clinic.clinic_name'}
                  ],
                  autoLoad: true
                }),
                displayField: 'clinic_name',
                valueField: 'id',
                lastQuery: '',
                emptyText: 'Select Clinic',
            fieldLabel: 'Clinic',
              hiddenName: 'Attendance.clinic_id',
              anchor: '95%'
            },{
              xtype: 'textarea',
              fieldLabel: 'Comments',
          labelAlign: 'top',
              name: 'Attendance.comments',
              height: 80,
              anchor: '95%'
        }, {
          xtype: 'hidden',
          name: 'Attendance.recall_event_id'
        },
        pendingRecalls,{
          layout: 'column',
          border: false,
          hideBorders: true,
                style: 'padding-left: 60px;',
                items: [
                  feedbackButton,
            followupAppointmentButton
                ]
        }
        ]
    };

    var attendanceFormColumn3 = {
      flex: 0.5,
      defaults: {
        hideLabel: true,
        anchor: '-20px'
      },
      items: [{
        xtype: 'checkbox',
        name: 'Attendance.restrictions_applied',
        boxLabel: 'Restictions Applied',
        inputValue: 1
      },{
        xtype: 'checkbox',
        name: 'Attendance.is_discharged',
        inputValue: 1,
        lazyInit: false,
              lazyRender: true,
        boxLabel: 'Discharged',
        listeners: {
          check: function (cb, checked) {
            this.finalOutcomeCombo.setDisabled(!checked);
            if (checked) {
              //this.finalOutcomeCombo.focus();
              //this.finalOutcomeCombo.doQuery(this.finalOutcomeCombo.allQuery, true);
              //this.finalOutcomeCombo.expand();
            }
          },
          scope: this
        }
      },{
        ref: '../../finalOutcomeCombo',
        xtype: 'combo',
        hiddenName: 'Attendance.outcome_id',
        store: Ext.StoreMgr.get('AttendanceOutcomesStore'),
            mode: 'remote',
          editable: false,
          triggerAction: 'all',
          lastQuery: '',
        displayField: 'title',
        valueField: 'id',
        style: {marginLeft1: '17px'},
        emptyText: 'Final Outcome',
        disabled: true
      }]
    }

    this.dateFilter = {
      from: new Ext.form.DateField(),
      to: new Ext.form.DateField()
    };

    var config = {
      id: 'attendance-form',
        labelAlign: 'right', // label settings here cascade unless overridden
        url:'/attendances/save.json',
      items: [{
          xtype:'hidden',
          name: 'Attendance.id'
        },{
          xtype:'hidden',
          name: 'Attendance.employee_id'
        },{
          layout: 'hbox',
          xtype: 'container',
        hideBorders: true,
        xtype: 'container',
        defaults: {
          flex: 1,
          xtype: 'container',
          layout: 'form'
        },
          items: [
            attendanceFormColumn1,
            attendanceFormColumn2,
            attendanceFormColumn3
        ]
        }],
        
        tbar: [
        {
            text: 'Save',
            disabled: false,
            handler: this.onSave,
            scope: this,
              cls: 'x-btn-text-icon',
        iconCls: 'page_save'
        },{
            text: 'Save and Close',
            disabled: false,
            handler: this.onSaveAndClose,
            scope: this,
              cls: 'x-btn-text-icon',
        iconCls: 'page_save'
        }, '-',
        'From', this.dateFilter.from,
        'To', this.dateFilter.to, {
          text: 'Find',
          handler: function () {
            var filter = form.getForm().getValues();
            if (typeof this.dateFilter.from.getValue().format === 'function') {
              filter['Filter.from_date'] = this.dateFilter.from.getValue().format('Y-m-d');
            }
            if (typeof this.dateFilter.to.getValue().format === 'function') {
              filter['Filter.to_date'] = this.dateFilter.to.getValue().format('Y-m-d');
            }
            this.ownerCt.search(filter);
          },
          scope: this,
        cls: 'x-btn-text-icon',
        iconCls: 'page_find'
        },'-',{
            text: 'Reset',
            handler: function () {
              form.getForm().reset();
              form.fireEvent('reset');
              this.dateFilter.from.reset();
              this.dateFilter.to.reset();
            },
            scope: this,
        cls: 'x-btn-text-icon',
        iconCls: 'page_green'
        }]
    };

    Ext.apply(this, config);
    IOH.Attendances.AttendanceForm.superclass.initComponent.apply(this, arguments);

    form.getForm().on('beforesetvalues', function (values) {
      // Init the store of person combo in order to show proper person name in the combo's textbox
      Ext.getCmp('attendance-person').getStore().loadData({
        rows: [{
          Person: {
            id: values['Person.id'],
            full_name: values['Person.full_name']
          },
          Patient: {
            Organisation: {}
          },
          Employee: {}
        }]
      });

    }, this);

    form.on('reset', function (values) {
      Ext.getCmp("feedback-button").disable();
      Ext.getCmp("followup-appointment-button").disable();
    });

    this.subscribe('attendance.saved', function () {
      IOH.APP.feedback('Attendance Saved', 'Attendance Record has been saved.');
    });

    pendingRecalls.relayEvents(personCombo, ['valuechange']);
    this.relayEvents(pendingRecalls, ['pendingselchange']);

    this.on('pendingselchange', this._onPendingRecallsSelChange, this);
    this.getForm().on('actioncomplete', this._render, this);

    form.on('make_private', this._onMakePrivate, this);
  },

  _render: function() {
    private = this.getForm().findField('Attendance.private').getValue();
    no_work_contact = this.getForm().findField('Attendance.no_work_contact').getValue();
    if(private == 1 || no_work_contact) {
      this.getForm().findField('Attendance.no_work_contact').setValue(true);
      this.getForm().findField('Attendance.no_work_contact').disable();
    } else {
      this.getForm().findField('Attendance.no_work_contact').enable();
    }
  },

    loadAttendance: function (attendanceId) {
    this.load({
      url: '/attendances/load/' + attendanceId + '.json',
      scripts: false,
      waitMsg: 'Loading attendance record ...',
      success: function (form, action) {
        Ext.getCmp("followup-appointment-button").setDisabled(!action.result.data['Appointment.referral_id']);
      },
      scope: this
    });

    Ext.getCmp("feedback-button").enable();
    },

    _onMakePrivate: function() {
      var form = this.getForm();
    Server.Attendances.direct_make_private(
      { id: form.findField('Attendance.id').getValue() },
      function(result) {
        if (result.success) {
          IOH.APP.feedback("Success", result.message);
          form.findField('Attendance.no_work_contact').disable();
        } else {
           IOH.APP.feedback("Error", result.message);
           form.findField('Attendance.no_work_contact').reset();
        }
      },
      this
    );
  },

    _onPendingRecallsSelChange: function (id) {
      this.getForm().findField('Attendance.recall_event_id').setValue(id);
    },

    onSave: function () {
      this._save(false);
    },

    onSaveAndClose: function () {
      this._save(true);
    },

    _save: function (bResetAfter) {
      var form = this.getForm();
      var isUpdate = form.findField('Attendance.id').getValue();
    form.waitMsgTarget = 'attendance-form';
      form.submit({
        url: '/attendances/save.json',
        success: function (form, action) {
          this.publish('attendance.saved');
          if (bResetAfter) {
                form.reset();
          } else {
            this.loadAttendance(action.result.id);
          }
        },
        scope: this,
      waitMsg: (isUpdate?'Updating ...':'Saving ...')
      });
    }
});
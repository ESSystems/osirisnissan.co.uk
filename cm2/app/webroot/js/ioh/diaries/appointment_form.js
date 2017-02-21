/**
 *
 */

(function () {
  var parent = {
    onRender: Ext.ensible.cal.EventEditWindow.prototype.onRender,
    initComponent: Ext.ensible.cal.EventEditWindow.prototype.initComponent
  };

  var commonReferralFields = [{
      name: 'id', mapping: 'Referral.id', type: 'int'
    },{
      name: 'Referral.patient_status_id', type: 'int'
    },{
      name: 'case_nature', mapping: 'Referral.case_nature'
    },{
      name: 'job_information', mapping: 'Referral.job_information'
    },{
      name: 'history', mapping: 'Referral.history'
    },{
      name: 'created_at', mapping: 'Referral.created_at', type: 'date', dateFormat: 'Y-m-d H:i:s'
    },{
      name: 'updated_at', mapping: 'Referral.updated_at', type: 'date', dateFormat: 'Y-m-d H:i:s'
    },{
      name: 'state', mapping: 'Referral.state'
    },{
      name: 'person_id', mapping: 'Person.id'
    },{
      name: 'Referral.referral_reason_id'
    },{
      name: 'case_reference_number', mapping: 'Referral.case_reference_number'
    },{
      name: 'Referral.sickness_started', type: 'date', dateFormat: 'Y-m-d'
    },{
      name: 'Referral.sicknote_expires', type: 'date', dateFormat: 'Y-m-d'
    },{
      name: 'Referral.operational_property_id', type: 'int'
  }];

  var existingRecordFields = commonReferralFields.concat([{
      name: 'patient_status', mapping: 'Referral.PatientStatus.status'
    },{
      name: 'referral_reason', mapping: 'Referral.ReferralReason.reason'
    },{
      name: 'operational_priority', mapping: 'Referral.OperationalPriority.operational_priority'
    },{
      name: 'referrer_full_name', mapping: 'Referral.Referrer', convert: function (v) { return v.Person !== undefined ? v.Person.full_name : ''; }
    },{
      name: 'referrer_email', mapping: 'Referral.Referrer.email'
    },{
      name: 'referrer_organisation_name', mapping: 'Referral.Referrer.Organisation.OrganisationName'
    },{
      name: 'referrer_type_id', mapping: 'Referral.Referrer.referrer_type_id'
    },{
      name: 'referrer_type', mapping: 'Referral.Referrer.ReferrerType.type'
  }]);

  var newRecordFields = commonReferralFields.concat([{
      name: 'patient_status', mapping: 'PatientStatus.status'
    },{
      name: 'referral_reason', mapping: 'ReferralReason.reason'
    },{
      name: 'operational_priority', mapping: 'OperationalPriority.operational_priority'
    },{
      name: 'referrer_full_name', mapping: 'Referrer.Person', convert: function (v) { if (v) { v = v.full_name; } return v; }
    },{
      name: 'referrer_email', mapping: 'Referrer.email'
    },{
      name: 'referrer_organisation_name', mapping: 'Referrer.Organisation', convert: function (v) { if (v) { v = v.OrganisationName; } return v; }
    },{
      name: 'referrer_type_id', mapping: 'Referrer.referrer_type_id'
    },{
      name: 'referrer_type', mapping: 'Referrer.ReferrerType', convert: function (v) { if (v) { v = v.type; } return v; }
  }]);

  var caseReferenceNumberStore = new Ext.data.DirectStore({
    directFn: Server.Referrals.direct_index,
        root: 'data',
        baseParams: {
      'get_referrers': true
    },
        fields: newRecordFields
    });

  var resultTpl = new Ext.XTemplate(
        '<tpl for="."><div class="search-item x-combo-list-item">',
            '{[values.Person.full_name]}',
          '<tpl if="Organisation.OrganisationName"> ({[values.Organisation.OrganisationName]}',
              '<tpl if="Employee.salary_number">, {[values.Employee.salary_number]}</tpl>)',
            '</tpl>',
        '</div></tpl>'
    );

    var referralTpl = new Ext.XTemplate(
    '<tpl for="."><div class="search-item">',
          '<h3><span>{created_at:date("j M, Y")}</span>Ref. No. {case_reference_number}</h3>',
          '<b>Status</b>: {patient_status}<br />',
          '<b>Reason</b>: {referral_reason}<br />',
          '<b>Operational Priority</b>: {operational_priority}<br />',
          '<b>State</b>: {state}',
        '</div></tpl>'
    );

    var referralDetailsTpl = new Ext.XTemplate(
      '<tpl for="."><div class="search-item">',
          '<h3><span>{created_at:date("j M, Y")}',
            '<br />by<br /><i>',
            '{referrer_full_name}<br />',
            '{referrer_type}<br/>',
            '{referrer_email}<br />',
            '{referrer_organisation_name}',
            '</i>',
          '</span>Reason: {referral_reason}</h3>',
          '<b>Status</b>: {patient_status}<br />',
          '<b>Case nature</b>: {case_nature}<br />',
          '<b>Job information</b>: {job_information}<br />',
          '<b>History</b>: {history}<br />',
          '<b>Operational Priority</b>: {operational_priority}<br />',
          '<b>State</b>: {state}',
        '</div></tpl>'
    );

  Ext.ensible.cal.EventEditWindow.prototype.calendarLabelText = 'Diary';

  Ext.override(Ext.ensible.cal.EventEditWindow, {
    titleTextAdd: 'Add Appointment',
    titleTextEdit: 'Edit Appointment',

    initComponent: function () {
      parent.initComponent.apply(this, arguments);
    },

    onRender: function () {
      this.calendarStore = Ext.StoreMgr.get('RegularDiaries');

      parent.onRender.apply(this, arguments);

      this.titleField.hide();

      var form = this.formPanel, self = this;

      (function () {
        var f = form.form, loadRecord = f.loadRecord, M = Ext.ensible.cal.EventMappings;

        f.loadRecord = function (rec, flag) {
          if (rec.phantom) {
            if (!flag) {
              // This is NOT edit operation, check if there are accepted referrals
              Server.Referrals.direct_peek_accepted(null, function (response) {
                if (!response.success) {
                  // No accepted referral
                  return;
                }

                Ext.iterate(M, function (i, f) {
                  if (response.data[f.mapping]) {
                    rec.data[i] = response.data[f.mapping];
                  }
                });
                rec.data[M.EndDate.name] =
                  rec.data[M.StartDate.name].add(Date.MINUTE, response.data['Appointment.length']);

                f.loadRecord(rec, true);

                if(response.referrals !== false) {
                  self.loadReferralStoreData(newRecordFields, response.referrals);
                  self.activeRecord.new_json = response.referrals;
                }

              }, this);
            }

            // Set the selected diary id
            rec.data[M.CalendarId.name] = Ext.StoreMgr.get('appointments').baseParams.diary_id;
          }

          f.findField('PersonId').getStore().loadData({
            rows: [{
              Person: {
                id: rec.data['PersonId'],
                full_name: rec.data['PersonName']
              },
              Patient: {
                Organisation: {}
              },
              Employee: {}
            }]
          });

          loadRecord.call(f, rec);
          self.dateRangeField.setValue(rec.data);

          self.resetCase();

          if(rec.json) {
            if(rec.json.Referral.id !== null) {
              self.loadReferralStoreData(existingRecordFields, rec.json);
            }
          } else if(rec.new_json) {
            self.loadReferralStoreData(newRecordFields, rec.new_json);
          }
        };
      })();

      var personCombo = new IOH.PersonCombo({
        fieldLabel: 'Person',
        hiddenName: Ext.ensible.cal.EventMappings.PersonId.name,
        anchor: '0px',
        onTriggerClick: function () {
          var combo = this;

          IOH.APP.showPeopleWindow({
            personId: form.getForm().findField('PersonId').getValue(),
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
                self.loadReferral(fst.get('id'));
              }
            }
          });
        },
        listeners: {
          'select': function(combo, record) {
            if(record.data.Person.id) {
              self.loadReferral(record.data.Person.id);
            }
          }
        }
      });

      var caseReferenceNumberCombo = new Ext.form.ComboBox({
        name: Ext.ensible.cal.EventMappings.ReferralId.name,
        fieldLabel: 'Case reference number',
        id: 'case-reference-number',
        xtype: 'combo',
        mode: 'remote',
        forceSelection: true,
        editable: true,
        triggerAction: 'all',
        loadingText: 'Loading ...',
        store: caseReferenceNumberStore,
        displayField: 'case_reference_number',
        valueField: 'id',
        hiddenName: Ext.ensible.cal.EventMappings.ReferralId.name,
        pageSize: 10,
        tpl: referralTpl,
        itemSelector: 'div.search-item',
        emptyText: 'No referral selected for the current person',
        allowBlank: true,
        anchor: '-10px',
        listeners: {
          'select': function(combo, record) {
            self.checkIfNewOrReview();
            self.loadReferralDetails(record.data);
          }
        }
      });

      this.formPanel.getForm().findField('CalendarId').addListener('select', function(combo, record) {
        self.checkIfNewOrReview();
      });

      this.formPanel.insert(0,
        {
          xtype: 'radiogroup',
          name: Ext.ensible.cal.EventMappings.NewOrReview.name,
          fieldLabel: 'Is',
          id: 'is-new-or-review',
          items: [
            {
              xtype: 'radio',
              name: Ext.ensible.cal.EventMappings.NewOrReview.name,
              inputValue: 'new',
              checked: true,
              boxLabel: 'New'
            },{
              xtype: 'radio',
              name: Ext.ensible.cal.EventMappings.NewOrReview.name,
              inputValue: 'review',
              boxLabel: 'Review'
            }
          ]
        }, personCombo
       );

      this.formPanel.add(
        {
          xtype: 'compositefield',
          labelWidth: 130,
          fieldLabel: 'Referred by',
          id: 'referred-by-container',
          items: [
            {
              hiddenName: Ext.ensible.cal.EventMappings.ReferredByType.name,
              id: 'referred-by-type',
              xtype: 'combo',
              mode: 'local',
              triggerAction: 'all',
              store: IOH.APP.referrerTypesStore,
              displayField: 'type',
              valueField: 'id',
              forceSelection: true,
              allowBlank: false,
              flex: 1
            },{
              xtype: 'textfield',
              name: Ext.ensible.cal.EventMappings.ReferredByName.name,
              id: 'referred-by-name',
              allowBlank: false,
              flex: 1
            },{
              xtype: 'button',
              text: 'Add Referral',
              id: 'add-referral-button',
              handler: function () {
                var f = this.formPanel.getForm(),
                  referrerTypeId = f.findField(Ext.ensible.cal.EventMappings.ReferredByType.name).getValue(),
                  referrerName   = f.findField(Ext.ensible.cal.EventMappings.ReferredByName.name).getValue(),
                  personId       = f.findField(Ext.ensible.cal.EventMappings.PersonId.name).getValue(),
                  personName     = f.findField(Ext.ensible.cal.EventMappings.PersonId.name).getRawValue();

                if (!referrerName || !referrerTypeId) {
                  alert('Please enter referrer information first.');
                  return;
                }

                IOH.Appointments.Referral.Window.show({
                  referrerTypeId: referrerTypeId,
                  referrerName: referrerName,
                  Person: {
                    id: personId,
                    name: personName
                  },
                  callback: function (referralData) {
                    var params = {
                      Appointment: {
                        referral_id: referralData['Referral.id'],
                        person_id: referralData['Referral.person_id'],
                        case_nature: referralData['Referral.case_nature'],
                        referral_reason_id: referralData['Referral.referral_reason_id'],
                        case_reference_number: referralData['Referral.case_reference_number'],
                        referrer_type_id: referrerTypeId,
                        referrer_name: referrerName,
                        length: 30 // Temporary solution,
                      }
                    };

                    Server.Referrals.direct_accept_a({
                      data: params
                    }, function () {
                      this.formPanel.getForm().loadRecord(this.activeRecord);
                    }, this);
                  },
                  scope: this
                });
              },
              scope: this
            }
          ],
          listeners: {
            // We need to enable / disable button in composite field manually -
            // ExtJS simply doesn't do this.

            disable: function (comp) {
              Ext.getCmp('add-referral-button').disable();
            },
            enable: function (comp) {
              Ext.getCmp('add-referral-button').enable();
            }
          }
        },{
          xtype: 'checkbox',
          name: Ext.ensible.cal.EventMappings.BlockedAppointment.name,
          boxLabel: 'Block Appointment',
          inputValue: 1
        },{
          xtype: 'fieldset',
          title: 'Case',
          disabled: true,
          id: 'case-reference-number-fieldset',
          style: 'padding:5px',
          items: [{
            xtype: 'container',
            layout: 'hbox',
            items:[{
              xtype: 'container',
              layout: 'form',
              labelWidth: 130,
              labelAlign: 'right',
              items: caseReferenceNumberCombo,
              flex: 1
            },{
              xtype: 'container',
              layout: 'form',
              items: {
                xtype:'button',
                  text:'Clear case',
                  listeners: {
                    'click' : function() {
                      Ext.getCmp('is-new-or-review').setValue('new');
                      self.clearValue();
                    }
                  }
              }
            }]
          },{
            xtype:'panel',
            id: 'referral-details',
            border: false,
            height: 150
          }]
        },{
          xtype: 'textarea',
          anchor: '100%',
          fieldLabel: 'Notes',
          name: Ext.ensible.cal.EventMappings.Notes.name
        });
    },

    loadReferralStoreData: function(recordFields, data) {
      var self = this;

      var store = new Ext.data.JsonStore({
          listeners: {
            load: function(store, result) {
              self.loadNewReferral(result[0].data);
            }
          },
              fields: recordFields
          });

        store.loadData([data]);
    },

    loadNewReferral: function(data) {
      caseReferenceNumberStore.setBaseParam('person_id', data.person_id);
      this.loadReferralDetails(data);
      Ext.getCmp('case-reference-number').setRawValue(data.case_reference_number);
    },

    loadReferral: function(id) {
      var self = this;

      caseReferenceNumberStore.setBaseParam('person_id', id);
      caseReferenceNumberStore.load({
        params: {person_id: id},
        callback: function(r, options, success) {
          if(success && r.length > 0) {
            self.loadReferralDetails(r[0].data);
          } else {
            self.clearValue();
            Ext.getCmp('case-reference-number-fieldset').disable();
          }
        }
      });
    },

    loadReferralDetails: function(data) {
      Ext.getCmp('case-reference-number-fieldset').enable();
      Ext.getCmp('case-reference-number').setValue(data.id);
      Ext.getCmp('referred-by-container').disable();
      Ext.getCmp('referred-by-type').setValue(data.referrer_type_id);
      Ext.getCmp('referred-by-name').setValue(data.referrer_full_name);
      referralDetailsTpl.overwrite(Ext.getCmp('referral-details').body,data);
      if(this.activeRecord != undefined && this.activeRecord.phantom) {
        this.checkIfNewOrReview();
      }
    },

    clearValue: function(){
      Ext.getCmp('case-reference-number').clearValue();
      Ext.getCmp('referral-details').update('');
      Ext.getCmp('referred-by-container').enable();
    },

    checkIfNewOrReview: function() {
      appointment_id = this.activeRecord !== undefined && !this.activeRecord.phantom ? this.activeRecord.id : '';
      person_id = this.formPanel.getForm().findField('PersonId').getValue();
      diary_id = this.formPanel.getForm().findField('CalendarId').getValue();
      referral_id = Ext.getCmp('case-reference-number').getValue();

      if(person_id !== '' && diary_id !== '' && referral_id !=='') {
        Server.Appointments.direct_is_new_or_review({
          data: {
            Appointment: {
              id: appointment_id,
              person_id: person_id,
              diary_id: diary_id,
              referral_id: referral_id
            }
          }
        }, function (response) {
          if(response != undefined && response.success === true) {
            Ext.getCmp('is-new-or-review').setValue(response.result);
          }
        }, this);
      }
    },

    resetCase: function() {
      caseReferenceNumberStore.setBaseParam('person_id', '');
      this.clearValue();
      Ext.getCmp('case-reference-number-fieldset').disable();
      Ext.getCmp('referred-by-container').enable();
    }
  });
})();

IOH.Absences.AbsenceForm = Ext.extend(Ext.form.FormPanel,
{
  initComponent: function () {
    var form = this;
    var sicknotesGrid = new IOH.Absences.SicknotesGrid({
      title: 'Sick Notes'
    });
    
    this.sicknotesGrid = sicknotesGrid;
    
    var config = {
      xtype: 'form',
      id: 'add-absences-form',
      labelAlign: 'right',
      labelWidth: 80,
      defaults: {
        border: false,
        style: {padding: '10px'}
      },
      autoScroll: true,
      items: [
        {
          xtype: 'hidden',
          name: 'Absence.id'
        },{
          xtype: 'hidden',
          name: 'Absence.employee_id'
        },{
          xtype: 'hidden',
          name: 'Person.id'
        }, {
          layout: 'column',
          hideBorders: true,
          items: [
            {
              width: 400,
              layout: 'form',
              hideBorders: true,
              style: 'padding-right: 2em;',
              defaults: {
                anchor: '100%'
              },
              items: [new IOH.PersonCombo({
                        fieldLabel: 'Employee',
                        name: 'Person.full_name',
                        // hiddenName: 'Person.id',
                        readOnly1: true,
                        onTriggerClick: function () {
                          IOH.APP.showPeopleWindow({
                          targetForm: form,
                          personId: form.getForm().findField('Person.id').getValue()
                        });
                        }
                    }), {
                layout: 'column',
                hideBorders: true,
                items: [{
                  columnWidth: 0.5,
                  layout: 'form',
                  defaults: {
                    anchor: '100%'
                  },
                  items: [{
                    xtype: 'datefield',
                    fieldLabel: 'From date',
                    name: 'Absence.start_date',
                    listeners: {
                      change: {
                        fn: this.updateSickDays,
                        scope: this
                      }
                    }
                  }, {
                    xtype: 'textfield',
                    fieldLabel: 'Sick Days',
                    name: 'Absence.sick_days'
                  }]
                }, {
                  columnWidth: 0.5,
                  layout: 'form',
                  defaults: {
                    anchor: '100%'
                  },
                  items: [{
                    xtype: 'datefield',
                    fieldLabel: 'To date',
                    name: 'Absence.end_date',
                    listeners: {
                      change: {
                        fn: this.updateSickDays,
                        scope: this
                      }
                    }
                  }, {
                    xtype: 'textfield',
                    fieldLabel: 'Calc',
                    name: 'Absence.calc_sick_days',
                    readOnly: true
                  }]
                }]
              }, {
                xtype: 'datefield',
                fieldLabel: 'Returned',
                name: 'Absence.returned_to_work_date'
              }, {
                xtype: 'hidden',
                name: 'Absence.main_diagnosis_code'
              },{
                xtype: 'combo',
                width: 265,
                name: 'MainDiagnosis.description',
                fieldLabel: 'Main Diagnosis',
                mode: 'remote',
                triggerAction: 'all',
                editable: false,
                store: new Ext.data.JsonStore({
                  url: '/absences/mainDiagnosis.json',
                  method: 'post',
                          root: 'rows',
                          fields: [
                            'id', 'description'
                          ]
                }),
                queryParam: 'Absence.id',
                displayField: 'description',
                valueField: 'id',
                listeners: {
                  beforequery: function (q) {
                    this.lastQuery = '';
                    q.query = form.getForm().findField('Absence.id').getValue();
                  },
                  beforeselect: function (combo, record) {
                    form.getForm().findField('Absence.main_diagnosis_code').setValue(record.data.id);
                  }
                }
              },{
                layout: 'column',
                hideBorders: true,
                items: [{
                  layout: 'form',
                  columnWidth: 0.7,
                  items: [{
                          xtype: 'checkbox',
                          boxLabel: 'Work Related',
                        labelSeparator: '',
                          name: 'Absence.work_related_absence',
                          inputValue: '1'
                        },{
                          xtype: 'checkbox',
                          boxLabel: 'Accident Report Complete',
                        labelSeparator: '',
                          name: 'Absence.accident_report_completed',
                          inputValue: '1'
                        }]
                }, {
                  layout: 'form',
                  columnWidth: 0.3,
                  items: [{
                          xtype: 'checkbox',
                          hideLabel: true,
                          boxLabel: 'Work Discomfort',
                        labelSeparator: '',
                          name: 'Absence.discomfort_report_completed',
                          inputValue: '1'
                        },{
                          xtype: 'checkbox',
                          hideLabel: true,
                          boxLabel: 'Neither',
                        labelSeparator: '',
                          name: 'Absence.tickbox_neither',
                          inputValue: '1'
                        }]
                }]
              }]
            }, 
            sicknotesGrid
          ]
        }], 
      tbar: [{
        id: 'save-absence-button',
        text: 'Save',
        disabled: true,
              handler: function () {
                var isUpdate = form.getForm().findField('Absence.id').getValue();
          form.getForm().waitMsgTarget = 'add-absences-form';
                form.getForm().submit({
                  url: '/absences/save.json',
                  success: function () {
                    if (!isUpdate) {
                      form.getForm().reset();
                    }
                    this.publish('absencesaved');
                  },
                    scope: this,
            waitMsg: (isUpdate?'Updating ...':'Saving ...')
                });
              },
              scope: this,
              cls: 'x-btn-text-icon',
        iconCls: 'page_save'
      },{
        text: 'Find',
        cls: 'x-btn-text-icon',
        iconCls: 'page_find',
            handler: function () {
          form.ownerCt.search();
        }
      }, {
        text: 'Reset',
        cls: 'x-btn-text-icon',
        iconCls: 'page_green',
              handler: function () {
          this.ownerCt.reset();
              },
              scope: this
      },{
            text: 'Merge',
        cls: 'x-btn-text-icon',
        iconCls: 'arrow_join',
        disabled: false,
            handler: function () {
          this.ownerCt.merge();
            },
            scope: this
          }],
      
          loadAbsence: function (absenceId, silent) {
            if (!silent) {
          form.getForm().waitMsgTarget = form.getEl();
            }
        form.getForm().load({
          url: '/absences/load/' + absenceId + '.json',
          scripts: false,
          waitMsg: 'Loading absence record ...',
          success: function () {
                Ext.getCmp('save-absence-button').setDisabled(false);
          }
        });
        this.loadSicknotes(absenceId); 
         },
         
      reload: function () {
        var id = form.getForm().findField('Absence.id').getValue();
        if (id) {
          this.loadAbsence(id);
        }
      },
          
          loadSicknotes: function (absenceId) {
        sicknotesGrid.getStore().load({
            params: {
            'Sicknote.absence_id' : absenceId
          },
          callback: function () {
            Ext.getCmp('sicknote-add-button').setDisabled(false);
          }
          });
          }
    };
    
    Ext.apply(this, config);
    IOH.Absences.AbsenceForm.superclass.initComponent.apply(this, arguments);
    
    this.subscribe('sicknotesaved', function () {
      this.loadAbsence.apply(this, arguments);
    }, this);

    this.subscribe('sicknotedeleted', function () {
      this.loadAbsence.apply(this, arguments);
    }, this);
  },
    
  updateSickDays: function () {
    var form      = this.getForm();
    var startDate = form.findField('Absence.start_date').getValue();
    var endDate   = form.findField('Absence.end_date').getValue();
    
    if (!startDate || !endDate) {
      return;
    }
    
    var sickDays = daysBetween(startDate, endDate) + 1;
    
    form.findField('Absence.sick_days').setValue(sickDays);
    form.findField('Absence.calc_sick_days').setValue(sickDays);
  },
  
  reset: function () {
      this.getForm().reset();
      this.sicknotesGrid.getStore().removeAll();
  }

});
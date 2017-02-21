/**
 *
 */
Ext.ns('IOH.referrers');

IOH.referrers.form = Ext.extend(Ext.form.FormPanel,
{
  bodyStyle: 'padding: 10px',
  labelAlign: 'right',

  initComponent: function() {
    var referrerForm = form = this;

    var resultTpl = new Ext.XTemplate(
              '<tpl for="."><div class="search-item x-combo-list-item">',
                  '{[values.Person.full_name]}',
                '<tpl if="Organisation.OrganisationName"> ({[values.Organisation.OrganisationName]}',
                    '<tpl if="Employee.salary_number">, {[values.Employee.salary_number]}</tpl>)',
                  '</tpl>',
              '</div></tpl>'
          );

    var personCombo = new Ext.form.ComboBox({
            xtype:'combo',
            triggerClass: 'x-form-search-trigger',
          mode: 'remote',
          width: 150,
        editable: true,
        forceSelection: true,
        triggerAction: 'all',
            fieldLabel: 'Person',
            name: 'Person.full_name',
            hiddenName: 'Person.id',
            anchor: '100%',
            store: new Ext.data.JsonStore({
          url: '/persons/lookup.json',
          totalProperty: 'totalRows',
          successProperty: 'success',
          root: 'rows',
          id: 'Person.id',
          fields: [
              'Person', 'Employee', 'Patient',
              {name: 'Organisation', mapping: 'Patient.Organisation'},
              {name: 'full_name', mapping: 'Person.full_name'},
              {name: 'id', mapping: 'Person.id'},
              {name: 'email_address', mapping: 'Person.email_address'},
              {name: 'employee_client_id', mapping: 'Employee.client_id'},
              {name: 'patient_client_id', mapping: 'Patient.ResponsibleOrganisationID'}
          ]
        }),
        pageSize: 10,
        tpl: resultTpl,
        itemSelector: 'div.search-item',
        emptyText: 'Select Person',
        displayField: 'full_name',
        valueField: 'id',
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
                  if (fst) {
                    if(fst.get('id')) {
                      combo.setValue(fst.get('id'));
                    }
                    var emailField = form.getForm().findField('Referrer.email');
                    if(emailField.getValue() == '' && fst.get('email_address')) {
                      emailField.setValue(fst.get('email_address'));
                    }
                    if(fst.get('employee_client_id') && fst.get('employee_client_id') != "") {
                      form.getForm().findField('Referrer.client_id').setValue(fst.get('employee_client_id'));
                    } else if(fst.get('patient_client_id') && fst.get('patient_client_id') != "") {
                      form.getForm().findField('Referrer.client_id').setValue(fst.get('patient_client_id'));
                    }
                  }
                },
                personId: form.getForm().findField('Person.id').getValue()
              });
            }
        });

    var cfg = {
      items: [
        {
          xtype: 'hidden',
          name: 'Referrer.id'
        },/*{
          xtype: 'hidden',
          name: 'Referrer.client_id'
        },*/{
          layout: 'column',
          hideBorders: true,
          xtype: 'container',
            items: [
              { // Column 1
                width: 400,
                style: 'margin-right: 20px;',
                layout: 'form',
                xtype: 'container',
                items: [
                  personCombo,
                  {
                    name: 'Referrer.client_id',
                    fieldLabel: 'Organisation',
                    xtype: 'combo',
                    mode: 'remote',
                    editable: false,
                    forceSelection: true,
                    triggerAction: 'all',
                    loadingText: 'Loading ...',
                    store: new Ext.data.JsonStore({
                      url: '/organisations.json',
                      root: 'rows',
                      fields: [
                        'id', 'name'
                      ],
                      autoLoad: true
                    }),
                    width: 275,
                    displayField: 'name',
                    valueField: 'id',
                    hiddenName: 'Referrer.client_id',
                    allowBlank:false
                  },{
                    xtype: 'textfield',
                    name: 'Referrer.email',
                    width: 275,
                    fieldLabel: 'Email'
                  },{
                    hiddenName: 'Referrer.referrer_type_id',
                    // fieldLabel: 'Type',
                    xtype: 'combo',
                    mode: 'local',
                    triggerAction: 'all',
                    store: IOH.APP.referrerTypesStore,
                    displayField: 'type',
                    valueField: 'id',
                    forceSelection: true,
                    allowBlank: false,
                    fieldLabel: 'Referrer type'
                  },{
                    xtype: 'combo',
                    mode: 'local',
                    triggerAction: 'all',
                    hiddenName: 'Referrer.track_referrals',
                    value: 'initiated_and_assigned',
                    store: {
                      xtype: 'arraystore',
                      fields: ['type', 'label'],
                      data: [['all', 'All'],['initiated_and_assigned', 'Initiated and Assigned Only']]
                    },
                    valueField: 'type',
                    displayField: 'label',
                    editable: false,
                    forceSelection: true,
                    fieldLabel: 'Referrals to follow',
                    width: 180
                  },{
                    xtype: 'checkbox',
                    name: 'Referrer.read_only_access',
                    boxLabel: 'Read only access',
                    inputValue: 1
                  }
                ]
              },{ // Column 2
                layout: 'form',
                xtype: 'container',
                items: [
                  {
                    xtype: 'fieldset',
                    title: 'Login details',
                    collapsible: true,
                    collapsed: false,
                    width: 300,
                    items: [
                      {
                        xtype: 'textfield',
                        name: 'Referrer.username',
                        fieldLabel: 'Username'
                      },{
                        xtype: 'textfield',
                        inputType: 'password',
                        name: 'Referrer.password',
                        fieldLabel: 'Password'
                      },{
                        xtype: 'textfield',
                        inputType: 'password',
                        name: 'Referrer.password_repeat',
                        fieldLabel: 'Repeat Password'
                      }
                    ]
                  }
                ]
              }
            ]
          }
      ],
      tbar: [
              {
                  text: 'Save',
                  handler: function () {
                    referrerForm.getForm().submit({
                      url: '/admin/referrers/save.json',
                      success: function (form, action) {
                        this.ownerCt.referrersGrid.getStore().reload({
                          callback: function () {
                            referrerForm.loadReferrer(action.result.referrer_id);
                          }
                        });
                      },
                      scope: referrerForm
                    });
                  },
                  cls: 'x-btn-text-icon',
            iconCls: 'page_save'
              },{
                id: 'delete-referrer-button',
            text: 'Delete',
            disabled: true,
            handler: function () {
              Ext.Msg.confirm('Delete referrer', 'Are you sure you want to delete this referrer?', function(btn){
                  if (btn == 'yes'){
                    Ext.Ajax.request({
                    url: '/admin/referrers/delete.json',
                    params: {
                      id: form.getForm().findField('Referrer.id').getValue()
                    },
                    success: function (response) {
                      var json = Ext.util.JSON.decode(response.responseText);

                      if (json.success = true) {
                        IOH.APP.feedback('Success', 'Referrer deleted');
                        referrerForm.ownerCt.referrersGrid.getStore().reload({
                                callback: function () {
                                  referrerForm.resetForm();
                                }
                              });
                      }
                    }
                  });
                  }
              });
                  },
            scope: this,
            cls: 'x-btn-text-icon',
            iconCls: 'page_delete'
          },{
                  text: 'Reset / New',
                  handler: function () {
                    referrerForm.resetForm();
                  },
            cls: 'x-btn-text-icon',
            iconCls: 'page_green'
              },{
                text: 'Find',
                handler: function () {
                  this.ownerCt.referrersGrid.reload(referrerForm.getForm().getValues());
                },
                scope: this,
            cls: 'x-btn-text-icon',
            iconCls: 'page_find'
              }
      ],
      labelAlign: 'right', // label settings here cascade unless overridden
      labelWidth: 120,
      loadReferrer: function (id) {
        this.load({
          url: String.format('/admin/referrers/load/{0}.json', id),
          scripts: false,
          text: 'Loading ...',
          success: function () {
            referrerForm.setTitle('Referrer Form [Edit Mode]');
            Ext.getCmp('delete-referrer-button').setDisabled(false);
          }
        });
      }
    };

    Ext.apply(this, cfg);
    Ext.apply(this.initialConfig, cfg);

    IOH.referrers.form.superclass.initComponent.apply(this, arguments);

    form.getForm().on('beforesetvalues', function (values) {
      // Init the store of person combo in order to show proper person name in the combo's textbox
      this.findField('Person.id').getStore().loadData({
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

    });
  },

  resetForm: function() {
    this.getForm().reset();
    this.setTitle('User Form [Search Mode]');
    Ext.getCmp('delete-referrer-button').setDisabled(true);
    this.ownerCt.referrersGrid.reload({});
  }
});

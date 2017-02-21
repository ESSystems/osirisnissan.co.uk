IOH.UsersForm = Ext.extend(Ext.form.FormPanel,
{

  initComponent: function () {
    var userForm = form = this;

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
              {name: 'id', mapping: 'Person.id'}
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
                  if (fst && fst.get('id')) {
                    combo.setValue(fst.get('id'));
                  }
                },
                personId: form.getForm().findField('Person.id').getValue()
              });
            }
        });

    var firstColumn = {
      layout: 'form',
      labelWidth: 100,
      minWidth: 350,
      width: 350,
      style: 'margin-right: 10px;',
      items: [
        {
              xtype:'hidden',
              name: 'User.id'
            }, personCombo, /*{
              xtype:'hidden',
              name: 'Person.id'
            }, {
                  xtype:'trigger',
                  triggerClass: 'x-form-search-trigger',
                  fieldLabel: 'Person',
                  name: 'Person.full_name',
                  allowBlank:false,
                  readOnly: true,
                  onTriggerClick: function () {
                    IOH.showPeopleWindow({
                    targetForm: userForm,
                    personId: userForm.getForm().findField('Person.id').getValue()
                  });
                  },
                  width: 245
        },*/ /*{
                  xtype:'textfield',
                  fieldLabel: 'Diary?',
                  name: 'User.diary_id'
        }, {
                  xtype:'textfield',
                  fieldLabel: 'Clinic Department',
                  name: 'User.clinic_department_id'
        },*/{
          xtype: 'combo',
          fieldLabel: 'Status',
          hiddenName: 'User.sec_status_code',
            mode: 'remote',
            triggerAction: 'all',
            editable: false,
          store: new Ext.data.JsonStore({
            url: '/SecurityStatuses/index.json',
            root: 'rows',
            idProperty: 'Status.status_code',
            fields: [{
              name:'status_code', mapping: 'Status.status_code'
            }, {
              name:'status_description', mapping: 'Status.status_description'
            }],
            autoLoad: true
          }),
          displayField: 'status_description',
          valueField: 'status_code',
          width: 245
        },{
          xtype: 'fieldset',
          title: 'Password',
          labelWidth: 90,
          collapsible: true,
                collapsed: false,
                defaults: {
            width: 225
          },
          items: [{
            xtype: 'textfield',
            fieldLabel: 'Password',
            inputType: 'password',
            readonly: true,
            name: 'User.sec_password'
          },
          {
            xtype: 'textfield',
            fieldLabel: 'Repeat It',
            inputType: 'password',
            readonly: true,
            name: 'User.password_again'
          }]
        }
      ]
    };

    var userGroupsStore = new Ext.data.JsonStore ({
      url: '/admin/users/groups.json',
      totalProperty: 'totalRows',
      successProperty: 'success',
      root: 'rows',
      id: 'id',
      fields: [
        'group_name'
      ],
      remoteSort: true
    });

    var groupsDelButton = new Ext.Button({
        text: 'Delete',
        handler: function () {
          var userId  = userForm.getForm().findField('User.id').getValue();
          var group = groupsGrid.getSelectionModel().getSelected();

          if (!userId || !group) {
            return;
          }

          Ext.Ajax.request({
          url: '/admin/users/delGroup',
          success: function () {
            userGroupsStore.reload();
          },
          failure: function () {
            alert('Delete failed');
          },
          params: { 'Ug.user_id': userId, 'Ug.group_id': group.id }
        });
        },
        scope: this
      });

      var groupsCombo = new Ext.form.ComboBox({
        mode: 'remote',
        editable: false,
        triggerAction: 'all',
      loadingText: 'Loading ...',
          store: new Ext.data.JsonStore({
            url: '/groups.json',
            root: 'rows',
            fields: [
              'id', 'group_name'
            ]
          }),
          displayField: 'group_name',
          valueField: 'id',
      fieldLabel: 'New Group',
          hiddenName: 'User.group_id',
          width: 120
      });

    var groupsBBar = new Ext.Toolbar([
          groupsCombo,
          {
            text: 'Add',
            handler: function () {
              var userId  = userForm.getForm().findField('User.id').getValue();
              var groupId = groupsCombo.getValue();
              if (!userId || !groupId) {
                return;
              }

            Ext.Ajax.request({
            url: '/admin/users/addGroup',
            success: function () {
              userGroupsStore.reload();
            },
            failure: function () {
              alert('Assign failed');
            },
            params: { 'Ug.user_id': userId, 'Ug.group_id': groupId }
          });
            }
          },
          '-',
          groupsDelButton
    ]);

    var groupsGrid = new Ext.grid.GridPanel({
      store: userGroupsStore,
      title: 'Groups',
      header: false,
      columns: [
            {
               id:'group_name',
               header: "Assigned to Groups",
               dataIndex: 'group_name'
            }
        ],
      border: true,
      bodyBorder: true,
      autoScroll:true,
      loadMask: true,
          viewConfig: {
              autoFill:true
          },
      height: 150,
      bbar: groupsBBar
    });

    groupsGrid.getSelectionModel().on('selectionchange', function () {
      groupsDelButton.setDisabled(!groupsGrid.getSelectionModel().hasSelection());
    });

    groupsBBar.on('enable', function () {
      groupsGrid.getSelectionModel().fireEvent('selectionchange');
    });

    groupsBBar.disable();

    var config = {
      defaults: {
        border: false,
        style: {padding: '10px'}
      },
      items: [{
        layout: 'column',
        defaults: {
          border: false
        },
        items: [
          firstColumn,
          {
            layout: 'form',
            width: 250,
            labelAlign: 'top',
            items: groupsGrid
          }
        ]
      }],
          tbar: [
          {
              text: 'Save',
              handler: function () {
                userForm.getForm().submit({
                  url: '/admin/users/save.json',
                  success: function (form, action) {
                    this.ownerCt.userGrid.getStore().reload({
                      callback: function () {
                        userForm.loadUser(action.result.user_id);
                      }
                    });
                  },
                  scope: userForm
                });
              },
              cls: 'x-btn-text-icon',
        iconCls: 'page_save'
          },{
              text: 'Reset',
              handler: function () {
                userForm.getForm().reset();
          userGroupsStore.removeAll();
          groupsBBar.disable();
          groupsCombo.reset();
          userForm.setTitle('User Form [Search Mode]');
              },
        cls: 'x-btn-text-icon',
        iconCls: 'page_green'
          },{
            text: 'Find',
            handler: function () {
              this.ownerCt.userGrid.reload(userForm.getForm().getValues());
            },
            scope: this,
        cls: 'x-btn-text-icon',
        iconCls: 'page_find'
          }],
          labelAlign: 'right', // label settings here cascade unless overridden
          labelWidth: 120,
          loadUser: function (id) {
        this.load({
          url: String.format('/admin/users/load/{0}.json', id),
          scripts: false,
          text: 'Loading ...',
          success: function () {
            userForm.setTitle('User Form [Edit Mode]');
          }
        });
          }
    };

    Ext.apply(this, config);

    IOH.UsersForm.superclass.initComponent.apply(this, arguments);

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

    userForm.getForm().on('actioncomplete', function (form, action) {
      if (action.type == 'load') {
        var userId = action.result.data['User.id'];
        if (userId) {
          userGroupsStore.load({params:{userId:userId}});
          groupsBBar.enable();
        } else {
          userGroupsStore.removeAll();
          groupsBBar.disable();
        }
      }
    });
  }
});
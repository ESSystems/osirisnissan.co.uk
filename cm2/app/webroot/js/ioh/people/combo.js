/**
 *
 */

IOH.PersonCombo = Ext.extend(Ext.form.ComboBox,
{
  emptyText: 'Select Person',
  triggerClass: 'x-form-search-trigger',
  minChars: 1,
  showLeavers: false,

  initComponent: function () {
    var combo = this;
      var resultTpl = new Ext.XTemplate(
              '<tpl for="."><div class="search-item x-combo-list-item">',
                  '<span style="float: left;">{[values.Person.full_name]} ({[fm.date(values.dob, Ext.form.DateField.prototype.format)]})</span>',
                '<tpl if="Organisation.OrganisationName"><div class="organisation quiet">{[values.Organisation.OrganisationName]}</div>',
                '<div style="clear:both;">',
                  '<tpl if="Employee.salary_number"><span class="quiet">Sal.:</span> {[values.Employee.salary_number]} </tpl>',
                  '<tpl if="Employee.sap_number"><span class="quiet">SAP:</span> {[values.Employee.sap_number]}</tpl>',
                  '</div>',
                  '</tpl>',
              '</div></tpl>'
          );

    var cfg = {
          mode: 'remote',
        editable: true,
        forceSelection: true,
        triggerAction: 'all',
            store: new Ext.data.JsonStore({
          url: '/persons/lookup.json',
          baseParams: {showLeavers: combo.showLeavers},
          totalProperty: 'totalRows',
          successProperty: 'success',
          root: 'rows',
          id: 'Person.id',
          fields: [
              'Person', 'Employee', 'Patient',
              {name: 'Organisation', mapping: 'Patient.Organisation'},
              {name: 'full_name', mapping: 'Person.full_name'},
              {name: 'id', mapping: 'Person.id'},
              {name: 'dob', mapping: 'Person.date_of_birth', type: 'date', dateFormat: 'Y-m-d'}
          ]
        }),
        pageSize: 10,
        tpl: resultTpl,
        itemSelector: 'div.search-item',
        displayField: 'full_name',
        valueField: 'id',
        queryDelay: 1000
        };

    Ext.apply(this, cfg);
    Ext.apply(this.initialConfig, cfg);

    IOH.PersonCombo.superclass.initComponent.apply(this, arguments);

    this.store.on({
      beforeload: function (store, options) {
        this.setDisabled(true);
      },
      load: function (store, rec, options) {
        this.setDisabled(false);
      },
      scope: this
    });
  }
});

Ext.reg('IOH.PersonCombo', IOH.PersonCombo);

<?=$this->requestAction('/persons/form.extjs', array('return'))?>
<?=$this->requestAction('/persons/grid.extjs', array('return'))?>

<script type="text/javascript">

Ext.namespace('IOH.People');

IOH.People.Window = Ext.extend(Ext.Window, {
  initComponent: function () {
    this.peopleStore = new IOH.People.Store();
    this.peopleGrid = new IOH.People.Grid({
      store: this.peopleStore,
      region: 'center',
      split: true,
      collapsible: true,
      collapseMode: 'mini',
      minHeight: 100
    });

    this.peopleForm = new IOH.People.Form({
      store: this.peopleStore,
      region: 'north',
      split: true,
      collapsible: true,
      collapseMode: 'mini',
      height: 370
    });

    var cfg = {
      title: 'Select Person',
      layout: 'border',
      width:550,
      height:580,
      closeAction:'hide',
      plain: true,
      border: false,
      items: [this.peopleForm, this.peopleGrid],
      buttons: [{
        text:'Select',
        handler: this.onSelect,
        scope: this
      },{
        text: 'Close',
        handler: function () {
          this.hide()
        },
        scope: this
      }]
    };

    Ext.apply(this, cfg);
    Ext.apply(this.initialConfig, cfg);

    IOH.People.Window.superclass.initComponent.apply(this, arguments);

    this.on('beforeshow', function () {
      this.peopleForm.options = this.options;
      this.peopleGrid.options = this.options;

      if (this.options.personId) {
        this.peopleForm.loadPerson(this.options.personId);
      } else {
        this.peopleForm.resetForm();
      }
    }, this);

    this.peopleGrid.getSelectionModel().on('rowselect', function(model, rowIndex, record) {
      if (model.getCount() == 1) {
        this.peopleForm.loadPerson(record.id);
      }
    }, this);
  },

  onSelect: function () {
    if (typeof this.options.onSelect == 'function') {
      return this.options.onSelect.call(this, this.peopleGrid.getSelectionModel().getSelections(), this);
    }

    var v = this.peopleForm.getForm().getFieldValues();
    var id = v['Person.id'];
    if (!id) {
      alert('Nothing selected');
      return false;
    }
    var o = Ext.apply({
      nameField: 'Person.full_name',
      valueField: 'Person.id'
    }, this.options);
    var f = o.targetForm.getForm();
    f.findField(o.nameField).setValue(v['Person.full_name'].replace('+', ' '));
    f.findField(o.valueField).setValue(id);
    this.hide();
  }
});
</script>
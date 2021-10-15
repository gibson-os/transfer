Ext.define('GibsonOS.module.transfer.session.Window', {
    extend: 'GibsonOS.Window',
    alias: ['widget.gosModuleTransferSessionWindow'],
    title: 'Verbindungen',
    width: 500,
    height: 295,
    layout: 'border',
    requiredPermission: {
        module: 'transfer',
        task: 'session'
    },
    initComponent: function() {
        this.items = [{
            xtype: 'gosModuleTransferSessionGrid',
            region: 'west',
            flex: 0,
            hideHeaders: true,
            split: true,
            collapsible: true,
            hideCollapseTool: true,
            header: false,
            width: 150
        },{
            xtype: 'gosModuleTransferSessionForm',
            region: 'center'
        }];

        this.callParent();

        var window = this;
        var form = this.down('#transferSessionForm');
        var grid = this.down('#transferSessionGrid');

        grid.getSelectionModel().on('selectionchange', function(selectionModel, records, options) {
            var record = records[0];
            form.enable();

            if (form.isDirty()) {
                GibsonOS.MessageBox.show({
                    title: 'Verbindung speichern?',
                    msg: 'Die Verbindung wurde nicht gespeichert. Jetzt speichern?',
                    type: GibsonOS.MessageBox.type.QUESTION,
                    buttons: [{
                        text: 'Ja',
                        handler: function() {
                            form.down('#transferSessionFormSaveButton').handler();

                            // @todo Danach
                            //form.getForm.findField('password').setValue(null);
                            //form.loadRecord(record);
                        }
                    },{
                        text: 'Nein',
                        handler: function() {
                            form.getForm().findField('password').setValue(null);
                            form.loadRecord(record);

                            form.getForm().getFields().each(function(field) {
                                field.originalValue = field.getValue();
                            });
                        }
                    }]
                });
            } else {
                form.loadRecord(record);
            }
        });
        grid.getStore().on('remove', function(store, record, index, isMove, options) {
            if (record.get('id') == form.getForm().findField('id').getValue()) {
                form.disable();
            }
        });
        form.getForm().on('actioncomplete', function(form, action, options) {
            var data = Ext.decode(action.response.responseText).data;
            var record = grid.getSelectionModel().getSelection()[0];

            Ext.iterate(data, function(key, value) {
                record.set(key, value);
            });

            record.commit();
            grid.down('#transferSessionGridDeleteButton').enable();
        });
    }
});
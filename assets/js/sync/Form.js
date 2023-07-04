Ext.define('GibsonOS.module.transfer.sync.Form', {
    extend: 'GibsonOS.form.Panel',
    alias: ['widget.gosModuleTransferSyncForm'],
    itemId: 'transferSyncForm',
    trackResetOnLoad: true,
    disabled: true,
    requiredPermission: {
        module: 'transfer',
        task: 'sync'
    },
    initComponent: function() {
        var me = this;

        me.items = [{
            xtype: 'gosFormHidden',
            name: 'id'
        },{
            xtype: 'fieldcontainer',
            fieldLabel: 'Lokales Verzeichnis',
            layout: 'hbox',
            defaults: {
                hideLabel: true
            },
            items: [{
                xtype: 'gosFormTextfield',
                name: 'localPath',
                flex: 1,
                margins: '0 5 0 0'
            },{
                xtype: 'gosButton',
                text: '...',
                handler: function() {
                    GibsonOS.module.explorer.dir.fn.dialog(me.getForm().findField('localPath'));
                }
            }]
        },{
            xtype: 'fieldcontainer',
            fieldLabel: 'Remote Verzeichnis',
            layout: 'hbox',
            defaults: {
                hideLabel: true
            },
            items: [{
                xtype: 'gosFormTextfield',
                name: 'remotePath',
                flex: 1,
                margins: '0 5 0 0'
            },{
                xtype: 'gosButton',
                text: '...',
                handler: function() {
                    var remotePathField = me.getForm().findField('remotePath');
                    var remotePath = remotePathField.getValue();

                    var dialog = new GibsonOS.module.transfer.index.Dialog({
                        gos: {
                            data: {
                                id: me.getForm().findField('id').getValue()
                            }
                        }
                    });
                    dialog.down('#gosModuleTransferIndexDialogOkButton').handler = function() {
                        var record = dialog.down('gosModuleTransferIndexTree').getSelectionModel().getSelection()[0];
                        remotePathField.setValue(record.get('id'));
                        dialog.close();
                    }
                }
            }]
        },{
            xtype: 'gosModuleTransferSyncIntervalComboBox'
        },{
            xtype: 'gosModuleTransferSyncDirectionComboBox'
        },{
            xtype: 'gosModuleTransferSyncDeleteComboBox'
        },{
            xtype: 'gosFormCheckbox',
            name: 'crypt',
            inputValue: true,
            fieldLabel: 'Verschlüsseln',
            boxLabel: 'Ordner und Dateien auf dem entfernten System verschlüsseln'
        },{
            xtype: 'gosFormCheckbox',
            name: 'active',
            inputValue: true,
            fieldLabel: 'Aktiviert'
        }];

        me.buttons = [{
            text: 'Speichern',
            itemId: 'transferSyncFormSaveButton',
            requiredPermission: {
                action:'save',
                permission: GibsonOS.Permission.MANAGE + GibsonOS.Permission.WRITE
            },
            handler: function() {
                me.getForm().submit({
                    xtype: 'gosFormActionAction',
                    url: baseDir + 'transfer/syn',
                    method: 'POST',
                    success: function(form, action) {
                        GibsonOS.MessageBox.show({
                            title: 'Gespeichert!',
                            msg: 'Synchronisation wurde erfolgreich gespeichert!',
                            type: GibsonOS.MessageBox.type.INFO
                        });
                    }
                })
            }
        }];

        me.callParent();

        me.down('gosModuleTransferSyncDirectionComboBox').on('change', function(combo, value) {
            var disable = false;

            if (value == 'sync') {
                disable = true;
            }

            me.getForm().findField('delete').setDisabled(disable);
        });
    }
});
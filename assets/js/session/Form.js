Ext.define('GibsonOS.module.transfer.session.Form', {
    extend: 'GibsonOS.form.Panel',
    alias: ['widget.gosModuleTransferSessionForm'],
    itemId: 'transferSessionForm',
    trackResetOnLoad: true,
    disabled: true,
    requiredPermission: {
        module: 'transfer',
        task: 'session'
    },
    initComponent: function() {
        var me = this;

        me.items = [{
            xtype: 'gosFormHidden',
            name: 'id'
        },{
            xtype: 'gosFormTextfield',
            name: 'name',
            fieldLabel: 'Name'
        },{
            xtype: 'gosModuleCoreParameterTypeAutoComplete',
            name: 'protocol',
            fieldLabel: 'Protokoll',
            valueField: 'className',
            displayField: 'name',
            parameterObject: {
                config: {
                    model: 'GibsonOS.module.transfer.session.model.Client',
                    autoCompleteClassname: 'GibsonOS\\Module\\Transfer\\AutoComplete\\ClientAutoComplete',
                    parameters: {}
                }
            }
        },{
            xtype: 'fieldcontainer',
            fieldLabel: 'URL',
            layout: 'hbox',
            defaults: {
                hideLabel: true
            },
            items: [{
                xtype: 'gosFormTextfield',
                name: 'url',
                flex: 1,
                margins: '0 5 0 0'
            },{
                xtype: 'gosFormNumberfield',
                name: 'port',
                width: 50,
                emptyText: 'Port'
            }]
        },{
            xtype: 'gosFormTextfield',
            name: 'user',
            fieldLabel: 'Benutzer'
        },{
            xtype: 'gosFormTextfield',
            inputType: 'password',
            name: 'password',
            fieldLabel: 'Passwort'
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
                                dir: remotePath ? remotePath : null,
                                id: me.getForm().findField('id').getValue(),
                                url: me.getForm().findField('url').getValue(),
                                port: me.getForm().findField('port').getValue(),
                                // @todo protocol hat falsches model und deswegen ist value immer null
                                protocol: me.getForm().findField('protocol').valueModels[0].raw.className,
                                user: me.getForm().findField('user').getValue(),
                                password: me.getForm().findField('password').getValue()
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
            xtype: 'gosFormCheckbox',
            name: 'onlyForThisUser',
            inputValue: true,
            fieldLabel: 'Zugriff',
            boxLabel: 'Nur für den aktuellen Benutzer'
        }];

        me.buttons = [{
            text: 'Speichern',
            itemId: 'transferSessionFormSaveButton',
            requiredPermission: {
                action: '',
                permission: GibsonOS.Permission.MANAGE + GibsonOS.Permission.WRITE,
                method: 'POST'
            },
            handler: function() {
                me.getForm().submit({
                    xtype: 'gosFormActionAction',
                    url: baseDir + 'transfer/session',
                    method: 'POST',
                    params: {
                        // @todo protocol hat falsches model und deswegen ist value immer null
                        clientClass: me.getForm().findField('protocol').valueModels[0].raw.className
                    },
                    success: function(form, action) {
                        var data = Ext.decode(action.response.responseText).data;

                        if (data.authenticationUrl) {
                            location.href = data.authenticationUrl;
                        } else {
                            GibsonOS.MessageBox.show({
                                title: 'Gespeichert!',
                                msg: 'Verbindung wurde erfolgreich gespeichert!',
                                type: GibsonOS.MessageBox.type.INFO
                            });
                        }
                    }
                })
            }
        }];

        me.callParent();

        me.down('gosModuleCoreParameterTypeAutoComplete').on('change', function(combo, value) {
            var disable = false;

            if (value == 'amazondrive') {
                disable = true;
            }

            me.getForm().findField('url').setDisabled(disable);
            me.getForm().findField('port').setDisabled(disable);
            me.getForm().findField('user').setDisabled(disable);
            me.getForm().findField('password').setDisabled(disable);
            me.getForm().findField('remotePath').setDisabled(disable);
        });
    }
});
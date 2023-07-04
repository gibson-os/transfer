Ext.define('GibsonOS.module.transfer.session.Grid', {
    extend: 'GibsonOS.grid.Panel',
    alias: ['widget.gosModuleTransferSessionGrid'],
    itemId: 'transferSessionGrid',
    requiredPermission: {
        module: 'transfer',
        task: 'session'
    },
    initComponent: function() {
        var grid = this;

        this.store = new GibsonOS.module.transfer.session.store.Grid();
        this.columns = [{
            dataIndex: 'name',
            flex: 1,
            renderer: function(value) {
                if (!value) {
                    return '(Neue Verbindung)';
                }

                return value;
            }
        }];
        this.tbar = [{
            iconCls: 'icon_system system_add',
            requiredPermission: {
                action: 'save',
                permission: GibsonOS.Permission.WRITE
            },
            handler: function() {
                grid.getSelectionModel().select(grid.getStore().add({port: 21}));
            }
        }, {
            iconCls: 'icon_system system_delete',
            itemId: 'transferSessionGridDeleteButton',
            disabled: true,
            requiredPermission: {
                action: 'delete',
                permission: GibsonOS.Permission.WRITE
            },
            handler: function () {
                var record = grid.getSelectionModel().getSelection()[0];

                GibsonOS.MessageBox.show({
                    title: 'Verbindung löschen?',
                    msg: 'Die Verbindung ' + record.get('name') + ' wirklich löschen?',
                    type: GibsonOS.MessageBox.type.QUESTION,
                    buttons: [{
                        text: 'Ja',
                        handler: function () {
                            GibsonOS.Ajax.request({
                                url: baseDir + 'transfer/session',
                                method: 'DELETE',
                                params: {
                                    id: record.get('id')
                                },
                                success: function (response) {
                                    grid.getStore().remove(record);
                                }
                            });
                        }
                    }, {
                        text: 'Nein'
                    }]
                });
            }
        },{
            xtype: 'gosModuleTransferSyncButton',
            disabled: true
        }];

        this.callParent();

        this.getSelectionModel().on('selectionchange', function(selectionModel, records, options) {
            var deleteButton = grid.down('#transferSessionGridDeleteButton');

            if (!records.length) {
                deleteButton.disable();
                return false;
            }

            if (records[0].get('id')) {
                deleteButton.enable();
            } else {
                deleteButton.disable();
            }
        });
    }
});
Ext.define('GibsonOS.module.transfer.sync.Grid', {
    extend: 'GibsonOS.grid.Panel',
    alias: ['widget.gosModuleTransferSyncGrid'],
    itemId: 'transferSyncGrid',
    requiredPermission: {
        module: 'transfer',
        task: 'sync'
    },
    initComponent: function() {
        var me = this;

        me.store = new GibsonOS.module.transfer.sync.store.Grid();
        me.columns = [{
            dataIndex: 'localPath',
            flex: 1,
            renderer: function (value) {
                if (!value) {
                    return '(Neue Synchronisation)';
                }

                return value;
            }
        },{
            dataIndex: 'direction',
            width: 25,
            renderer: function(value) {
                if (!value) {
                    return '';
                }

                return value;
            }
        },{
            dataIndex: 'remotePath',
            flex: 1,
            renderer: function(value) {
                if (!value) {
                    return '';
                }

                return value;
            }
        }];
        me.tbar = [{
            iconCls: 'icon_system system_add',
            requiredPermission: {
                action: 'save',
                permission: GibsonOS.Permission.WRITE
            },
            handler: function() {
                me.getSelectionModel().select(me.getStore().add({port: 21}));
            }
        },{
            iconCls: 'icon_system system_delete',
            itemId: 'transferSyncGridDeleteButton',
            disabled: true,
            requiredPermission: {
                action: 'delete',
                permission: GibsonOS.Permission.WRITE
            },
            handler: function() {
                var record = me.getSelectionModel().getSelection()[0];

                GibsonOS.MessageBox.show({
                    title: 'Synchronisation löschen?',
                    msg: 'Die Synchronisation ' + record.get('name') + ' wirklich löschen?',
                    type: GibsonOS.MessageBox.type.QUESTION,
                    buttons: [{
                        text: 'Ja',
                        handler: function() {
                            GibsonOS.Ajax.request({
                                url: baseDir + 'transfer/sync',
                                method: 'DELETE',
                                params:  {
                                    id: record.get('id')
                                },
                                success: function(response) {
                                    me.getStore().remove(record);
                                }
                            });
                        }
                    },{
                        text: 'Nein'
                    }]
                });
            }
        }];

        me.callParent();

        me.getSelectionModel().on('selectionchange', function(selectionModel, records, options) {
            var deleteButton = me.down('#transferSyncGridDeleteButton');

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
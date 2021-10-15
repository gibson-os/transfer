Ext.define('GibsonOS.module.transfer.index.transfer.Grid', {
    extend: 'GibsonOS.grid.Panel',
    alias: ['widget.gosModuleTransferIndexTransferGrid'],
    requiredPermission: {
        module: 'transfer',
        task: 'index'
    },
    multiSelect: true,
    viewConfig: {
        loadMask: false,
        preserveScrollOnRefresh: true
    },
    initComponent: function() {
        var grid = this;

        this.store = new GibsonOS.module.transfer.index.store.Transfer({
            gos: {
                autoReload: true,
                autoReloadDelay: 3000,
                data: this.gos.data
            },
            listeners: {
                load: function(store, records, successful, operation, options) {
                    var autoRefresh = store.getProxy().getReader().jsonData.autoRefresh;
                    var btnRefresh = grid.down('#transferIndexTransferRefreshButton');

                    if (autoRefresh == 1) {
                        btnRefresh.toggle(true);
                    } else {
                        btnRefresh.toggle(false);
                    }

                    store.gos.autoReload = autoRefresh;
                }
            }
        });
        this.columns = [{
            header: '&nbsp;',
            dataIndex: 'direction',
            width: 25,
            renderer: function(value, metaData, record) {
                return '<div class="icon_system system_' + (record.get('crypt') ? 'key' : (value == 'download' ? 'down' : 'up')) + '"></div>';
            }
        },{
            header: 'Lokaler Pfad',
            dataIndex: 'localPath',
            flex: 1
        },{
            header: 'Remote Pfad',
            dataIndex: 'remotePath',
            flex: 1
        },{
            header: 'Größe',
            dataIndex: 'size',
            align: 'right',
            width: 70,
            renderer: function(value) {
                return transformSize(value);
            }
        },{
            header: 'Übertragen',
            dataIndex: 'transferred',
            align: 'right',
            width: 70,
            renderer: function(value) {
                return transformSize(value);
            }
        },{
            header: 'Fortschritt',
            xtype: 'gosGridColumnProgressBar'
        },{
            header: 'Dauer',
            dataIndex: 'elapsed',
            align: 'right',
            width: 60,
            renderer: function(value) {
                var date = new Date(((23 * 60 * 60) + value) * 1000);
                return Ext.Date.format(date, 'H:i:s');
            }
        },{
            header: 'Übrig',
            dataIndex: 'remaining',
            align: 'right',
            width: 60,
            renderer: function(value) {
                var date = new Date(((23 * 60 * 60) + value) * 1000);
                return Ext.Date.format(date, 'H:i:s');
            }
        },{
            header: 'Geschwindigkeit',
            dataIndex: 'speed',
            align: 'right',
            renderer: function(value) {
                return transformSize(value) + '/s';
            }
        }];
        this.dockedItems = [{
            xtype: 'gosToolbar',
            dock: 'top',
            items: [{
                xtype: 'gosButton',
                itemId: 'transferIndexTransferRefreshButton',
                text: 'Refresh',
                enableToggle: true,
                listeners: {
                    toggle: function(button, pressed, options) {
                        var store = grid.getStore();

                        if (pressed) {
                            store.getProxy().setExtraParam('autoRefresh', 1);
                            store.gos.cancelLoad();
                            store.gos.autoReload = true;
                        } else {
                            store.getProxy().setExtraParam('autoRefresh', 0);
                            store.gos.autoReload = false;
                            store.gos.cancelLoad();
                        }
                    }
                }
            }]
        },{
            xtype: 'gosToolbarPaging',
            store: this.store,
            displayMsg: 'Übertragungen {0} - {1} von {2}',
            emptyMsg: 'Keine Übertragungen vorhanden'
        }];

        this.callParent();

        this.on('activate', function(grid, options) {
            grid.getStore().load();
        });
    }
});
Ext.define('GibsonOS.module.transfer.index.Container', {
    extend: 'GibsonOS.Panel',
    alias: ['widget.gosModuleTransferIndexContainer'],
    itemId: 'transferIndexView',
    layout: 'fit',
    gos: {
        data: {
            fileSize: 0,
            fileCount: 0,
            dirCount: 0
        }
    },
    initComponent: function() {
        var view = this;

        this.gos.store = new GibsonOS.module.transfer.index.store.View({
            autoLoad: false,
            gos: {
                data: {
                    extraParams: {
                        dir: this.gos.data.dir
                    }
                }
            }
        });
        this.gos.store.on('load', function(store, records, successful, operation, options) {
            var dir = store.getProxy().getReader().jsonData.dir;

            if (store.getProxy().getReader().jsonData.meta) {
                view.gos.data.fileSize = store.getProxy().getReader().jsonData.meta.fileSize;
                view.gos.data.fileCount = store.getProxy().getReader().jsonData.meta.fileCount;
                view.gos.data.dirCount = store.getProxy().getReader().jsonData.meta.dirCount;
            }
        }, this, {
            priority: 999
        });

        this.items = [{
            xtype: 'gosModuleTransferIndexContainerGrid',
            store: this.gos.store,
            listeners: {
                //itemclick: GibsonOS.module.transfer.index.itemClick
            }
        },{
            xtype: 'gosModuleTransferIndexView',
            store: this.gos.store,
            hidden: true,
            gos: {
                data: {
                    iconSize: 32
                }
            },
            listeners: {
                //itemclick: GibsonOS.module.explorer.index.listener.itemClick
            }
        },{
            xtype: 'gosModuleTransferIndexView',
            store: this.gos.store,
            hidden: true,
            gos: {
                data: {
                    iconSize: 48
                }
            },
            listeners: {
                //itemclick: GibsonOS.module.explorer.index.listener.itemClick
            }
        },{
            xtype: 'gosModuleTransferIndexView',
            store: this.gos.store,
            hidden: true,
            gos: {
                data: {
                    iconSize: 64
                }
            },
            listeners: {
                //itemclick: GibsonOS.module.explorer.index.listener.itemClick
            }
        },{
            xtype: 'gosModuleTransferIndexView',
            store: this.gos.store,
            hidden: true,
            gos: {
                data: {
                    iconSize: 128
                }
            },
            listeners: {
                //itemclick: GibsonOS.module.explorer.index.listener.itemClick
            }
        },{
            xtype: 'gosModuleTransferIndexView',
            store: this.gos.store,
            hidden: true,
            gos: {
                data: {
                    iconSize: 256
                }
            },
            listeners: {
                //itemclick: GibsonOS.module.explorer.index.listener.itemClick
            }
        }];

        this.callParent();
    }
});
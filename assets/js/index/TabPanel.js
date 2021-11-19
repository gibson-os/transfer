Ext.define('GibsonOS.module.transfer.index.TabPanel', {
    extend: 'GibsonOS.TabPanel',
    alias: ['widget.gosModuleTransferIndexTabPanel'],
    itemId: 'transferIndexTabPanel',
    border: true,
    initComponent: function() {
        this.items = [{
            xtype: 'gosModuleExplorerIndexPanel',
            title: 'Explorer',
            flex: 0,
            gos: {
                data: {
                    dir: this.gos.data.dir ? this.gos.data.dir : null
                }
            },
            requiredPermission: {
                module: 'explorer',
                task: 'dir'
            }
        },{
            xtype: 'gosModuleTransferIndexPanel',
            title: 'Remote',
            flex: 0,
            requiredPermission: {
                module: 'transfer',
                task: 'index'
            }
        }];

        this.callParent();

        this.down('#explorerDirGrid').itemContextMenu.insert(2, {xtype: 'gosModuleTransferIndexUploadButton'});
        this.down('#explorerDirView32').itemContextMenu.insert(2, {xtype: 'gosModuleTransferIndexUploadButton'});
        this.down('#explorerDirView48').itemContextMenu.insert(2, {xtype: 'gosModuleTransferIndexUploadButton'});
        this.down('#explorerDirView64').itemContextMenu.insert(2, {xtype: 'gosModuleTransferIndexUploadButton'});
        this.down('#explorerDirView128').itemContextMenu.insert(2, {xtype: 'gosModuleTransferIndexUploadButton'});
        this.down('#explorerDirView256').itemContextMenu.insert(2, {xtype: 'gosModuleTransferIndexUploadButton'});
        this.down('#explorerDirTree').itemContextMenu.insert(2, {
            xtype: 'gosModuleTransferIndexUploadButton',
            uploadFunction(crypt) {
                const menu = this.up('#contextMenu');
                const parent = menu.getParent();
                const record = menu.getRecord();
                let extraParams = {};

                let remotePath = null;
                const activeTab = GibsonOS.module.transfer.index.fn.getConnectedTransferNeighbor(parent);

                if (activeTab) {
                    remotePath = activeTab.down('#transferIndexView').gos.store.getProxy().getReader().jsonData.dir;
                    extraParams = activeTab.down('#transferIndexView').gos.store.getProxy().extraParams;
                }

                if (remotePath) {
                    GibsonOS.module.transfer.index.fn.upload(record.get('id'), null, remotePath, {
                        id: extraParams.id ? extraParams.id : null,
                        url: extraParams.url ? extraParams.url : null,
                        port: extraParams.port ? extraParams.port : null,
                        protocol: extraParams.protocol ? extraParams.protocol : null,
                        user: extraParams.user ? extraParams.user : null,
                        password: extraParams.password ? extraParams.password : null
                    }, crypt);
                }
            }
        });

        this.down('#explorerDirGrid').removeListener('itemdblclick', GibsonOS.module.explorer.dir.itemDblClick);
        this.down('#explorerDirView32').removeListener('itemdblclick', GibsonOS.module.explorer.dir.itemDblClick);
        this.down('#explorerDirView48').removeListener('itemdblclick', GibsonOS.module.explorer.dir.itemDblClick);
        this.down('#explorerDirView64').removeListener('itemdblclick', GibsonOS.module.explorer.dir.itemDblClick);
        this.down('#explorerDirView128').removeListener('itemdblclick', GibsonOS.module.explorer.dir.itemDblClick);
        this.down('#explorerDirView256').removeListener('itemdblclick', GibsonOS.module.explorer.dir.itemDblClick);

        this.down('#explorerDirGrid').on('itemdblclick', GibsonOS.module.transfer.index.listener.explorerItemDblClick);
        this.down('#explorerDirView32').on('itemdblclick', GibsonOS.module.transfer.index.listener.explorerItemDblClick);
        this.down('#explorerDirView48').on('itemdblclick', GibsonOS.module.transfer.index.listener.explorerItemDblClick);
        this.down('#explorerDirView64').on('itemdblclick', GibsonOS.module.transfer.index.listener.explorerItemDblClick);
        this.down('#explorerDirView128').on('itemdblclick', GibsonOS.module.transfer.index.listener.explorerItemDblClick);
        this.down('#explorerDirView256').on('itemdblclick', GibsonOS.module.transfer.index.listener.explorerItemDblClick);
    }
});
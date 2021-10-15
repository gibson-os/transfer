Ext.define('GibsonOS.module.transfer.index.download.Button', {
    extend: 'Ext.menu.Item',
    alias: ['widget.gosModuleTransferIndexDownloadButton'],
    itemId: 'transferIndexDownloadButton',
    text: 'Download',
    iconCls: 'icon_system system_down',
    requiredPermission: {
        task: 'index',
        action: 'download',
        permission: GibsonOS.Permission.WRITE
    },
    handler: function() {
        var menu = this.up('#contextMenu');
        var parent = menu.getParent();
        var records = parent.getSelectionModel().getSelection();
        var store = parent.getStore();
        var proxy = store.getProxy();
        var dir = proxy.getReader().jsonData.dir;
        var extraParams = proxy.extraParams;

        var localPath = null;
        var activeTab = GibsonOS.module.transfer.index.fn.getActiveNeighborTab(parent);

        if (activeTab) {
            if (activeTab.getXType() == 'gosModuleExplorerIndexPanel') {
                localPath = activeTab.down('#explorerIndexView').gos.store.getProxy().getReader().jsonData.dir;
            } // Sonst FTP
        }

        if (localPath) {
            var files = [];

            Ext.iterate(records, function(record) {
                files.push(record.get('name'));
            });

            GibsonOS.module.transfer.index.fn.download(dir, files, localPath, {
                id: extraParams.id ? extraParams.id : null,
                url: extraParams.url ? extraParams.url : null,
                port: extraParams.port ? extraParams.port : null,
                protocol: extraParams.protocol ? extraParams.protocol : null,
                user: extraParams.user ? extraParams.user : null,
                password: extraParams.password ? extraParams.password : null
            });
        }
    }
});
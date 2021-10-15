GibsonOS.define('GibsonOS.module.transfer.index.listener.itemDblClick', function(view, record, item, index, event, options) {
    var store = view.getStore();
    var proxy = store.getProxy();
    var dir = proxy.getReader().jsonData.dir;

    if (record.get('type') == 'dir') {
        GibsonOS.module.transfer.index.fn.open(store, dir + record.get('name') + '/');
    } else {
        var extraParams = proxy.extraParams;

        // Wenn von FTP zu FTP 2 Schritte speichern

        var activeTab = GibsonOS.module.transfer.index.fn.getActiveNeighborTab(view);
        var localPath = null;

        if (activeTab) {
            if (activeTab.getXType() == 'gosModuleExplorerIndexPanel') {
                localPath = activeTab.down('#explorerIndexView').gos.store.getProxy().getReader().jsonData.dir;
            } // Sonst FTP
        }

        if (localPath) {
            GibsonOS.module.transfer.index.fn.download(dir, record.get('name'), localPath, {
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
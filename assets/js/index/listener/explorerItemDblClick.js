GibsonOS.define('GibsonOS.module.transfer.index.listener.explorerItemDblClick', function(view, record, item, index, event, options) {
    var store = view.getStore();
    var proxy = store.getProxy();
    var dir = proxy.getReader().jsonData.dir;

    if (GibsonOS.module.explorer.file.chromecast.fn.play(record)) {
        return true;
    }

    if (record.get('type') == 'dir') {
        GibsonOS.module.explorer.dir.open(store, dir + record.get('name') + '/');
    } else {
        var extraParams = {};

        // Explorer zu Explorer verhindern?

        var remotePath = null;
        var activeTab = GibsonOS.module.transfer.index.fn.getConnectedTransferNeighbor(view);

        if (activeTab) {
            remotePath = activeTab.down('#transferIndexView').gos.store.getProxy().getReader().jsonData.dir;
            extraParams = activeTab.down('#transferIndexView').gos.store.getProxy().extraParams;
        }

        if (remotePath) {
            GibsonOS.module.transfer.index.fn.upload(dir, record.get('name'), remotePath, {
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
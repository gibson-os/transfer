Ext.define('GibsonOS.module.transfer.sync.store.Grid', {
    extend: 'GibsonOS.data.Store',
    alias: ['store.gosModuleTransferSyncGridStore'],
    pageSize: 100,
    model: 'GibsonOS.module.transfer.sync.model.Grid',
    constructor: function(data) {
        this.proxy = {
            type: 'gosDataProxyAjax',
            url: baseDir + 'transfer/sync',
            method: 'GET'
        };

        this.callParent(arguments);
    }
});
Ext.define('GibsonOS.module.transfer.index.store.Transfer', {
    extend: 'GibsonOS.data.Store',
    alias: ['store.gosModuleTransferIndexTransferStore'],
    autoLoad: false,
    pageSize: 100,
    model: 'GibsonOS.module.transfer.index.model.Transfer',
    constructor: function(data) {
        this.proxy = {
            type: 'gosDataProxyAjax',
            url: baseDir + 'transfer/index/transfer',
            method: 'GET',
            extraParams: {
                type: data.gos.data.type,
                autoRefresh: -1
            }
        };

        this.callParent(arguments);
    }
});
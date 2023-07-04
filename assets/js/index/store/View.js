Ext.define('GibsonOS.module.transfer.index.store.View', {
    extend: 'GibsonOS.data.Store',
    alias: ['store.gosModuleTransferIndexContainerStore'],
    proxy: {
        type: 'gosDataProxyAjax',
        url: baseDir + 'transfer/index',
        method: 'GET'
    },
    model: 'GibsonOS.module.transfer.index.model.View'
});
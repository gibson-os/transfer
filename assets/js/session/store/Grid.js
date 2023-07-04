Ext.define('GibsonOS.module.transfer.session.store.Grid', {
    extend: 'GibsonOS.data.Store',
    alias: ['store.gosModuleTransferSessionGridStore'],
    proxy: {
        type: 'gosDataProxyAjax',
        url: baseDir + 'transfer/session',
        method: 'GET'
    },
    model: 'GibsonOS.module.transfer.session.model.Grid'
});
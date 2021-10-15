Ext.define('GibsonOS.module.transfer.session.store.Grid', {
    extend: 'GibsonOS.data.Store',
    alias: ['store.gosModuleTransferSessionGridStore'],
    proxy: {
        type: 'gosDataProxyAjax',
        url: baseDir + 'transfer/session/index'
    },
    model: 'GibsonOS.module.transfer.session.model.Grid'
});
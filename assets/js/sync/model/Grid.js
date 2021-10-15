Ext.define('GibsonOS.module.transfer.sync.model.Grid', {
    extend: 'GibsonOS.data.Model',
    fields: [{
        name: 'id',
        type: 'int'
    },{
        name: 'direction',
        type: 'string'
    },{
        name: 'localPath',
        type: 'string'
    },{
        name: 'remotePath',
        type: 'string'
    },{
        name: 'interval',
        type: 'string'
    },{
        name: 'delete',
        type: 'string'
    },{
        name: 'crypt',
        type: 'int'
    },{
        name: 'status',
        type: 'string'
    },{
        name: 'next_sync',
        type: 'date'
    }]
});
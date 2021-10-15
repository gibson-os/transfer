Ext.define('GibsonOS.module.transfer.index.model.Transfer', {
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
        name: 'size',
        type: 'int'
    },{
        name: 'transferred',
        type: 'int'
    },{
        name: 'elapsed',
        type: 'int'
    },{
        name: 'remaining',
        type: 'int'
    },{
        name: 'speed',
        type: 'int'
    },{
        name: 'percent',
        type: 'double'
    },{
        name: 'crypt',
        type: 'bool'
    }]
});
Ext.define('GibsonOS.module.transfer.index.model.View', {
    extend: 'GibsonOS.data.Model',
    fields: [{
        name: 'size',
        type: 'int'
    },{
        name: 'name',
        type: 'string'
    },{
        name: 'decryptedName',
        type: 'string'
    },{
        name: 'type',
        type: 'string'
    },{
        name: 'modified',
        type: 'int'
    },{
        name: 'hidden',
        type: 'bool'
    }]
});
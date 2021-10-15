Ext.define('GibsonOS.module.transfer.session.model.Grid', {
    extend: 'GibsonOS.data.Model',
    fields: [{
        name: 'id',
        type: 'int'
    },{
        name: 'name',
        type: 'string'
    },{
        name: 'url',
        type: 'string'
    },{
        name: 'port',
        type: 'int'
    },{
        name: 'protocol',
        type: 'string'
    },{
        name: 'user',
        type: 'string'
    },{
        name: 'remotePath',
        type: 'string'
    },{
        name: 'localPath',
        type: 'string'
    },{
        name: 'hasPassword',
        type: 'bool'
    },{
        name: 'onlyForThisUser',
        type: 'bool'
    }]
});
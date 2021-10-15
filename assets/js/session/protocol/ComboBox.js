Ext.define('GibsonOS.module.transfer.session.protocol.ComboBox', {
    extend: 'GibsonOS.form.ComboBox',
    alias: ['widget.gosModuleTransferSessionProtocolComboBox'],
    itemId: 'transferSessionProtocolComboBox',
    name: 'protocol',
    fieldLabel: 'Protokoll',
    displayField: 'name',
    valueField: 'id',
    store: {
        xtype: 'gosDataStore',
        fields: [{
            name: 'id',
            type: 'string'
        },{
            name: 'name',
            type: 'string'
        }],
        data: [{
            id: 'transfer',
            name: 'FTP'
        },{
            id: 'sftp',
            name: 'SFTP'
        },{
            id: 'webdav',
            name: 'WebDAV'
        },{
            id: 'amazondrive',
            name: 'Amazon Drive'
        }]
    }
});
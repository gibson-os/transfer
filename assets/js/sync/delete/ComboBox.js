Ext.define('GibsonOS.module.transfer.sync.delete.ComboBox', {
    extend: 'GibsonOS.form.ComboBox',
    alias: ['widget.gosModuleTransferSyncDeleteComboBox'],
    itemId: 'transferSyncDeleteComboBox',
    name: 'delete',
    fieldLabel: 'Löschen',
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
            id: 'yes',
            name: 'Ja'
        },{
            id: 'no',
            name: 'Nein'
        },{
            id: 'only',
            name: 'Nur löschen'
        }]
    }
});
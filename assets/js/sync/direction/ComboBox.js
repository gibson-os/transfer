Ext.define('GibsonOS.module.transfer.sync.direction.ComboBox', {
    extend: 'GibsonOS.form.ComboBox',
    alias: ['widget.gosModuleTransferSyncDirectionComboBox'],
    itemId: 'transferSyncDirectionComboBox',
    name: 'direction',
    fieldLabel: 'Richtung',
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
            id: 'up',
            name: 'Upload'
        },{
            id: 'down',
            name: 'Download'
        },{
            id: 'sync',
            name: 'Synchronisieren'
        }]
    }
});
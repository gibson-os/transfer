Ext.define('GibsonOS.module.transfer.sync.Button', {
    extend: 'GibsonOS.Button',
    alias: ['widget.gosModuleTransferSyncButton'],
    itemId: 'transferSyncButton',
    iconCls: 'icon_system system_sync',
    requiredPermission: {
        module: 'transfer',
        task: 'sync',
        action: '',
        permission: GibsonOS.Permission.READ,
        method: 'GET'
    },
    handler: function() {
        new GibsonOS.module.transfer.sync.Window();
    }
});
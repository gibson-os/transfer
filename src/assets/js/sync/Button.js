Ext.define('GibsonOS.module.transfer.sync.Button', {
    extend: 'GibsonOS.Button',
    alias: ['widget.gosModuleTransferSyncButton'],
    itemId: 'transferSyncButton',
    iconCls: 'icon_system system_sync',
    requiredPermission: {
        module: 'transfer',
        task: 'sync',
        action: 'index',
        permission: GibsonOS.Permission.READ
    },
    handler: function() {
        new GibsonOS.module.transfer.sync.Window();
    }
});
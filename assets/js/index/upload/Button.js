Ext.define('GibsonOS.module.transfer.index.upload.Button', {
    extend: 'Ext.menu.Item',
    alias: ['widget.gosModuleTransferIndexUploadButton'],
    itemId: 'transferIndexUploadButton',
    text: 'Upload',
    iconCls: 'icon_system system_upload',
    requiredPermission: {
        task: 'index',
        action: 'upload',
        permission: GibsonOS.Permission.WRITE
    },
    handler: function(crypt) {
        var menu = this.up('#contextMenu');
        var parent = menu.getParent();
        var records = parent.getSelectionModel().getSelection();
        var store = parent.getStore();
        var proxy = store.getProxy();
        var dir = proxy.getReader().jsonData.dir;
        var extraParams = {};

        var remotePath = null;
        var activeTab = GibsonOS.module.transfer.index.fn.getConnectedTransferNeighbor(parent);

        if (activeTab) {
            remotePath = activeTab.down('#transferIndexView').gos.store.getProxy().getReader().jsonData.dir;
            extraParams = activeTab.down('#transferIndexView').gos.store.getProxy().extraParams;
        }

        if (remotePath) {
            var files = [];

            Ext.iterate(records, function(record) {
                files.push(record.get('name'));
            });

            GibsonOS.module.transfer.index.fn.upload(dir, files, remotePath, {
                id: extraParams.id ? extraParams.id : null,
                url: extraParams.url ? extraParams.url : null,
                port: extraParams.port ? extraParams.port : null,
                protocol: extraParams.protocol ? extraParams.protocol : null,
                user: extraParams.user ? extraParams.user : null,
                password: extraParams.password ? extraParams.password : null
            }, crypt);
        }
    },
    menu: [{
        text: 'Verschlüsselt',
        iconCls: 'icon_system system_upload',
        handler: function() {
            var menu = this.up('#contextMenu');
            var button = menu.down('gosModuleTransferIndexUploadButton');
            button.handler(true);
        }
    }]
});
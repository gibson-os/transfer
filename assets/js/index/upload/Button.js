Ext.define('GibsonOS.module.transfer.index.upload.Button', {
    extend: 'Ext.menu.Item',
    alias: ['widget.gosModuleTransferIndexUploadButton'],
    itemId: 'transferIndexUploadButton',
    text: 'Upload',
    iconCls: 'icon_system system_upload',
    requiredPermission: {
        module: 'transfer',
        task: 'index',
        action: 'upload',
        permission: GibsonOS.Permission.WRITE
    },
    handler() {
        this.uploadFunction(false);
    },
    uploadFunction(crypt) {
        const menu = this.up('#contextMenu');
        const parent = menu.getParent();
        const records = parent.getSelectionModel().getSelection();
        const store = parent.getStore();
        const proxy = store.getProxy();
        const dir = proxy.getReader().jsonData.dir;
        let extraParams = {};

        let remotePath = null;
        const activeTab = GibsonOS.module.transfer.index.fn.getConnectedTransferNeighbor(parent);

        if (activeTab) {
            remotePath = activeTab.down('#transferIndexView').gos.store.getProxy().getReader().jsonData.dir;
            extraParams = activeTab.down('#transferIndexView').gos.store.getProxy().extraParams;
        }

        if (remotePath) {
            let files = [];

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
            const menu = this.up('#contextMenu');
            const button = menu.down('gosModuleTransferIndexUploadButton');
            button.uploadFunction(true);
        }
    }]
});
Ext.define('GibsonOS.module.transfer.index.dir.add.Button', {
    extend: 'Ext.menu.Item',
    alias: ['widget.gosModuleTransferIndexDirAddButton'],
    itemId: 'transferIndexDirAddButton',
    text: 'Neuer Ordner',
    iconCls: 'icon16 icon_dir',
    requiredPermission: {
        task: 'index',
        action: 'addDir',
        permission: GibsonOS.Permission.WRITE
    },
    handler() {
        this.addFunction(false);
    },
    addFunction(crypt) {
        const button = this;
        const menu = button.up('#contextMenu');
        const view = menu.getParent();
        const store = view.getStore();
        const proxy = store.getProxy();
        const dir = proxy.getReader().jsonData.dir;
        const extraParams = proxy.extraParams;

        GibsonOS.module.transfer.index.fn.addDir(dir, {
            id: extraParams.id ? extraParams.id : null,
            url: extraParams.url ? extraParams.url : null,
            port: extraParams.port ? extraParams.port : null,
            protocol: extraParams.protocol ? extraParams.protocol : null,
            user: extraParams.user ? extraParams.user : null,
            password: extraParams.password ? extraParams.password : null
        }, function (response) {
            const data = Ext.decode(response.responseText).data;

            view.up().fireEvent('addDir', button, response, dir, data.name);
            view.getStore().add(data);
        }, crypt);
    },
    menu: [{
        text: 'Verschlüsselt',
        iconCls: 'icon16 icon_dir',
        handler() {
            const menu = this.up('#contextMenu');
            const button = menu.down('gosModuleTransferIndexDirAddButton');
            button.addFunction(true);
        }
    }]
});
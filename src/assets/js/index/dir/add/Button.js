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
    handler: function(crypt) {
        var button = this;
        var menu = button.up('#contextMenu');
        var view = menu.getParent();
        var store = view.getStore();
        var proxy = store.getProxy();
        var dir = proxy.getReader().jsonData.dir;
        var extraParams = proxy.extraParams;

        GibsonOS.module.transfer.index.fn.addDir(dir, {
            id: extraParams.id ? extraParams.id : null,
            url: extraParams.url ? extraParams.url : null,
            port: extraParams.port ? extraParams.port : null,
            protocol: extraParams.protocol ? extraParams.protocol : null,
            user: extraParams.user ? extraParams.user : null,
            password: extraParams.password ? extraParams.password : null
        }, function (response) {
            var data = Ext.decode(response.responseText).data;

            view.up().fireEvent('addDir', button, response, dir, data.name);
            view.getStore().add(data);
        }, crypt);
    },
    menu: [{
        text: 'Verschlüsselt',
        iconCls: 'icon16 icon_dir',
        handler: function() {
            var menu = this.up('#contextMenu');
            var button = menu.down('gosModuleTransferIndexDirAddButton');
            button.handler(true);
        }
    }]
});
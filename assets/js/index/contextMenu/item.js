GibsonOS.define('GibsonOS.module.transfer.index.contextMenu.item', [{
    xtype: 'gosModuleTransferIndexDirAddButton'
},('-'), {
    xtype: 'gosModuleTransferIndexDownloadButton',
    gos: {
        default: true
    }
},{
    text: 'Löschen',
    iconCls: 'icon_system system_delete',
    requiredPermission: {
        action: '',
        permission: GibsonOS.Permission.WRITE,
        method: 'DELETE'
    },
    handler: function() {
        var button = this;
        var menu = button.up('#contextMenu');
        var view = menu.getParent();
        var proxy = view.getStore().getProxy();
        var dir = proxy.getReader().jsonData.dir;
        var records = view.getSelectionModel().getSelection();
        var extraParams = proxy.extraParams;

        GibsonOS.module.transfer.index.fn.delete(dir, records, {
            id: extraParams.id ? extraParams.id : null,
            url: extraParams.url ? extraParams.url : null,
            port: extraParams.port ? extraParams.port : null,
            protocol: extraParams.protocol ? extraParams.protocol : null,
            user: extraParams.user ? extraParams.user : null,
            password: extraParams.password ? extraParams.password : null
        }, function(response) {
            view.up().fireEvent('deleteFile', response, dir, records);
            view.getStore().remove(records);
        });
    }
}]);
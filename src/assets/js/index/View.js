Ext.define('GibsonOS.module.transfer.index.View', {
    extend: 'GibsonOS.View',
    alias: ['widget.gosModuleTransferIndexView'],
    requiredPermission: {
        module: 'transfer',
        task: 'index'
    },
    multiSelect: true,
    style: 'background: white;',
    overflowY: 'auto',
    itemSelector: 'div.explorerViewItem',
    trackOver: true,
    overItemCls: 'explorerViewItemOver',
    selectedItemCls: 'explorerViewItemSelected',
    initComponent: function() {
        var me = this;

        me.itemContextMenu = GibsonOS.module.transfer.index.contextMenu.item;
        me.containerContextMenu = GibsonOS.module.transfer.index.contextMenu.container;

        var iconSize = this.gos.data.iconSize;
        var badgeSize = iconSize/2;

        if (iconSize == 48) {
            badgeSize = 16;
        }

        me.tpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="explorerViewItem explorerViewItem' + iconSize + '<tpl if="hidden"> hideItem</tpl>" title="{decryptedName}">',
            '<tpl if="thumb">',
            '<div class="explorerViewItemIcon icon' + iconSize + '" style="background-image: url(data:image/png;base64,{thumb});"></div>',
            '<tpl else>',
            '<div class="explorerViewItemIcon icon_default icon' + iconSize + ' <tpl if="icon &gt; 0">customIcon{icon}<tpl else>icon_{type}</tpl>"></div>',
            '</tpl>',
            '<div class="explorerViewItemBadge">{[GibsonOS.module.explorer.file.fn.renderBadge(values, ' + badgeSize + ')]}</div>',
            '<div class="explorerViewItemName">{decryptedName}</div>',
            '</div>',
            '</tpl>'
        );
        me.itemId = 'transferIndexView' + iconSize;

        me.callParent();
        me.on('itemdblclick', GibsonOS.module.transfer.index.listener.itemDblClick);
        me.on('itemkeydown', function(view, record, item, index, event) {
            if (event.getKey() == Ext.EventObject.DELETE) {
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
            } else if (event.getKey() == Ext.EventObject.RETURN) {
                GibsonOS.module.transfer.index.listener.itemDblClick(view, record);
            } else {
                //GibsonOS.module.explorer.dir.fn.jumpToItem(view, record, index, event);
            }
        });
    }
});
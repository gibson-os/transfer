Ext.define('GibsonOS.module.transfer.index.ContainerGrid', {
    extend: 'GibsonOS.grid.Panel',
    alias: ['widget.gosModuleTransferIndexContainerGrid'],
    itemId: 'transferIndexGrid',
    requiredPermission: {
        module: 'transfer',
        task: 'index'
    },
    multiSelect: true,
    columns: [{
        dataIndex: 'type',
        width: 25,
        renderer: function(value, metaData, record) {
            if (record.get('thumb')) {
                return '<div class="icon icon16" style="background-image: url(data:image/png;base64,' + record.get('thumb') + ');"></div>';
            }

            var icon = 'icon_' + value;

            if (record.get('icon') > 0) {
                icon = 'customIcon' + record.get('icon');
            }

            return '<div class="icon icon16 icon_default ' + icon + '"></div>';
        }
    },{
        header: 'Name',
        dataIndex: 'decryptedName',
        flex: 1
    },{
        header: 'Zuletzt bearbeitet',
        align: 'right',
        dataIndex: 'modified',
        width: 120,
        renderer: function(value) {
            var date = new Date(value * 1000);
            return Ext.Date.format(date, 'Y-m-d H:i');
        }
    },{
        header: 'Größe',
        align: 'right',
        dataIndex: 'size',
        width: 80,
        renderer: function(value) {
            return transformSize(value);
        }
    }],
    viewConfig: {
        getRowClass: function(record) {
            if (record.get('hidden')) {
                return 'hideItem';
            }
        },
        listeners: {
            render: function(view) {
                var grid = view.up('gridpanel');

                grid.dragZone = Ext.create('Ext.dd.DragZone', view.getEl(), {
                    getDragData: function(event) {
                        var proxy = grid.getStore().getProxy();
                        var dir = proxy.getReader().jsonData.dir;
                        var sourceElement = event.getTarget().parentNode.parentNode;
                        var record = view.getRecord(sourceElement);

                        if (
                            record &&
                            sourceElement
                        ) {
                            var clone = sourceElement.cloneNode(true);
                            var moveData = {
                                grid: grid,
                                record: record
                            };

                            if (record.get('type') == 'dir') {
                                var data = {
                                    module: 'explorer',
                                    task: 'index',
                                    action: 'index',
                                    text: record.get('name'),
                                    icon: 'icon_dir',
                                    customIcon: record.get('icon'),
                                    params: {
                                        dir: dir + record.get('name') + '/'
                                    }
                                };

                                moveData.type = 'dir';
                            } else {
                                var data = {
                                    module: 'explorer',
                                    task: 'file',
                                    action: 'download',
                                    text: record.get('name'),
                                    icon: 'icon_' + record.get('type'),
                                    thumb: record.get('thumb'),
                                    params: {
                                        path: dir + record.get('name')
                                    }
                                };

                                moveData.type = 'file';
                            }

                            return grid.dragData = {
                                sourceEl: sourceElement,
                                repairXY: Ext.fly(sourceElement).getXY(),
                                ddel: clone,
                                shortcut: data,
                                moveData: moveData
                            };
                        }
                    },
                    getRepairXY: function() {
                        return this.dragData.repairXY;
                    }
                });
            }
        }
    },
    initComponent: function() {
        var grid = this;

        this.itemContextMenu = GibsonOS.module.transfer.index.contextMenu.item;
        this.containerContextMenu = GibsonOS.module.transfer.index.contextMenu.container;

        this.callParent();

        this.on('itemdblclick', GibsonOS.module.transfer.index.listener.itemDblClick);
        this.on('cellkeydown', function(table, td, cellIndex, record, tr, rowIndex, event) {
            if (event.getKey() == Ext.EventObject.DELETE) {
                var proxy = grid.getStore().getProxy();
                var dir = proxy.getReader().jsonData.dir;
                var extraParams = proxy.extraParams;
                var records = grid.getSelectionModel().getSelection();

                GibsonOS.module.transfer.index.fn.delete(dir, records, {
                    id: extraParams.id ? extraParams.id : null,
                    url: extraParams.url ? extraParams.url : null,
                    port: extraParams.port ? extraParams.port : null,
                    protocol: extraParams.protocol ? extraParams.protocol : null,
                    user: extraParams.user ? extraParams.user : null,
                    password: extraParams.password ? extraParams.password : null
                }, function(response) {
                    grid.up().fireEvent('deleteFile', response, dir, records);
                    grid.getStore().remove(records);
                });
            } else if (event.getKey() == Ext.EventObject.RETURN) {
                GibsonOS.module.transfer.index.listener.itemDblClick(grid, record);
            } else {
                //GibsonOS.module.explorer.dir.fn.jumpToItem(grid, record, rowIndex, event);
            }
        });
    }
});
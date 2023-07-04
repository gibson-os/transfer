Ext.define('GibsonOS.module.transfer.index.Tree', {
    extend: 'GibsonOS.tree.Panel',
    alias: ['widget.gosModuleTransferIndexTree'],
    itemId: 'transferIndexTree',
    requiredPermission: {
        module: 'transfer',
        task: 'index'
    },
    useArrows: true,
    itemContextMenu: [{
        xtype: 'gosModuleTransferIndexDirAddButton'
    },('-'),{
        xtype: 'gosModuleTransferIndexDownloadButton',
        handler: function() {
            var menu = this.up('#contextMenu');
            var parent = menu.getParent();
            var store = parent.getStore();
            var record = menu.getRecord();
            var proxy = store.getProxy();
            var extraParams = proxy.extraParams;

            var localPath = null;
            var activeTab = GibsonOS.module.transfer.index.fn.getActiveNeighborTab(parent);

            if (activeTab) {
                if (activeTab.getXType() == 'gosModuleExplorerIndexPanel') {
                    localPath = activeTab.down('#explorerIndexView').gos.store.getProxy().getReader().jsonData.dir;
                } // Sonst FTP
            }

            if (localPath) {
                GibsonOS.module.transfer.index.fn.download(record.get('id'), null, localPath, {
                    id: extraParams.id ? extraParams.id : null,
                    url: extraParams.url ? extraParams.url : null,
                    port: extraParams.port ? extraParams.port : null,
                    protocol: extraParams.protocol ? extraParams.protocol : null,
                    user: extraParams.user ? extraParams.user : null,
                    password: extraParams.password ? extraParams.password : null
                });
            }
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
            var parent = menu.getParent();
            var dir = menu.getRecord().get('id');
            var proxy = parent.getStore().getProxy();
            var extraParams = proxy.extraParams;

            GibsonOS.module.transfer.index.fn.delete(dir, [], {
                id: extraParams.id ? extraParams.id : null,
                url: extraParams.url ? extraParams.url : null,
                port: extraParams.port ? extraParams.port : null,
                protocol: extraParams.protocol ? extraParams.protocol : null,
                user: extraParams.user ? extraParams.user : null,
                password: extraParams.password ? extraParams.password : null
            }, function(response) {
                var node = parent.getSelectionModel().getLastSelected();
                parent.fireEvent('deleteDir', response, node);
                node.remove();
            });
        }
    }],
    initComponent: function() {
        var me = this;

        me.store = new GibsonOS.module.transfer.index.store.Tree({
            gos: {
                data: {
                    tree: this
                    /*extraParams: {
                     dir: this.gosData.dir
                     }*/
                }
            }
        });

        me.callParent();

        me.on('cellkeydown', function(view, td, cellIndex, record, tr, rowIndex, event) {
            if (event.getKey() == Ext.EventObject.DELETE) {
                var proxy = me.getStore().getProxy();
                var extraParams = proxy.extraParams;

                GibsonOS.module.transfer.index.fn.delete(record.get('id'), [], {
                    id: extraParams.id ? extraParams.id : null,
                    url: extraParams.url ? extraParams.url : null,
                    port: extraParams.port ? extraParams.port : null,
                    protocol: extraParams.protocol ? extraParams.protocol : null,
                    user: extraParams.user ? extraParams.user : null,
                    password: extraParams.password ? extraParams.password : null
                }, function(response) {
                    tree.fireEvent('deleteDir', response, record);
                    record.remove();
                });
            }
        });
    },
    /*itemContextMenu: [{
     text: 'Neuer Ordner',
     iconCls: 'icon16 icon_dir',
     requiredPermission: {
     action: 'save',
     permission: GibsonOS.Permission.WRITE
     },
     handler: function() {
     var button = this;
     var menu = this.up('#contextMenu');
     var parent = menu.getParent();
     var store = parent.getStore();
     var proxy = store.getProxy();
     var dir = menu.getRecord().get('id');

     GibsonOS.module.explorer.dir.add(dir, function(response, text) {
     if (
     parent.gosData &&
     parent.gosData.addDir
     ) {
     parent.gosData.addDir(button, response, dir, text);
     }

     var node = parent.getSelectionModel().getLastSelected();
     node.appendChild({
     iconCls: 'icon16 icon_dir',
     id: dir + text + '/',
     text: text
     });
     });
     }
     },('-'),{
     text: 'Umbennen',
     requiredPermission: {
     action: 'rename',
     permission: GibsonOS.Permission.WRITE
     },
     handler: function() {
     Ext.MessageBox.prompt('Neuer Name', 'Neuer Name', function(btn, text) {
     if (btn == 'ok') {
     record.set('text', text);
     saveDesktop();
     }
     }, window, false, record.get('text'));
     }
     },{
     text: 'Löschen',
     iconCls: 'icon_system system_delete',
     requiredPermission: {
     action: 'delete',
     permission: GibsonOS.Permission.DELETE
     },
     handler: function() {
     }
     },{
     text: 'Download (zip)',
     iconCls: 'icon_system system_down',
     requiredPermission: {
     action: 'download',
     permission: GibsonOS.Permission.READ
     },
     handler: function() {
     }
     }],*/
    viewConfig: {
        listeners: {
            render: function(view) {
                var tree = view.up('treepanel');

                tree.dragZone = Ext.create('Ext.dd.DragZone', tree.getEl(), {
                    getDragData: function(event) {
                        var tree = view.up('treepanel');
                        var proxy = tree.getStore().getProxy();
                        var dir = proxy.getReader().jsonData.dir;
                        var sourceElement = event.getTarget().parentNode.parentNode.parentNode;

                        if (sourceElement) {
                            var record = view.getRecord(sourceElement);
                            var clone = sourceElement.cloneNode(true);
                            var data = {
                                module: 'transfer',
                                task: 'index',
                                action: 'index',
                                text: record.get('text'),
                                icon: 'icon_dir',
                                params: {
                                    dir: record.get('id')
                                }
                            };

                            return tree.dragData = {
                                sourceEl: sourceElement,
                                repairXY: Ext.fly(sourceElement).getXY(),
                                ddel: clone,
                                shortcut: data
                            };
                        }
                    },
                    getRepairXY: function() {
                        return this.dragData.repairXY;
                    }
                });
                tree.dropZone = GibsonOS.dropZones.add(tree.getEl(), {
                    getTargetFromEvent: function(event) {
                        return event.getTarget('.x-grid-row');
                    },
                    onNodeOver : function(target, dd, event, data){
                        if (data.moveData) {
                            return Ext.dd.DropZone.prototype.dropAllowed;
                        }

                        return Ext.dd.DropZone.prototype.dropNotAllowed;
                    },
                    onNodeDrop: function(target, dd, event, data) {
                        data = data.moveData;
                        data.tree = tree;
                        data.to = view.getRecord(target).get('id');

                        GibsonOS.module.explorer.file.fn.move(data);
                    }
                });
            }
        }
    }
});
Ext.define('GibsonOS.module.transfer.index.Panel', {
    extend: 'GibsonOS.Panel',
    alias: ['widget.gosModuleTransferIndexPanel'],
    itemId: 'transferIndexPanel',
    layout: 'border',
    initComponent: function() {
        var panel = this;

        this.gos.data.path = [];
        this.gos.data.decryptedPath = [];
        this.gos.data.homePath = '/';
        this.gos.data.dirHistory = [];
        this.gos.data.dirHistoryPointer = -1;
        this.gos.data.updateBottomBar = function() {
            var view = panel.down('#transferIndexView');

            panel.down('#transferIndexSize').setText('Größe: ' + transformSize(view.gos.data.fileSize));
            panel.down('#transferIndexFiles').setText('Dateien: ' + view.gos.data.fileCount);
            panel.down('#transferIndexDirs').setText('Ordner: ' + view.gos.data.dirCount);
        };

        this.items = [{
            xtype: 'gosPanel',
            region: 'center',
            layout: 'border',
            itemId: 'transferIndexPanelCenter',
            disabled: true,
            tbar: [{
                xtype: 'gosButton',
                itemId: 'transferIndexBackButton',
                iconCls: 'icon_system system_back',
                requiredPermission: {
                    action:'read',
                    permission: GibsonOS.Permission.READ
                },
                disabled: true,
                handler: function() {
                    var panel = this.up('#transferIndexPanel');

                    if (panel.gos.data.dirHistoryPointer > 0) {
                        panel.gos.data.dirHistoryPointer--;

                        var viewStore = panel.down('#transferIndexView').gos.store;
                        viewStore.getProxy().extraParams.dir = panel.gos.data.dirHistory[panel.gos.data.dirHistoryPointer];
                        viewStore.load();
                    }
                }
            },{
                xtype: 'gosButton',
                itemId: 'transferIndexNextButton',
                iconCls: 'icon_system system_next',
                requiredPermission: {
                    action:'read',
                    permission: GibsonOS.Permission.READ
                },
                disabled: true,
                handler: function() {
                    var panel = this.up('#transferIndexPanel');

                    if (panel.gos.data.dirHistoryPointer < panel.gos.data.dirHistory.length-1) {
                        panel.gos.data.dirHistoryPointer++;

                        var viewStore = panel.down('#transferIndexView').gos.store;
                        viewStore.getProxy().extraParams.dir = panel.gos.data.dirHistory[panel.gos.data.dirHistoryPointer];
                        viewStore.load();
                    }
                }
            },{
                xtype: 'gosButton',
                itemId: 'transferIndexUpButton',
                iconCls: 'icon_system system_up',
                requiredPermission: {
                    action:'read',
                    permission: GibsonOS.Permission.READ
                },
                handler: function() {
                    var panel = this.up('#transferIndexPanel');

                    if (panel.gos.data.path.length > 1) {
                        var pathString = '';

                        for (var i = 0; i < panel.gos.data.path.length-1; i++) {
                            pathString += panel.gos.data.path[i] + '/';
                        }

                        var viewStore = panel.down('#transferIndexView').gos.store;
                        viewStore.getProxy().extraParams.dir = pathString;
                        viewStore.load();
                    }
                }
            },('-'),{
                xtype: 'gosPanel',
                itemId: 'transferIndexPath',
                frame: false,
                plain: false,
                flex: 0
            },('->'),{
                xtype: 'gosFormTextfield',
                itemId: 'transferIndexSearch',
                enableKeyEvents: true,
                hideLabel: true,
                gos: {
                    data: {
                        searchActive: false,
                        stopSearch: false
                    }
                }
            },{
                xtype: 'gosButton',
                itemId: 'transferIndexViewButton',
                iconCls: 'icon_system system_view_details',
                menu: [{
                    text: 'Sehr Kleine Symbole',
                    iconCls: 'icon_system system_view_very_small_icons',
                    handler: function() {
                        var panel = this.up('#transferIndexPanel');
                        panel.down('#transferIndexView48').hide();
                        panel.down('#transferIndexView64').hide();
                        panel.down('#transferIndexView128').hide();
                        panel.down('#transferIndexView256').hide();
                        panel.down('#transferIndexGrid').hide();
                        panel.down('#transferIndexView32').show();
                        panel.down('#transferIndexView32').fireEvent('selectionchange', view.down('#transferIndexView32').getSelectionModel());
                    }
                },{
                    text: 'Kleine Symbole',
                    iconCls: 'icon_system system_view_small_icons',
                    handler: function() {
                        var panel = this.up('#transferIndexPanel');
                        panel.down('#transferIndexView32').hide();
                        panel.down('#transferIndexView64').hide();
                        panel.down('#transferIndexView128').hide();
                        panel.down('#transferIndexView256').hide();
                        panel.down('#transferIndexGrid').hide();
                        panel.down('#transferIndexView48').show();
                        panel.down('#transferIndexView48').fireEvent('selectionchange', view.down('#transferIndexView48').getSelectionModel());
                    }
                },{
                    text: 'Mittlere Symbole',
                    iconCls: 'icon_system system_view_middle_icons',
                    handler: function() {
                        var panel = this.up('#transferIndexPanel');
                        panel.down('#transferIndexView32').hide();
                        panel.down('#transferIndexView48').hide();
                        panel.down('#transferIndexView128').hide();
                        panel.down('#transferIndexView256').hide();
                        panel.down('#transferIndexGrid').hide();
                        panel.down('#transferIndexView64').show();
                        panel.down('#transferIndexView64').fireEvent('selectionchange', view.down('#transferIndexView64').getSelectionModel());
                    }
                },{
                    text: 'Große Symbole',
                    iconCls: 'icon_system system_view_big_icons',
                    handler: function() {
                        var panel = this.up('#transferIndexPanel');
                        panel.down('#transferIndexView32').hide();
                        panel.down('#transferIndexView48').hide();
                        panel.down('#transferIndexView64').hide();
                        panel.down('#transferIndexView256').hide();
                        panel.down('#transferIndexGrid').hide();
                        panel.down('#transferIndexView128').show();
                        panel.down('#transferIndexView128').fireEvent('selectionchange', view.down('#transferIndexView128').getSelectionModel());
                    }
                },{
                    text: 'Sehr Große Symbole',
                    iconCls: 'icon_system system_view_very_big_icons',
                    handler: function() {
                        var panel = this.up('#transferIndexPanel');
                        panel.down('#transferIndexView32').hide();
                        panel.down('#transferIndexView48').hide();
                        panel.down('#transferIndexView64').hide();
                        panel.down('#transferIndexView128').hide();
                        panel.down('#transferIndexGrid').hide();
                        panel.down('#transferIndexView256').show();
                        panel.down('#transferIndexView256').fireEvent('selectionchange', view.down('#transferIndexView256').getSelectionModel());
                    }
                },{
                    text: 'Liste',
                    iconCls: 'icon_system system_view_details',
                    handler: function() {
                        var panel = this.up('#transferIndexPanel');
                        panel.down('#transferIndexView32').hide();
                        panel.down('#transferIndexView48').hide();
                        panel.down('#transferIndexView64').hide();
                        panel.down('#transferIndexView128').hide();
                        panel.down('#transferIndexView256').hide();
                        panel.down('#transferIndexGrid').show();
                        panel.down('#transferIndexGrid').fireEvent('selectionchange', view.down('#transferIndexGrid').getSelectionModel());
                    }
                }]
            }],
            items: [{
                xtype: 'gosModuleTransferIndexContainer',
                region: 'center',
                flex: 0,
                gos: {
                    data: {
                        dir: this.gos.data.dir
                    }
                }
            },{
                xtype: 'gosModuleTransferIndexTree',
                region: 'west',
                flex: 0,
                collapsible: true,
                split: true,
                width: 250,
                hideCollapseTool: true,
                header: false,
                listeners: {
                    itemclick: function(tree, record, item, index, event, options) {
                        var panel = tree.up('#transferIndexPanel');
                        panel.gos.data.dirHistory.dirHistory = panel.gos.data.dirHistory.slice(0, panel.gos.data.dirHistoryPointer+1);

                        var viewStore = panel.down('#transferIndexView').gos.store;
                        viewStore.getProxy().setExtraParam('dir', record.data.id);
                        viewStore.load();
                    }
                }
            },{
                xtype: 'gosModuleTransferIndexTransferTabPanel',
                region: 'south',
                flex: 0,
                collapsible: true,
                split: true,
                height: 200,
                hideCollapseTool: true,
                collapsed: true,
                header: false
            }]
        }];

        var keyUpListener = function(field, event) {
            var toolBar = field.up();

            if (event.getKey() == Ext.EventObject.RETURN) {
                this.up('gosToolbar').down('#transferIndexConnectButton').toggle();
            } else if (
                toolBar.down('#transferIndexIdField').getValue() &&
                (
                event.getKey() > 31 ||
                event.getKey() == 8
                )
            ) {
                toolBar.down('#transferIndexIdField').setValue(null);
                toolBar.down('#transferIndexLocalPathField').setValue(null);
                toolBar.down('#transferIndexPasswordField').setValue(null);
            }
        };

        this.dockedItems = [{
            xtype: 'gosToolbar',
            dock: 'top',
            items: [{
                xtype: 'gosModuleTransferSessionAutoComplete',
                width: 120,
                hideLabel: true,
                emptyText: 'Verbindung',
                listeners: {
                    select: function(combo, records, options) {
                        var record = records[0];
                        var toolBar = combo.up();

                        toolBar.down('#transferIndexIdField').setValue(record.get('id'));
                        toolBar.down('#transferIndexLocalPathField').setValue(record.get('localPath'));
                        toolBar.down('#transferIndexUrlField').setValue(record.get('url'));
                        toolBar.down('#transferIndexPortField').setValue(record.get('port'));
                        toolBar.down('#transferIndexProtocolField').setValue(record.get('protocol'));
                        toolBar.down('#transferIndexUserField').setValue(record.get('user'));
                        toolBar.down('#transferIndexPasswordField').setValue(record.get('hasPassword') ? 'Password' : null);
                    }
                }
            },{
                iconCls: 'icon_system system_save',
                requiredPermission: {
                    action: 'save',
                    permission: GibsonOS.Permission.WRITE + GibsonOS.Permission.MANAGE
                },
                handler: function() {
                    new GibsonOS.module.transfer.session.Window();
                }
            },('-'),{
                xtype: 'gosFormHidden',
                itemId: 'transferIndexIdField'
            },{
                xtype: 'gosFormHidden',
                itemId: 'transferIndexLocalPathField'
            },{
                xtype: 'gosModuleTransferSessionProtocolComboBox',
                itemId: 'transferIndexProtocolField',
                hideLabel: true,
                width: 70,
                emptyText: 'Protokoll',
                enableKeyEvents: true,
                listeners: {
                    keyup: keyUpListener,
                    afterrender: function(combo) {
                        var store = combo.getStore();

                        store.remove(store.getById('amazondrive'));
                    }
                }
            },{
                xtype: 'gosFormTextfield',
                itemId: 'transferIndexUrlField',
                hideLabel: true,
                width: 70,
                emptyText: 'URL',
                enableKeyEvents: true,
                listeners: {
                    keyup: keyUpListener
                }
            },{
                xtype: 'gosFormNumberfield',
                itemId: 'transferIndexPortField',
                hideLabel: true,
                width: 50,
                emptyText: 'Port',
                enableKeyEvents: true,
                listeners: {
                    keyup: keyUpListener
                }
            },{
                xtype: 'gosFormTextfield',
                itemId: 'transferIndexUserField',
                hideLabel: true,
                width: 90,
                emptyText: 'Benutzername',
                enableKeyEvents: true,
                listeners: {
                    keyup: keyUpListener
                }
            },{
                xtype: 'gosFormTextfield',
                itemId: 'transferIndexPasswordField',
                hideLabel: true,
                width: 80,
                inputType: 'password',
                emptyText: 'Passwort',
                enableKeyEvents: true,
                listeners: {
                    keyup: keyUpListener
                }
            }, {
                itemId: 'transferIndexConnectButton',
                text: 'Verbinden',
                enableToggle: true,
                requiredPermission: {
                    permission: GibsonOS.Permission.READ
                },
                listeners: {
                    toggle: function (button, pressed) {
                        var autoCompleteField = panel.down('#transferSessionAutoComplete');
                        var idField = panel.down('#transferIndexIdField');
                        var localPathField = panel.down('#transferIndexLocalPathField');
                        var urlField = panel.down('#transferIndexUrlField');
                        var portField = panel.down('#transferIndexPortField');
                        var protocolField = panel.down('#transferIndexProtocolField');
                        var userField = panel.down('#transferIndexUserField');
                        var passwordField = panel.down('#transferIndexPasswordField');
                        var syncButton = panel.down('#transferSyncButton');

                        if (pressed) {
                            autoCompleteField.disable();
                            urlField.disable();
                            portField.disable();
                            protocolField.disable();
                            userField.disable();
                            passwordField.disable();
                            syncButton.enable();

                            var store = panel.down('#transferIndexView').gos.store;
                            var proxy = store.getProxy();

                            proxy.setExtraParam('id', idField.getValue());
                            proxy.setExtraParam('url', urlField.getValue());
                            proxy.setExtraParam('port', portField.getValue());
                            proxy.setExtraParam('protocol', protocolField.getValue());
                            proxy.setExtraParam('user', userField.getValue());
                            proxy.setExtraParam('password', passwordField.getValue());
                            proxy.setExtraParam('dir', null);

                            var treeStore = panel.down('#transferIndexTree').getStore();
                            var treeProxy = treeStore.getProxy();

                            treeProxy.setExtraParam('id', idField.getValue());
                            treeProxy.setExtraParam('url', urlField.getValue());
                            treeProxy.setExtraParam('port', portField.getValue());
                            treeProxy.setExtraParam('protocol', protocolField.getValue());
                            treeProxy.setExtraParam('user', userField.getValue());
                            treeProxy.setExtraParam('password', passwordField.getValue());
                            treeProxy.setExtraParam('dir', null);

                            panel.down('#transferIndexTransferTabPanel').items.each(function (item) {
                                var transferStore = item.getStore();
                                var transferProxy = transferStore.getProxy();

                                transferProxy.setExtraParam('id', idField.getValue());
                                transferProxy.setExtraParam('url', urlField.getValue());
                                transferProxy.setExtraParam('port', portField.getValue());
                                transferProxy.setExtraParam('protocol', protocolField.getValue());
                                transferProxy.setExtraParam('user', userField.getValue());
                                transferProxy.setExtraParam('password', passwordField.getValue());

                                if (
                                    item.up().getCollapsed() === false &&
                                    item.isVisible()
                                ) {
                                    transferStore.load();
                                }
                            });

                            store.load();
                            var activeTab = GibsonOS.module.transfer.index.fn.getActiveNeighborTab(panel);

                            if (
                                activeTab &&
                                activeTab.getXType() == 'gosModuleExplorerIndexPanel' &&
                                localPathField.getValue()
                            ) {
                                var localStore = activeTab.down('#explorerIndexView').gos.store;
                                var localProxy = localStore.getProxy();

                                if (localProxy.extraParams.dir != localPathField.getValue()) {
                                    localProxy.setExtraParam('dir', localPathField.getValue());
                                    localStore.load();
                                }
                            }
                        } else {
                            panel.down('#transferIndexPanelCenter').disable();

                            autoCompleteField.enable();
                            idField.enable();
                            urlField.enable();
                            portField.enable();
                            protocolField.enable();
                            userField.enable();
                            passwordField.enable();
                            syncButton.disable();
                        }
                    }
                }
            },('-'),{
                xtype: 'gosModuleTransferSyncButton',
                disabled: true
            }]
        },{
            xtype: 'gosToolbar',
            dock: 'bottom',
            items: [{
                itemId: 'transferIndexSize',
                xtype: 'gosToolbarTextItem',
                text: 'Größe: 0 ' + sizeUnits[0]
            },('-'),{
                itemId: 'transferIndexFiles',
                xtype: 'gosToolbarTextItem',
                text: 'Dateien: 0'
            },('-'),{
                itemId: 'transferIndexDirs',
                xtype: 'gosToolbarTextItem',
                text: 'Ordner: 0'
            },{
                xtype: 'tbseparator',
                itemId: 'transferIndexUploadSeparator',
                hidden: true
            },{
                xtype: 'progressbar',
                itemId: 'transferIndexUploadFile',
                hidden: true,
                width: 250
            },{
                xtype: 'progressbar',
                itemId: 'transferIndexUploadTotal',
                hidden: true,
                width: 150
            }]
        }];

        this.callParent();

        var selectionChange = function(selection, records) {
            panel.down('#transferIndexGrid').getSelectionModel().select(records, false, true);
            panel.down('#transferIndexView32').getSelectionModel().select(records, false, true);
            panel.down('#transferIndexView48').getSelectionModel().select(records, false, true);
            panel.down('#transferIndexView64').getSelectionModel().select(records, false, true);
            panel.down('#transferIndexView128').getSelectionModel().select(records, false, true);
            panel.down('#transferIndexView256').getSelectionModel().select(records, false, true);
        };

        this.down('#transferIndexGrid').on('selectionchange', selectionChange);
        this.down('#transferIndexView32').on('selectionchange', selectionChange);
        this.down('#transferIndexView48').on('selectionchange', selectionChange);
        this.down('#transferIndexView64').on('selectionchange', selectionChange);
        this.down('#transferIndexView128').on('selectionchange', selectionChange);
        this.down('#transferIndexView256').on('selectionchange', selectionChange);

        this.down('#transferIndexSearch').on('keyup', function(textfield) {
            var search = textfield.getValue().toLowerCase();
            var viewStore = panel.down('#transferIndexView').gos.store;

            if (textfield.gos.data.searchActive) {
                textfield.gos.data.stopSearch = true;
            } else {
                textfield.gos.data.stopSearch = false;
                textfield.gos.data.searchActive = true;
            }

            viewStore.each(function(record) {
                if (textfield.gos.data.stopSearch) {
                    textfield.gos.data.stopSearch = false;
                    textfield.gos.data.searchActive = false;
                    return false;
                }

                if (record.get('name').toLowerCase().indexOf(search) == -1) {
                    record.set('hidden', true);
                } else {
                    record.set('hidden', false);
                }
            });

            textfield.gos.data.searchActive = false;
        });
        this.down('#transferIndexView').gos.store.on('beforeload', function(store, operation, options) {
            panel.down('#transferIndexSearch').setValue(null);
        });
        this.down('#transferIndexView').gos.store.on('load', function(store, operation, options) {
            var data = store.getProxy().getReader().jsonData;

            if (data.failure) {
                panel.down('#transferIndexConnectButton').toggle(false);
            } else if (data.success) {
                var dir = store.getProxy().getReader().jsonData.dir;

                panel.down('#transferIndexPanelCenter').enable();

                panel.gos.data.dir = dir;
                panel.gos.data.path = store.getProxy().getReader().jsonData.path;
                panel.gos.data.decryptedPath = store.getProxy().getReader().jsonData.decryptedPath;
                panel.gos.data.updateBottomBar();

                var toolbarPath = panel.down('#transferIndexPath');
                toolbarPath.removeAll();

                for (var i = 0; i < panel.gos.data.path.length; i++) {
                    toolbarPath.add({
                        xtype: 'gosButton',
                        text: panel.gos.data.decryptedPath[i] + '/',
                        gos : {
                            encryptedName: panel.gos.data.path[i]
                        },
                        listeners: {
                            click: function(button, event, options) {
                                var pathString = '';

                                for (var i = 0; i < toolbarPath.items.items.length; i++) {
                                    var item = toolbarPath.items.items[i];
                                    pathString += item.gos.encryptedName + '/';

                                    if (item.id == button.id) {
                                        break;
                                    }
                                }

                                panel.gos.data.dirHistory = panel.gos.data.dirHistory.slice(0, panel.gos.data.dirHistoryPointer+1);
                                store.getProxy().extraParams.dir = pathString;
                                store.load();
                            }
                        }
                    });
                }

                // Dir History
                if (
                    panel.gos.data.dirHistory.length == 0 ||
                    (
                    dir != panel.gos.data.dirHistory[panel.gos.data.dirHistory.length-1] &&
                    panel.gos.data.dirHistory.length-1 == panel.gos.data.dirHistoryPointer
                    )
                ) {
                    panel.gos.data.dirHistory.push(dir);
                    panel.gos.data.dirHistoryPointer++;
                }

                if (panel.gos.data.dirHistoryPointer == panel.gos.data.dirHistory.length-1) {
                    panel.down('#transferIndexNextButton').disable();
                } else {
                    panel.down('#transferIndexNextButton').enable();
                }

                if (panel.gos.data.dirHistoryPointer == 0) {
                    panel.down('#transferIndexBackButton').disable();
                } else {
                    panel.down('#transferIndexBackButton').enable();
                }

                // Tree
                var tree = panel.down('#transferIndexTree');
                var node = tree.getStore().getNodeById(dir);

                if (!node) {
                    tree.getStore().getProxy().setExtraParam('dir', dir);
                    tree.getStore().load();
                } else {
                    tree.getSelectionModel().select(node, false, true);
                    tree.getView().focusRow(node);
                }
            }
        });
    }
});
Ext.define('GibsonOS.module.transfer.index.transfer.TabPanel', {
    extend: 'GibsonOS.TabPanel',
    alias: ['widget.gosModuleTransferIndexTransferTabPanel'],
    activeTab: 0,
    itemId: 'transferIndexTransferTabPanel',
    requiredPermission: {
        module: 'transfer',
        task: 'index'
    },
    initComponent: function() {
        this.items = [{
            xtype: 'gosModuleTransferIndexTransferGrid',
            title: 'Aktiv',
            gos: {
                data: {
                    type: 'active'
                }
            }
        },{
            xtype: 'gosModuleTransferIndexTransferGrid',
            title: 'Fertig',
            gos: {
                data: {
                    type: 'finished'
                }
            }
        },{
            xtype: 'gosModuleTransferIndexTransferGrid',
            title: 'Fehlerhaft',
            gos: {
                data: {
                    type: 'error'
                }
            }
        }];

        this.callParent();

        var clearAutoRefresh = function(panel) {
            if (panel.gos.data.autoRefresh) {
                panel.down('#transferIndexTransferRefreshButton').toggle(false, true);
                window.clearInterval(panel.gos.data.autoRefresh);
            }
        };

        this.on('tabchange', function(tabPanel, newCard, oldCard, options) {
            clearAutoRefresh(oldCard);
        });
        this.on('collapse', function(tabPanel, options) {
            clearAutoRefresh(tabPanel.getActiveTab());
        });
    }
});
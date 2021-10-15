Ext.define('GibsonOS.module.transfer.index.App', {
    extend: 'GibsonOS.App',
    alias: ['widget.gosModuleTransferIndexApp'],
    title: 'Transfer',
    appIcon: 'icon_exe',
    width: 1200,
    height: 600,
    layout: 'border',
    requiredPermission: {
        module: 'transfer',
        task: 'index'
    },
    initComponent: function() {
        var app = this;

        this.items = [{
            xtype: 'gosModuleTransferIndexTabPanel',
            itemId: 'transferIndexTabPanelLeft',
            region: 'center',
            gos: {
                data: {
                    dir: this.gos.data.dir ? this.gos.data.dir : null,
                    side: 'left'
                }
            }
        },{
            xtype: 'gosModuleTransferIndexTabPanel',
            itemId: 'transferIndexTabPanelRight',
            region: 'east',
            activeTab: 1,
            gos: {
                data: {
                    dir: this.gos.data.dir ? this.gos.data.dir : null,
                    side: 'right'
                }
            }
        }];
        this.id = 'gosModuleTransferIndexApp' + Ext.id();

        this.callParent();
    }
});
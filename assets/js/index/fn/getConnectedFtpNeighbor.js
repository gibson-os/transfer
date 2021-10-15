GibsonOS.define('GibsonOS.module.transfer.index.fn.getConnectedTransferNeighbor', function(view) {
    var activeTab = GibsonOS.module.transfer.index.fn.getActiveNeighborTab(view);

    if (activeTab) {
        if (activeTab.getXType() == 'gosModuleTransferIndexPanel') {
            if (!activeTab.down('#transferIndexConnectButton').pressed) {
                GibsonOS.MessageBox.show({msg: 'Remote nicht verbunden!'});

                return false;
            }

            return activeTab;
        }
    }

    return false;
});
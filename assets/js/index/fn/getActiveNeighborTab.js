GibsonOS.define('GibsonOS.module.transfer.index.fn.getActiveNeighborTab', function(view) {
    var itemId = view.up('gosModuleTransferIndexTabPanel').getItemId();
    var neighbor = null;

    if (itemId.search(/left/i) > -1) {
        neighbor = view.up('#app').down('#transferIndexTabPanelRight');
    } else if (itemId.search(/right/i) > -1) {
        neighbor = view.up('#app').down('#transferIndexTabPanelLeft');
    }

    if (!neighbor) {
        return false;
    }

    return neighbor.getActiveTab();
});
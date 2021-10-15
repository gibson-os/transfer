Ext.define('GibsonOS.module.transfer.session.AutoComplete', {
    extend: 'GibsonOS.form.AutoComplete',
    alias: ['widget.gosModuleTransferSessionAutoComplete'],
    itemId: 'transferSessionAutoComplete',
    url: baseDir + 'transfer/session/autoComplete',
    model: 'GibsonOS.module.transfer.session.model.Grid',
    requiredPermission: {
        module: 'transfer',
        task: 'session',
        action: 'autoComplete',
        permission: GibsonOS.Permission.READ
    }
});
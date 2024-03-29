Ext.define('GibsonOS.module.transfer.session.AutoComplete', {
    extend: 'GibsonOS.module.core.parameter.type.AutoComplete',
    alias: ['widget.gosModuleTransferSessionAutoComplete'],
    itemId: 'transferSessionAutoComplete',
    requiredPermission: {
        module: 'transfer',
        task: 'session',
        action: '',
        permission: GibsonOS.Permission.READ,
        method: 'GET'
    },
    parameterObject: {
        config: {
            model: 'GibsonOS.module.transfer.session.model.Grid',
            autoCompleteClassname: 'GibsonOS\\Module\\Transfer\\AutoComplete\\SessionAutoComplete',
            parameters: {}
        }
    },
});
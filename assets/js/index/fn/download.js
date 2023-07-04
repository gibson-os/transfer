GibsonOS.define('GibsonOS.module.transfer.index.fn.download', function(dir, files, localPath, session) {
    var params = {
        dir: dir,
        localPath: localPath,
        id: session.id ? session.id : null,
        url: session.url ? session.url : null,
        port: session.port ? session.port : null,
        protocol: session.protocol ? session.protocol : null,
        user: session.user ? session.user : null,
        password: session.password ? session.password : null
    };

    if (files) {
        params['files[]'] = files;
    }

    GibsonOS.Ajax.request({
        url: baseDir + 'transfer/index/download',
        method: 'GET',
        timeout: 36000000,
        params: params,
        success: function(response) {
            GibsonOS.MessageBox.show({
                title: 'Fertig!',
                msg: 'Downloads wurden hinzugefügt.',
                type: GibsonOS.MessageBox.type.INFO
            });
        }
    });
});
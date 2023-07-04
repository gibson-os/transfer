GibsonOS.define('GibsonOS.module.transfer.index.fn.upload', function(dir, files, remotePath, session, crypt) {
    var params = {
        dir: dir,
        remotePath: remotePath,
        id: session.id ? session.id : null,
        url: session.url ? session.url : null,
        port: session.port ? session.port : null,
        protocol: session.protocol ? session.protocol : null,
        user: session.user ? session.user : null,
        password: session.password ? session.password : null,
        crypt: crypt
    };

    if (files) {
        params['files[]'] = files;
    }

    GibsonOS.Ajax.request({
        url: baseDir + 'transfer/index/upload',
        method: 'POST',
        timeout: 36000000,
        params: params,
        success: function(response) {
            GibsonOS.MessageBox.show({
                title: 'Fertig!',
                msg: 'Uploads wurden hinzugefügt.',
                type: GibsonOS.MessageBox.type.INFO
            });
        }
    });
});
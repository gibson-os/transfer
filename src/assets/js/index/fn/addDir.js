GibsonOS.define('GibsonOS.module.transfer.index.fn.addDir', function(dir, session, success, crypt) {
    var params = {
        dir: dir,
        id: session.id ? session.id : null,
        url: session.url ? session.url : null,
        port: session.port ? session.port : null,
        protocol: session.protocol ? session.protocol : null,
        user: session.user ? session.user : null,
        password: session.password ? session.password : null,
        crypt: crypt
    };

    GibsonOS.MessageBox.show({
        title: 'Ordnername',
        msg: 'Name des neuen Ordners?',
        type: GibsonOS.MessageBox.type.PROMPT,
        promptParameter: 'dirname',
        okText: 'Anlegen'
    },{
        url: baseDir + 'transfer/index/addDir',
        params: params,
        success: function(response) {
            if (success) {
                success(response);
            }
        }
    });
});
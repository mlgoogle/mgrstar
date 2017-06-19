requirejs.config({
    baseUrl: '../../../Public/template/assets/js',
    urlArgs: "version=" + new Date().getTime(),
    paths: {
        jquery          : 'vendor/jquery.min',
        layer           : 'vendor/layer',
        pagination      : 'vendor/jquery.pagination',
        utils           : 'libs/utils',
        config          : 'libs/config',
        datepicker      : 'vendor/jquery.datetimepicker',
        remodal         : 'vendor/remodal',

        sysAPI          : 'api/sysAPI',
        accountAPI      : 'api/accountAPI',
        clientAPI       : 'api/clientAPI',
        countAPI        : 'api/countAPI',


        // base64: 'libs/base64',
        // vaildform: 'vendor/Vaildform.min',
        // exif: 'vendor/exif',
        // submitApply: 'modules/submitApply',
    },
    shim: {
        'utils': {
            deps: ['config'],
            exports: ''
        }

    }
});

define(["jquery","layer"], function ($) {
    layer.config({
        offset: '200px'
    })
});

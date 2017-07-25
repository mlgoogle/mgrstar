define(["jquery"], function ($) {
    var channelAPI = {
        baseRequestUrl: "..",
        //新增
        getChannel: function (data,cb) {
            $.post(this.baseRequestUrl+"/channel/getChannel", data, function (result) {
                console.log(data)
                cb(result);
            })
        },
    }

    return channelAPI;

});



/**
 * Created by ctdone on 2017/8/10.
 */

function ajaxUrl() {
    data = {
    };
    ajaxPost(starUrl,data);
}


function ajaxPost(url,data) {
    $.ajax({
        type: "POST",
        url: url,
        dataType: "json",
        contentType:"application/json",
        data: data,
        success: function(msg){

        }
    });
}


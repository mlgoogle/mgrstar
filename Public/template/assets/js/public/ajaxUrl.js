/**
 * Created by ctdone on 2017/8/10.
 */

function ajaxUrl(uid) {
    data = {
        "uid":uid?uid:0
    };
    ajaxPost(starUrl,data);
}


function ajaxPost(url,data) {  
    $.ajax({
        type: "POST",
        url: url,
        dataType: "json",
       // contentType:"application/json",
        data: data,
        success: function(msg){
            console.log(msg);
        }
    });
}


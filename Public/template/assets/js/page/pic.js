/**
 * Created by ctdone on 2017/7/21.
 */
var xhrPic,clName,name;
function createXMLHttpRequestPic() {
    if (window.ActiveXObject) {
        xhrPic = new ActiveXObject("Microsoft.XMLHTTP");
    } else if (window.XMLHttpRequest) {
        xhrPic = new XMLHttpRequest();
    }
}

// 首页推荐图
function UploadIndexPic(){
    var fileObj = document.getElementById("filePic1").files;

    var len = fileObj.length;
    var form = new FormData();
    for (var i = 0; i < len; i++) {
        var size  = fileObj[i].size/1024;
        var type  = fileObj[i].type;

        if(size>2000){
            layer.msg('附件不能大于2M');
            return false;
        }


        if(!type.match('jpeg|jpg|png')){
            layer.msg('图片类型只支持jpg,png');
            return false;
        }

        form.append("myfile", fileObj[i]);
    }

    createXMLHttpRequestPic();
    clName = 'pic1_div';
    name = 'pic1';
    xhrPic.onreadystatechange = imgPic;
    var FileController = rootHomeUrl + '/Lucida/UploadFilePic' ;
    xhrPic.open("post", FileController, true);
    xhrPic.send(form);
}


//介绍背景图
function UploadBackPic() {
    var fileObj = document.getElementById("fileBackPic").files;

    var len = fileObj.length;
    var form = new FormData();
    for (var i = 0; i < len; i++) {
        var size  = fileObj[i].size/1024;
        var type  = fileObj[i].type;

        if(size>2000){
            layer.msg('附件不能大于2M');
            return false;
        }


        if(!type.match('jpeg|jpg|png')){
            layer.msg('图片类型只支持jpg,png');
            return false;
        }

        form.append("myfile", fileObj[i]);
    }

    createXMLHttpRequestPic();
    clName = 'back_pic_div';
    name = 'back_pic';
    xhrPic.onreadystatechange = imgPic;
    var FileController = rootHomeUrl + '/Lucida/UploadFilePic' ;
    xhrPic.open("post", FileController, true);
    xhrPic.send(form);

}

function imgPic() {
    if (xhrPic.readyState == 4) {
        if (xhrPic.status == 200 || xhrPic.status == 0) {
            var result = xhrPic.responseText;
            var json = eval("(" + result + ")");

            if(json.code == -2){
                layer.msg(json.message);
            }else {
                $('input[name='+ name +']').val(json);
            }
        }


      var img = '<span><img src="'+ publicUrl +'/uploads/pic/'+ json +'"></span>';
        $("."+clName).html(img);
        // console.log(xhr);
        // console.log(json);
    }
}


function UpladFile() {
    var fileObj = document.getElementById("file").files;
    var FileController = rootHomeUrl + '/Lucida/uploadFile';

    var len = fileObj.length;
    var form = new FormData();
    for (var i = 0; i < len; i++) {
        form.append("myfile[]", fileObj[i]);
    }

    createXMLHttpRequestPic();
    xhrPic.onreadystatechange = handleStateChange;
    xhrPic.open("post", FileController, true);
    xhrPic.send(form);
}


function handleStateChange() {
    if (xhrPic.readyState == 4) {
        if (xhrPic.status == 200 || xhrPic.status == 0) {
            var result = xhrPic.responseText;
            var json = eval("(" + result + ")");
            var len = json.length;
            var existImgLen = parseInt($(".lucida-div img").length);

            for (var i = 0; i < len; i++) {
                var i = parseInt(i);
                var num = i + existImgLen + 1;
                if (num > 5) {
                    num = (i < 1) ? 1 : i;
                }
                var name = "pic" + (num + 1);
                var inp = "<input type='hidden' name='"+name+"' value='"+ json[i] +"' />";
                $(".modalForm").append(inp);

                var img = '<span><img src="'+ publicUrl +'/uploads/lucida/'+json[i]+'"></span>';
                //var img = '<span><img src="'+json[i]+'"></span>';
                $(".lucida-div").append(img);
                //$(".lucida-div").html(img);
            }
        }
    }
}


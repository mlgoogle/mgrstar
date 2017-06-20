define(["jquery", "config", "datepicker", "layer"], function ($, config) {

    var utils = {
        getQuery: function (name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if (r != null)return unescape(r[2]);
            return null;
        },
        showLoading: function (ele) {
            $(ele).find("tbody").append("<img id='loading' src='/images/loading.gif'>").find("td").addClass("isLoading")
        },
        selectAll: function () {
            $("tr th input[type=checkbox]").on("change", function () {
                var $this = $(this);
                $this.prop("checked")
                    ? ( $this.parents("tr").siblings("tr").find("input[type=checkbox]").prop("checked", true))
                    : ( $this.parents("tr").siblings("tr").find("input[type=checkbox]").prop("checked", false));
            })
        },
        initDatePicker: function () {
            var oStart = $('#dateStart');
            var oEnd = $('#dateEnd');
            oStart.datetimepicker({
                lang: 'ch',
                timepicker: false,
                format: 'Y-m-d',
                formatDate: 'Y-m-d',
                scrollInput: false,
                validateOnBlur: false,
                onShow: function (ct) {
                    this.setOptions({
                        maxDate: oEnd.val() ? oEnd.val() : '+1970-01-01'
                    })
                },
                // minDate:'-1970-01-01',

                //yearStart: 2016,     //设置最小年份
                //yearEnd: 2050,        //设置最大年份
            });
            oEnd.datetimepicker({
                lang: 'ch',
                timepicker: false,
                format: 'Y-m-d',
                formatDate: 'Y-m-d',
                scrollInput: false,
                validateOnBlur: false,
                maxDate: '+1970-01-01',
                onShow: function (ct) {
                    this.setOptions({
                        minDate: oStart.val() ? oStart.val() : false
                    })
                }
            });
        },
        getCheckedArr: function () {
            var arr = [];
            $(".data-container table tbody tr").each(function () {
                var $this = $(this);
                if($this.find("input[type=checkbox]").prop("checked")){
                    arr.push($this.attr('data-id'));
                }
            });
            return arr;
        },
        initClientInfo: function () {
            var userId = utils.getQuery('uid');
            $(".data-container .tab-btns a").each(function () {
                var $this = $(this);
                var href = $this.attr("href") + '?uid=' + userId;
                $this.attr("href", href);
            });
        },
        /**
         * 表单验证
         * @param data {object}
         */
        validate: function () {

        }

    };


    /**
     *
     * @param str 字符串
     * @param regex 正则
     * @returns {boolean}
     */
    function valid(str, regex) {
        return regex.test(str)
    }


    return utils;
});
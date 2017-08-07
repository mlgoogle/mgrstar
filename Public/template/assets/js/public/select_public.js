/**
 * Created by ctdone on 2017/7/31.
 */
define([
    "jquery",
    "utils",
    "config",
    "accountAPI",
    "layer",
    "pagination",
    "remodal"
], function ($, utils, config, accountAPI) {
    data = {};
    accountAPI.searchOrg(data, function (result) {

        if(!result.list){
            return false;
        }
        var memberTr = '<option value="0">--选择--</option>',optionHtml = '';

        $.each(result.list, function (i, v) {
            optionHtml = '<option value="' + v.memberid+ '" data-mark = "' + v.mark + '" >' + v.name + '</option>';
            memberTr += optionHtml;
        });

        $('.search-bar select[name=member]').html(memberTr);
    });


    $('.search-bar').on("change","select[name=member]",function() {
        var __this = $(this);
        var memberId = __this.val();

        var memberMark = __this.find('option[value='+ memberId +']').attr('data-mark');
        $('.search-bar input[name=memberMark]').val(memberMark);


        var data = {
            memberid:memberId
        }

        accountAPI.getAgentList(data, function (result) {
            var agentTr = '<option value="0">--选择--</option>',agentOptionHtml = '';
            if(!result.list){
                $('.search-bar select[name=agent]').html(agentTr);
                $('.search-bar select[name=agentSub]').html(agentTr);
                return false;
            }


            $.each(result.list, function (j, a) {

                agentOptionHtml = '<option value="' + a.id + '" data-mark = "' + a.mark + '" >' + a.nickname + '</option>';
                agentTr += agentOptionHtml;
            });

            $('.search-bar select[name=agent]').html(agentTr);

        });

    });


    $('.search-bar').on("change","select[name=agent]",function() {
        var __this  = $(this);
        var agentId = __this.val();

        var agentMark = __this.find('option[value='+ agentId +']').attr('data-mark');
        $('.search-bar input[name=agentMark]').val(agentMark);

        var data = {
            agentId:agentId
        }

        accountAPI.getAgentSubList(data, function (result) {
            var agentSubTr = '<option value="0">--选择--</option>',agentSubOptionHtml = '';

            if(!result.list){
                $('.search-bar select[name=agentSub]').html(agentSubTr);
                return false;
            }

            $.each(result.list, function (j, a) {

                agentSubOptionHtml = '<option value="' + a.id + '" data-mark = "' + a.mark + '" >' + a.nickname + '</option>';
                agentSubTr += agentSubOptionHtml;
            });

            $('.search-bar select[name=agentSub]').html(agentSubTr);

        });

    });

    $('.search-bar').on('change','select[name=agentSub]',function () {
        agentId = $(this).val();

        var agentSubMark = $(this).find('option[value='+ agentId +']').attr('data-mark');
        $('.search-bar input[name=agentSubMark]').val(agentSubMark);

    })


});


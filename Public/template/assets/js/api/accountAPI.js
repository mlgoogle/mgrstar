/**
 * Created by DAY on 2017/4/15.
 */

define(["jquery"], function ($) {
    var accountAPI = {

        baseRequestUrl: "..",

        /**
         * 机构管理-获取一级机构列表 (添加机构时渲染所属机构 下拉列表)
         */
        getTopOrgList: function (data, cb) {
            $.post(this.baseRequestUrl + "/member/getRootList", data, function (result) {
                cb(result);
            })
        },
        /**
         * 机构管理-获取所有机构列表 (添加用户时渲染所属机构 下拉列表)
         */
        getOrgList: function (data, cb) {
            $.post(this.baseRequestUrl + "/member/getList", data, function (result) {
                cb(result);
            })
        },

        /**
         * 机构管理-选择机构获取区域经纪人列表 (添加用户时渲染所属机构 下拉列表)
         */
        getAgentList:function (data, cb){
            $.post(this.baseRequestUrl + "/member/getAgentInfo", data, function (result) {
                cb(result);
            })
        },

        /**
         * 机构管理-选择机构获取经纪人列表 (添加用户时渲染所属机构 下拉列表)
         */
        getAgentSubList:function (data, cb){
            $.post(this.baseRequestUrl + "/member/getAgentSubInfo", data, function (result) {
                cb(result);
            })
        },

        /**
         * 机构管理-新建机构
         */
        addOrg: function (data, cb) {
            $.post(this.baseRequestUrl + "/member/add", data, function (result) {
                cb(result);
            })
        },
        /**
         * 机构管理-删除
         */
        delOrg: function (data, cb) {
            $.post(this.baseRequestUrl + "/delOrg.php", data, function (result) {
                cb(result);
            })
        },
        /**
         * 机构管理-修改
         */
        changeOrg: function (data, cb) {
            $.post(this.baseRequestUrl + "/member/updateMember", data, function (result) {
                cb(result);
            })
        },
        /**
         * 用户管理-启用
         */
        updateOrgStatus: function (data, cb) {
            $.post(this.baseRequestUrl + "/member/updateStatus", data, function (result) {
                cb(result);
            })
        },

        /**
         * 机构管理-查询
         */
        searchOrg: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/member/getList", data, function (result) {
                cb(result);
            })
        },


        /**
         * 用户管理-新建机构
         */
        addUser: function (data, cb) {
            $.post(this.baseRequestUrl + "/adminuser/addUser", data, function (result) {
                cb(result);
            })
        },
        /**
         * 用户管理-删除
         */
        delUser: function (data, cb) {
            $.post(this.baseRequestUrl + "/adminuser/delUser", data, function (result) {
                cb(result);
            })
        },
        /**
         * 用户管理-修改
         */
        changeUser: function (data, cb) {
            $.post(this.baseRequestUrl + "/adminuser/updateUser", data, function (result) {
                cb(result);
            })
        },
        /**
         * 用户管理-重置密码
         */
        resetPwd: function (data, cb) {
            $.post(this.baseRequestUrl + "/adminuser/resetPassword", data, function (result) {
                cb(result);
            })
        },
        /**
         * 用户管理-启用
         */
        updateUserStatus: function (data, cb) {
            $.post(this.baseRequestUrl + "/adminuser/updateStatus", data, function (result) {
                cb(result);
            })
        },
        /**
         * 用户管理-查询
         */
        getUserList: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/adminuser/getList", data, function (result) {
                cb(result);
            })
        },


        /**
         * 经纪人管理-新建机构
         */
        addBroker: function (data, cb) {
            $.post(this.baseRequestUrl + "/agent/add", data, function (result) {
                cb(result);
            })
        },
        /**
         * 经纪人管理-删除
         */
        delBroker: function (data, cb) {
            $.post(this.baseRequestUrl + "/agent/del", data, function (result) {
                cb(result);
            })
        },
        /**
         * 经纪人管理-审核
         */
        checkBroker: function (data, cb) {
            $.post(this.baseRequestUrl + "/agent/updateVerify", data, function (result) {
                cb(result);
            })
        },
        /**
         * 经纪人管理-启用/禁用
         */
        updateBrokerStatus: function (data, cb) {
            $.post(this.baseRequestUrl + "/agent/updateStatus", data, function (result) {
                cb(result);
            })
        },

        /**
         * 经纪人管理-启用/禁用
         */
        updateBrokerSubStatus: function (data, cb) {
            $.post(this.baseRequestUrl + "/agentSub/updateStatus", data, function (result) {
                cb(result);
            })
        },

        /**
         * 经纪人管理-查询
         */
        searchBroker: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/agent/getList", data, function (result) {
                cb(result);
            })
        },

        /**
         * 第二级经纪人管理-查询
         */
        searchAgentSub: function (data, cb) {
            data.pageNum = 10;
            $.post(this.baseRequestUrl + "/agentSub/getList", data, function (result) {
                cb(result);
            })
        },

        /**
         * 第二级经纪人管理-新建机构
         */
        addAgentSub: function (data, cb) {
            $.post(this.baseRequestUrl + "/agentSub/add", data, function (result) {
                cb(result);
            })
        },


        /**
         * 查询单个 区域经纪人
         */
        getAgentOne:function (data, cb){
            $.post(this.baseRequestUrl + "/agent/getAgentFind", data, function (result) {
                cb(result);
            })
        },


        /**
         * 查询单个 经纪人
         */
        getAgentSubOne:function (data, cb){
            $.post(this.baseRequestUrl + "/agentSub/getAgentSubFind", data, function (result) {
                cb(result);
            })
        },
        
       // 修改

        updateAgent:function (data,cb) {;

            $.post(this.baseRequestUrl + "/agent/updateAgent", data, function (result) {
                cb(result);
            })
        },

        // 修改

        updateAgentSub:function (data,cb) {;

            $.post(this.baseRequestUrl + "/agentSub/updateAgentSub", data, function (result) {
                cb(result);
            })
        },


    };
    return accountAPI;
});
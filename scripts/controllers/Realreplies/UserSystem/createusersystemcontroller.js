'use strict';
function createusersystemcontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder, DTColumnDefBuilder, SweetAlert, $stateParams) {
    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
    $scope.roleobject = [];
    $scope.roles = [];
    $scope.multipleroleSelect = [];
   
    var user = new Object();
    if ($state.params != null) {
        user = $stateParams;
    }
    //$scope.multiplemenuSelect = [];
    $scope.userid = (user.userId == undefined ? "" : user.userId);
    if ($scope.userid != "" && $scope.userid != undefined) {
        $state.current.data.title = "Edit User System";
      
    }
    else
        $state.current.data.title = "Create User System";
    $scope.username = (user.username == undefined ? "" : user.username);
    $scope.password = "";
    var data = {
        userid: $scope.userid
    };
    $http.post(HOSTSERVER.url + '/getAllRoleSystem.php', data,
        {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            transformRequest: function (obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            }

        }).success(function (response) {
             $scope.roleobject = response.roles;
             $scope.roles = [];
            $scope.multipleroleSelect = [];
            $($scope.roleobject).each(function (index) {
                $scope.roles.push($scope.roleobject[index].rolename);
                if ($scope.roleobject[index].countuser != "0")
                    $scope.multipleroleSelect.push($scope.roleobject[index].rolename);
            });


        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })


    $scope.findobjectfromname = function (name) {
        var object = null;
        $($scope.roleobject).each(function (index) {
            if ($scope.roleobject[index].rolename == name)
                object = $scope.roleobject[index];
        })
        return object;
    }


    $scope.save = function () {
        var roleids = "";
        $($scope.multipleroleSelect).each(function (index) {
            var object = $scope.findobjectfromname($scope.multipleroleSelect[index]);
            if (index != ($scope.multipleroleSelect.length - 1))

                roleids += object.roleid + ",";
            else
                roleids += object.roleid;
        })
        var data = {
            username: $scope.username,
            password: $scope.password,
            roleids: roleids,
            userid: $scope.userid
        };

        if ($scope.username == "" || $scope.username == undefined || $scope.password == "" || $scope.password == undefined) {
            SweetAlert.swal('errors', "please fill all fields", 'error');
            return;
        }

        if ($scope.password != $scope.repassword) {
            SweetAlert.swal('errors', "password and repassword don't match", 'error');
            return;
        }
        $http.post(HOSTSERVER.url + '/updateusersystem.php', data,
            {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                transformRequest: function (obj) {
                    var str = [];
                    for (var p in obj)
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    return str.join("&");
                }

            }
            ).success(function (response) {
                if (response.status == "-1") {
                    SweetAlert.swal('Access Denied!', "you can't have permission to access  this page ", 'error');
                    return;
                }

                if (response.status == "0")
                    SweetAlert.swal('errors', response.message, 'error');
                else
                    $state.go('app.usersystems.managerusersystems');
            }).error(function (errors, status) {
                var errorObj = __errorHandler.ProcessErrors(errors);
                __errorHandler.Swal(errorObj, _sweetAlert);
            })
    }
}

angular
  .module('ReadrepliesAdmin')
  .controller('createusersystemcontroller', ['$scope', '$interval', 'COLORS', 'HOSTSERVER', '$http', 'AuthorizationService', '$state', "DTOptionsBuilder", "DTColumnDefBuilder", "SweetAlert", "$stateParams", createusersystemcontroller]);

'use strict';
function createusercontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder, DTColumnDefBuilder, SweetAlert, $stateParams) {
    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
    var userinfor = new Object();
    if ($state.params != null) {
        userinfor = $stateParams;
    }
   
    $scope.userid = (userinfor.id == undefined ? "" : userinfor.id);
    if ($scope.userid != "" && $scope.userid != undefined)
        $state.current.data.title = "Edit User";
    else
        $state.current.data.title = "Create User";
    $scope.useremail = userinfor.email;
    $scope.userfname = userinfor.name;
    $scope.userlname = userinfor.lname;
    $scope.userpassword = "";
    $scope.userrepassword = "";
    $scope.useractive = (userinfor.disabled=="1"?false:true);
    
    $scope.save = function () {
        var data = {
            disabled: ($scope.useractive == true ? "0" : "1"),
            email: $scope.useremail,
            password: $scope.userpassword,
            fname: $scope.userfname,
            lname: $scope.userlname,
            userid: $scope.userid
        };

        if ($scope.useremail == "" || $scope.useremail == "" || $scope.userpassword==""  ) {
            SweetAlert.swal('errors', "please fill all fields", 'error');
            return;
        }
        else if ($scope.userpassword != $scope.userrepassword)
        {
            SweetAlert.swal('errors', "password and re-password don't match", 'error');
            return;

        }

        $http.post(HOSTSERVER.url + '/updateuser.php', data,
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
                else if (response.status == "-2") {
                    $state.go('user.signin');
                    return;
                }
            if (response.status=="0")
                SweetAlert.swal('errors', response.message, 'error');
            else
                $state.go('app.users.managerusers');
        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })
    }
}

angular
  .module('ReadrepliesAdmin')
  .controller('createusercontroller', ['$scope', '$interval', 'COLORS', 'HOSTSERVER', '$http', 'AuthorizationService', '$state', "DTOptionsBuilder", "DTColumnDefBuilder", "SweetAlert","$stateParams", createusercontroller]);

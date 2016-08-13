'use strict';
function createrolecontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder, DTColumnDefBuilder, SweetAlert, $stateParams) {
    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
    $scope.menus = [];
    $scope.multiplemenuSelect=[];
    $scope.menuobjects = [
    {
        'menuid': "1",
        'name': 'MANAGER_USERS',
    },
    {
        'menuid': "2",
        'name': 'CREATE_EDIT_USER',
    },
    {
        'menuid': "3",
        'name': 'MANAGER_POSTS',
    },
    {
        'menuid': "4",
        'name': 'MANAGER_COMMENTS',
    },
    {
        'menuid': "5",
        'name': 'BLOCK_COMMENT',
    },
    {
        'menuid': "6",
        'name': 'BLOCK_POST',
    },

    ];
    $($scope.menuobjects).each(function (index) {
        $scope.menus.push($scope.menuobjects[index].name);
    });
    var role = new Object();
    if ($state.params != null) {
        role = $stateParams;
    }
    //$scope.multiplemenuSelect = [];
    $scope.roleid = (role.roleid == undefined ? "" : role.roleid);
    if ($scope.roleid != "" && $scope.roleid != undefined) {
        $state.current.data.title = "Edit Role";
        var data = {
            roleid: $scope.roleid
        };
        $http.post(HOSTSERVER.url + '/getAllmenuSystem.php', data,
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
                if (response.status == "-1") {
                    SweetAlert.swal('Access Denied!', "you can't have permission to access  this page ", 'error');
                    return;
                }
                else if (response.status == "-2") {
                    $state.go('user.signin');
                    return;
                }
                $($scope.menuobjects).each(function (index) {
                    $scope.menus.push($scope.menuobjects[index].name);
                });
            $(response.menus).each(function (index) {
               $scope.multiplemenuSelect.push(response.menus[index].name);
            })
            
        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })
    }
    else
        $state.current.data.title = "Create Role";
    $scope.rolename = role.rolename;
    $scope.description = role.description;
  
    
   

    $scope.findobjectfromname = function (name) {
        var object = null;
        $($scope.menuobjects).each(function(index)
        {
            if ($scope.menuobjects[index].name == name)
                object = $scope.menuobjects[index];
        })
        return object;
    }


    $scope.save = function () {
        var menuids = "";
        $($scope.multiplemenuSelect).each(function (index) {
            var object = $scope.findobjectfromname($scope.multiplemenuSelect[index]);
            if (index != ($scope.multiplemenuSelect.length - 1))
                
                menuids += object.menuid + ",";
            else
                menuids += object.menuid;
        })
        var data = {
            rolename: $scope.rolename,
            description: $scope.description,
            menuids: menuids,
            roleid: $scope.roleid
        };

        if ($scope.rolename == "" || $scope.description == "" ) {
            SweetAlert.swal('errors', "please fill all fields", 'error');
            return;
        }
        

        $http.post(HOSTSERVER.url + '/updaterole.php', data,
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
                $state.go('app.roles.managerroles');
        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })
    }
}

angular
  .module('ReadrepliesAdmin')
  .controller('createrolecontroller', ['$scope', '$interval', 'COLORS', 'HOSTSERVER', '$http', 'AuthorizationService', '$state', "DTOptionsBuilder", "DTColumnDefBuilder", "SweetAlert", "$stateParams", createrolecontroller]);

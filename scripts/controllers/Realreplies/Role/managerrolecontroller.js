'use strict';

function managerrolecontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder, DTColumnDefBuilder, SweetAlert, $location, $modal) {

    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
    $scope.titleBlock = "Block";
    $scope.rolesmanager = [];
    $scope.dtOptions = DTOptionsBuilder.newOptions().withPaginationType('full_numbers').withDisplayLength(10).withOption('aaSorting', [[0, 'desc']]);
    $scope.dtColumnDefs = [
    
    ];
  
    $http.post(HOSTSERVER.url + '/getAllRoleSystem.php', null).success(function (response) {
        if (response.status == "-1") {
            SweetAlert.swal('Access Denied!', "you can't have permission to access  this page ", 'error');
            return;
        }
        $scope.rolesmanager = response.roles;
    }).error(function (errors, status) {
        var errorObj = __errorHandler.ProcessErrors(errors);
        __errorHandler.Swal(errorObj, _sweetAlert);
    })
    $scope.getBlockTitle = function (person) {
        if (person.disabled == "1")
        {
            return "Unblock";
        }
        else
            return "Block";

    }
    $scope.editrole = function (role) {
        $state.go('app.roles.editroles', role);
    }
    $scope.viewcomments = function (person)
    {
        $state.go('app.posts.managerpostsofuser', person);
    }
    $scope.viewimages = function (person) {
        var data = {
            userid: person.id,
        };
        $http.post(HOSTSERVER.url + '/getAllImagesofUser.php', data,
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
                 if (response.userimages.length > 0) {
                     var modalInstance = $modal.open({
                         templateUrl: 'ModelslideImages.html',
                         controller: 'ModalslideInstanceCtrl',
                         size: "large",
                         resolve: {
                             items: function () {
                                 return response.userimages;
                             }
                         }
                     });
                 }
                 else {
                     SweetAlert.swal('Images!', 'This user do not have images!', 'success');
                 }
             }).error(function (errors, status) {
                 var errorObj = __errorHandler.ProcessErrors(errors);
                 __errorHandler.Swal(errorObj, _sweetAlert);
             })
    }

    $scope.blockuser = function (person) {
        var data = {
            userid: person.id,
            disabled: person.disabled=="1"?"0":"1"
        };
        $http.post(HOSTSERVER.url + '/blockuser.php', data,           
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
            if (person.disabled == "0") {
                person.disabled = "1";
                SweetAlert.swal('Block!', 'You Blocked this user!', 'success');
                
            }
            else {
                person.disabled = "0";
                SweetAlert.swal('Block!', 'You Unblocked this user!', 'success');

            }
        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })

    }

}
function ModalslideInstanceCtrl($scope, $modalInstance, items) {
    $scope.items = items;
    $scope.images = [];

    $(items).each(function (index) {
        $scope.images.push({
            thumb: 'http://' + items[index].thumb,
            url:  items[index].attachment,
            video: false,

        });
    })


    $scope.currentIndex = 0;

    $scope.setCurrentIndex = function (index) {
        $scope.currentIndex = index;
    };

    $scope.isCurrentIndex = function (index) {
        return $scope.currentIndex === index;
    };

    $scope.showNext = function () {
        $scope.currentIndex = ($scope.currentIndex < $scope.images.length - 1) ? ++$scope.currentIndex : 0;
    };
    $scope.showPrevious = function () {
        $scope.currentIndex = ($scope.currentIndex > 0) ? --$scope.currentIndex : $scope.images.length - 1;
    };

}
angular
  .module('ReadrepliesAdmin')
  .controller('managerrolecontroller', ['$scope', '$interval', 'COLORS', 'HOSTSERVER', '$http', 'AuthorizationService', '$state', "DTOptionsBuilder", "DTColumnDefBuilder", "SweetAlert", "$location", "$modal", managerrolecontroller])
  .controller('ModalslideInstanceCtrl', ['$scope', '$modalInstance', 'items', ModalslideInstanceCtrl]);
;

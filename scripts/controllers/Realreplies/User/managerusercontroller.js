'use strict';

function managerusercontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder, DTColumnDefBuilder, SweetAlert, $location, $modal, DTColumnBuilder) {

    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
    $scope.titleBlock = "Block";
    $scope.usersmanager = [];
    $scope.dtOptions = DTOptionsBuilder.newOptions()
            .withFnServerData(serverData)
            .withDataProp('data')
            .withOption('processing', true)
            .withOption('serverSide', true)
            .withOption('paging', true)
            .withPaginationType('full_numbers')
            .withDisplayLength(10)
           

    function serverData(sSource, aoData, fnCallback, oSettings) {




        //All the parameters you need is in the aoData variable
        var draw = aoData[0].value;
        var order = aoData[2].value;
        var start = aoData[3].value;
        var length = aoData[4].value;
        var search = aoData[5].value;
        var data = {
            pagenumber: start,
            pagesize: length,
            textsearch: search.value
        };

        $http.post(HOSTSERVER.url + '/getAllUsers.php', data, {
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
            var records = {
                'recordsTotal': response.data[0].TotalRows,
                'data': [],
                'recordsFiltered': response.data[0].TotalRows
            };

            fnCallback(records);
            $(".dataTables_empty").hide();
            $scope.usersmanager = response.data[0].Rows;

        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })
    }



    $scope.dtColumns = [

      DTColumnBuilder.newColumn('id', 'ID'),
      DTColumnBuilder.newColumn('email', 'Email'),
      DTColumnBuilder.newColumn('name', 'First Name'),
      DTColumnBuilder.newColumn('lname', 'Last Name')

    ];
  

    $scope.getBlockTitle = function (person) {
        if (person.disabled == "1")
        {
            return "Unblock";
        }
        else
            return "Block";

    }
    $scope.edituser = function (person) {
        $state.go('app.users.createuser',person);
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
                 if (response.status == "-1") {
                     SweetAlert.swal('Access Denied!', "you can't have permission to access  this page ", 'error');
                     return;
                 }
                 else if (response.status == "-2") {
                     $state.go('user.signin');
                     return;
                 }
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

                if (response.status == "-1") {
                    SweetAlert.swal('Access Denied!', "you can't have permission to access  this page ", 'error');
                    return;
                }
                else if (response.status == "-2") {
                    $state.go('user.signin');
                    return;
                }
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
            url:  'http://'  + items[index].attachment,
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
  .controller('managerusercontroller', ['$scope', '$interval', 'COLORS', 'HOSTSERVER', '$http', 'AuthorizationService', '$state', "DTOptionsBuilder", "DTColumnDefBuilder", "SweetAlert", "$location","$modal", "DTColumnBuilder",managerusercontroller])
  .controller('ModalslideInstanceCtrl', ['$scope', '$modalInstance', 'items', ModalslideInstanceCtrl]);
;

'use strict';

function managerpostcontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder, DTColumnBuilder, SweetAlert, $location, $modal, $stateParams) {

    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
    $scope.titleBlock = "Block";
    $scope.postsmanager = [];
    $scope.dtOptions = DTOptionsBuilder.newOptions().withPaginationType('full_numbers').withDisplayLength(10).withOption('aaSorting', [[0, 'desc']]);

    
    $scope.dtColumns = [
       DTColumnBuilder.newColumn('ID').withTitle('IdsdsdD'),
    
    ];
   
    var data = {
        userid: "",
    };

    if ($state.params != null) {
        data.userid = $stateParams.id == undefined ? "" : $stateParams.id;
    }
    $http.post(HOSTSERVER.url + '/getAllPosts.php', data, {
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
        $scope.postsmanager = response.questions;

    }).error(function (errors, status) {
        var errorObj = __errorHandler.ProcessErrors(errors);
        __errorHandler.Swal(errorObj, _sweetAlert);
    })
    $scope.getBlockTitle = function (post) {
        if (post.isblock == "1")
        {
            return "Unblock";
        }
        else
            return "Block";

    }
    $scope.viewcomment=function(post){
        $state.go('app.comments.managercommentsofquestion',post);
    }
    $scope.viewimages = function (post) {
        var data = {
            questionid: post.id,
        };
        $http.post(HOSTSERVER.url + '/getAllImagesofquestion.php', data,
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
            if (response.questionimages.length > 0) {
                var modalInstance = $modal.open({
                    templateUrl: 'ModelslideImages.html',
                    controller: 'ModalslideInstanceCtrl',
                    size: "large",
                    resolve: {
                        items: function () {
                            return response.questionimages;
                        }
                    }
                });
            }
            else {
                SweetAlert.swal('Images!', 'This question do not have images!', 'success');
            }
        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })
    }

   
    $scope.blockquesion = function (post) {
        var data = {
            userid: post.userId,
            questionid:post.id,
            block: post.isblock == "1" ? "0" : "1"
        };
        $http.post(HOSTSERVER.url + '/blockquestion.php', data,
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
                if (post.isblock == "0") {
                    post.isblock = "1";
                    SweetAlert.swal('Block!', 'You Blocked this question!', 'success');
                
            }
            else {
                    post.isblock = "0";
                    SweetAlert.swal('Block!', 'You Unblocked this question!', 'success');

            }
        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })

    }

}
// Please note that $modalInstance represents a modal window (instance) dependency.
// It is not the same as the $modal service used above.
function ModalslideInstanceCtrl($scope, $modalInstance, items) {
    $scope.items = items;
    $scope.images = [];

    $(items).each(function (index) {
        $scope.images.push({
            thumb: 'http://' + items[index].imagethumb,
            url: 'http://' + items[index].image,
            video:false,

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
  .controller('managerpostcontroller', ['$scope', '$interval', 'COLORS', 'HOSTSERVER', '$http', 'AuthorizationService', '$state', "DTOptionsBuilder", "DTColumnBuilder", "SweetAlert", "$location", "$modal", "$stateParams", managerpostcontroller])
  .controller('ModalslideInstanceCtrl', ['$scope', '$modalInstance', 'items', ModalslideInstanceCtrl]);

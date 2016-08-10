'use strict';

function managercommentcontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder, DTColumnBuilder, SweetAlert, $location, $modal, $stateParams) {

    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
    $scope.titleBlock = "Block";
    $scope.commentsmanager = [];
    $scope.dtOptions = DTOptionsBuilder.newOptions().withPaginationType('full_numbers').withDisplayLength(10).withOption('aaSorting', [[0, 'desc']]);

    
    $scope.dtColumns = [
       DTColumnBuilder.newColumn('ID').withTitle('IdsdsdD'),
    
    ];
   
    var data = {
        questionid: "",
    };

    if ($state.params != null) {
        data.questionid = $stateParams.id == undefined ? "" : $stateParams.id;
    }
    $http.post(HOSTSERVER.url + '/getAllComments.php', data, {
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
        $scope.commentsmanager = response.answers;

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

    $scope.viewimages = function (comment) {

        if (comment.attachment != "" && comment.attachment != undefined) {
            var modalInstance = $modal.open({
                templateUrl: 'ModelslideImages.html',
                controller: 'ModalslideInstanceCtrl',
                size: "large",
                resolve: {
                    items: function () {
                        return [comment]
                    }
                }
            });

        }
        else {
            SweetAlert.swal('Images!', 'This answer do not have images!', 'success');
        }


    
    }

   
    $scope.blockcomment = function (comment) {
        var data = {
            userid: comment.userId,
            answerid: comment.id,
            block: comment.isblock == "1" ? "0" : "1"
        };
        $http.post(HOSTSERVER.url + '/blockanswer.php', data,
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
                if (comment.isblock == "0") {
                    comment.isblock = "1";
                    SweetAlert.swal('Block!', 'You Blocked this question!', 'success');
                
            }
            else {
                    comment.isblock = "0";
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
            thumb: 'http://' + items[index].thumb,
            url: 'http://' + items[index].attachment,
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
  .controller('managercommentcontroller', ['$scope', '$interval', 'COLORS', 'HOSTSERVER', '$http', 'AuthorizationService', '$state', "DTOptionsBuilder", "DTColumnBuilder", "SweetAlert", "$location", "$modal", "$stateParams", managercommentcontroller])
  .controller('ModalslideInstanceCtrl', ['$scope', '$modalInstance', 'items', ModalslideInstanceCtrl]);

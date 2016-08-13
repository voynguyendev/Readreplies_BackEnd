'use strict';

function managercommentcontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder, DTColumnBuilder, SweetAlert, $location, $modal, $stateParams) {

    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
    $scope.titleBlock = "Block";
    $scope.commentsmanager = [];
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
            questionid: "",
            pagenumber: start,
            pagesize: length,
            textsearch: search.value
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


            $scope.commentsmanager = response.data[0].Rows;

        }).error(function (errors, status) {
            var errorObj = __errorHandler.ProcessErrors(errors);
            __errorHandler.Swal(errorObj, _sweetAlert);
        })

    }
     


    $scope.dtColumns = [
      DTColumnBuilder.newColumn('id', 'Id'),
      DTColumnBuilder.newColumn('answer', 'Content')
    ];

   
   
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
            questionid: comment.questionid,
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

                if (response.status == "-1") {
                    SweetAlert.swal('Access Denied!', "you can't have permission to access  this page ", 'error');
                    return;
                }
                else if (response.status == "-2") {
                    $state.go('user.signin');
                    return;
                }

                if (comment.isblock == "0") {
                    comment.isblock = "1";
                    SweetAlert.swal('Block!', 'You Blocked this Comment!', 'success');
                
            }
            else {
                    comment.isblock = "0";
                    SweetAlert.swal('Block!', 'You Unblocked this Comment!', 'success');

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

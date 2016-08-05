'use strict';

function managerusercontroller($scope, $interval, COLORS, HOSTSERVER, $http, AuthorizationService, $state, DTOptionsBuilder,DTColumnDefBuilder) {

    if (!AuthorizationService.IsAuthorized()) {
        $state.go('user.signin');
    }
   
    $scope.usersmanager = [];
    $scope.dtOptions = DTOptionsBuilder.newOptions().withPaginationType('full_numbers').withDisplayLength(10).withOption('aaSorting', [[0, 'desc']]);
    $scope.dtColumnDefs = [
       // DTColumnDefBuilder.newColumnDef(0),
        //DTColumnDefBuilder.newColumnDef(1).notVisible(),
       // DTColumnDefBuilder.newColumnDef(0).notSortable()
    
    ];
  
    $http.post(HOSTSERVER.url + '/getAllUsers.php', null).success(function (response) {
        $scope.usersmanager = response.userinfors;

    }).error(function (errors, status) {
        var errorObj = __errorHandler.ProcessErrors(errors);
        __errorHandler.Swal(errorObj, _sweetAlert);
    })

   

}

angular
  .module('ReadrepliesAdmin')
  .controller('managerusercontroller', ['$scope', '$interval', 'COLORS', 'HOSTSERVER', '$http', 'AuthorizationService', '$state', "DTOptionsBuilder", "DTColumnDefBuilder", managerusercontroller]);

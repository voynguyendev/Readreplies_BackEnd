'use strict';

function sessionCtrl($scope, $state, AuthorizationService, SweetAlert) {
  $scope.signin = function () {
    $state.go('user.signin');
  };
  $scope.email = "";
  $scope.password = "";
  $scope.submit = function () {
      AuthorizationService.SignInLocal($scope.email, $scope.password).then(function (response) {
          if (response.status == "1") {
              $scope.$parent.user.fname = response.userinfor.username;
              $state.go('app.dashboard');
          }
          else {
              SweetAlert.swal('errors', "Your UserName or Password Invalid", 'error');
          }
      }, function (errors) {
          var errorObj = __errorHandler.ProcessErrors(errors);
          __errorHandler.Swal(errorObj, _sweetAlert);
      }); 
  };
}

angular
  .module('ReadrepliesAdmin')
  .controller('sessionCtrl', ['$scope', '$state', 'AuthorizationService', 'SweetAlert', sessionCtrl]);

'use strict';

function validationCtrl($scope) {
  $scope.validationOpt = {
    rules: {
      namefield: {
        required: true,
        minlength: 3
      },
      urlfield: {
        required: true,
        minlength: 3,
        url: true
      }
    }
  };
}

angular
  .module('ReadrepliesAdmin')
  .controller('validationCtrl', ['$scope', validationCtrl]);

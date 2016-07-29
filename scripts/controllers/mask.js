'use strict';

function maskCtrl($scope) {
  $scope.maskOpt = {
    autoclear: false
  };
}

angular
  .module('ReadrepliesAdmin')
  .controller('maskCtrl', ['$scope', maskCtrl]);

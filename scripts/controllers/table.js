'use strict';

function tableCtrl($scope) {
  $scope.dataTableOpt = {
    'ajax': 'data/datatables-arrays.json'
  };
}

angular
  .module('ReadrepliesAdmin')
  .controller('tableCtrl', ['$scope', tableCtrl]);

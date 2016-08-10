'use strict';

function 
($scope, FileUploader) {
  var uploader = $scope.uploader = new FileUploader({
    //url: 'scripts/upload.php'
  });

  // FILTERS

  uploader.filters.push({
    name: 'customFilter',
    fn: function (item /*{File|FileLikeObject}*/ , options) {
      return this.queue.length < 10;
    }
  });
}

angular
  .module('ReadrepliesAdmin')
  .controller('uploadCtrl', ['$scope', 'FileUploader', uploadCtrl]);

'use strict';

angular
  .module('ReadrepliesAdmin')
  .controller('AppCtrl', ['$scope', '$http', '$localStorage','AuthorizationService','$state',
function AppCtrl($scope, $http, $localStorage, AuthorizationService,$state) {
      $scope.mobileView = 767;
      $scope.app = {
        name: 'Urban',
        author: 'Nyasha',
        version: '1.0.0',
        year: (new Date()).getFullYear(),
        layout: {
          isSmallSidebar: false,
          isChatOpen: false,
          isFixedHeader: true,
          isFixedFooter: false,
          isBoxed: false,
          isStaticSidebar: false,
          isRightSidebar: false,
          isOffscreenOpen: false,
          isConversationOpen: false,
          isQuickLaunch: false,
          sidebarTheme: '',
          headerTheme: ''
        },
        isMessageOpen: false,
        isConfigOpen: false
      };

      $scope.logout = function () {
          AuthorizationService.Logout();
          $state.go('user.signin');
      }
      $scope.user = {
        fname: '',
        lname: '',
        jobDesc: 'Administrastor',
        avatar: 'images/avatar.jpg',
      };

      AuthorizationService.Getuserinfor().then(function (response) {
          if (response.status == "1")
          {
              $scope.user.fname = response.userinfor.username;
              $scope.user.lname = "";
          }
          
      }, function (errors) {
          var errorObj = __errorHandler.ProcessErrors(errors);
          __errorHandler.Swal(errorObj, _sweetAlert);
      });


      if (angular.isDefined($localStorage.layout)) {
        $scope.app.layout = $localStorage.layout;
      } else {
        $localStorage.layout = $scope.app.layout;
      }

      $scope.$watch('app.layout', function () {
        $localStorage.layout = $scope.app.layout;
      }, true);

      $scope.getRandomArbitrary = function () {
        return Math.round(Math.random() * 100);
      };
    }
]);


angular
  .module('ReadrepliesAdmin')
  .factory('RoleService', ['$http', '$q', '$cookies', '$interval', '$timeout', 'HOSTSERVER', function ($http, $q, $cookies, $interval, $timeout, HOSTSERVER) {
    
      var _getAllUser = function () {
          var deferer = $q.defer();
          $http.get('/getAllUsers.php')
              .success(function (response) {
                  deferer.resolve(questions);
              }).error(function (errors, status) {
                  __promiseHandler.Error(errors, status, deferer);
              })
          return deferer.promise;
      }

      return {
          IsAuthorized: _isAuthorized,
          Auth: _auth,
          VerifyUser: _verifyUser,
          SignInLocal: _signInLocal,
          SignUpLocal: _signUpLocal,
          GetExternalLoginProviders: _getExternalLoginProviders,
          GetExternalProvider: _getExternalProvider,
          ExternalLogin: _externalLogin,
          ExternalLoginWithExternalBearer: _externalLoginWithExternalBearer,
          PrepareSignUpExternal: _prepareSignUpExternal,
          IsSignUpExternal: _isSignUpExternal,
          GetSignUpExternal: _getSignUpExternal,
          RegisterExternal: _registerExternal,
          GetUserAuthInfo: _getUserAuthInfo,
          GetAllSecurityQuestions: _getAllSecurityQuestions,
          Logout: _logout,
          VerifyPhoneNumber: _verifyPhoneNumber,
          VerifyPIN: _verifyPIN,
          ValidateRegistrationInfo: _validateRegistrationInfo,
          ValidateExternalRegistrationInfo: _validateExternalRegistrationInfo,
          CheckPIN: _checkPIN,
          IsLinkedWithBusinessAccount: _isLinkedWithBusinessAccount,
          Getuserinfor: _getuserinfor
      }
  }])
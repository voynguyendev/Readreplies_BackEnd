'use strict';

angular
  .module('ReadrepliesAdmin')
  .factory('AuthorizationService', ['$http', '$q', '$cookies', '$interval', '$timeout', 'HOSTSERVER', function ($http, $q, $cookies, $interval, $timeout, HOSTSERVER) {
      var _authorized = false;

      var _accessToken = $cookies.get('access_token');
      if (_accessToken) {
          _authorized = true;
      }

      var _isAuthorized = function () {
          return _authorized;
      }

      var _auth = function (accessToken) {
          _authorized = true;
          $cookies.put('access_token', accessToken, { path: '/' });
      }

      var _unAuth = function () {
          _authorized = false;
          $cookies.remove('access_token', { path: '/' });
      }

      var _verifyUser = function () {
          var deferer = $q.defer();

          $http.post('/api/Account/VerifyUser')
              .success(function (response) {
                  deferer.resolve(response);
              }).error(function (errors, status) {
                  deferer.reject();
              })

          return deferer.promise;
      }

      var _signInLocal = function (username, password) {
          var deferer = $q.defer();

          var data = {
              username: username,
              password: password,
          };

          $http.post(HOSTSERVER.url + '/token.php', data, {
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
              _auth(response.access_token)
              deferer.resolve(response);
          }).error(function (errors, status) {
              deferer.reject(errors);
          })

          return deferer.promise;
      }

      var _signUpLocal = function (registerModel) {
          var deferer = $q.defer();

          $http.post('/api/Account/Register', registerModel)
              .success(function (response) {
                  deferer.resolve();
              }).error(function (errors, status) {
                  __promiseHandler.Error(errors, status, deferer);
              });

          return deferer.promise;
      }

      var _prepareSignUpExternal = function (externalAccessToken, externalProvider) {
          $cookies.put('register_external', externalAccessToken, { path: '/' });
          $cookies.putObject('external_provider', externalProvider, { path: '/' });
          window.location.href = '/User/SignUp';
      }

      var _getExternalProvider = function () {
          var provider = $cookies.getObject('external_provider');
          $cookies.remove('external_provider', { path: '/' });
          return provider;
      }

      var externalAccessToken = null;
      var _isSignUpExternal = function () {
          externalAccessToken = $cookies.get('register_external');
          $cookies.remove('register_external', { path: '/' })
          return externalAccessToken != undefined && externalAccessToken != null;
      }

      var _getSignUpExternal = function () {
          var deferer = $q.defer();

          $http.get('/api/Account/ExternalLoginInfo', {
              headers: {
                  Authorization: externalAccessToken
              }
          }).success(function (info) {
              deferer.resolve(info);
          }).error(function (errors, status) {
              __promiseHandler.Error(errors, status);
          });

          return deferer.promise;
      }

      var _getExternalLoginProviders = function () {
          var deferer = $q.defer();

          $http.get('/api/Account/ExternalLogins', { params: { returnUrl: '/User/ExternalLoginSuccess' } })
              .success(function (providers) {
                  deferer.resolve(providers);
              }).error(function (errors, status) {
                  deferer.reject();
              })

          return deferer.promise;
      }

      var _externalLogin = function (provider, callback) {
          var deferer = $q.defer();
          var resolved = false;
          window.__authCallback = function (fragment) {
              resolved = true;
              var copy = {};
              for (var i in fragment) {
                  copy[i] = fragment[i];
              }
              deferer.resolve(copy);
          }
          var oauthWindow = window.open(provider.Url, "Authenticate Account", "location=0,status=0,width=600,height=750");

          var checkClosed = $interval(function () {
              if (oauthWindow.closed) {
                  $interval.cancel(checkClosed)
                  $timeout(function () {
                      if (!resolved) {
                          deferer.reject('User cancelled');
                      }
                  }, 100)
              }
          }, 200);

          return deferer.promise;
      }

      var _externalLoginWithExternalBearer = function () {
          var deferer = $q.defer();

          $http.post('/api/Account/LoginWithExternalBearer', null, {
              headers: {
                  Authorization: externalAccessToken
              }
          }).success(function (accessToken) {
              _auth(accessToken);
              deferer.resolve();
          }).error(function (errors, status) {
              __promiseHandler.Error(errors, status, deferer);
          });

          return deferer.promise;
      }

      var _registerExternal = function (model) {
          var deferer = $q.defer();

          $http.post('/api/Account/RegisterExternal', model, {
              headers: {
                  Authorization: externalAccessToken
              }
          }).success(function () {
              deferer.resolve();
          }).error(function (errors, status) {
              __promiseHandler.Error(errors, status, deferer);
          });

          return deferer.promise;
      }

      var _getUserAuthInfo = function (accessToken) {
          var deferer = $q.defer();

          $http.get('/api/Account/UserInfo', {
              headers: {
                  Authorization: accessToken
              }
          }).success(function (response) {
              deferer.resolve(response);
          }).error(function (errors, status) {
              deferer.reject();
          })

          return deferer.promise;
      }

      var _logout = function () {
          var deferer = $q.defer();

          $http.post('/api/Account/Logout', null).success(function () {
              _unAuth();
              deferer.resolve();
          }).error(function (errors, status) {
              deferer.reject();
          })

          return deferer.promise;
      }

      var _getAllSecurityQuestions = function () {
          var deferer = $q.defer();

          $http.get('/Api/Account/SecurityQuestions')
              .success(function (questions) {
                  deferer.resolve(questions);
              }).error(function (errors, status) {
                  __promiseHandler.Error(errors, status, deferer);
              })

          return deferer.promise;
      }

      var _verifyPhoneNumber = function (model) {
          var deferer = $q.defer();

          $http.post('/Api/Account/Verify/PhoneNumber', model)
              .success(function (requestId) {
                  deferer.resolve(requestId);
              }).error(function (errors, status) {
                  __promiseHandler.Error(errors, status, deferer);
              })

          return deferer.promise;
      }

      var _verifyPIN = function (model) {
          var deferer = $q.defer();

          $http.post('/Api/Account/Verify/PIN', model)
              .success(function (verifiedToken) {
                  deferer.resolve(verifiedToken);
              }).error(function (errors, status) {
                  __promiseHandler.Error(errors, status, deferer);
              })

          return deferer.promise;
      }

      var _validateRegistrationInfo = function (model) {
          var deferer = $q.defer();

          $http.post('/Api/Account/ValidateRegistrationInfo', model)
              .success(function () {
                  deferer.resolve();
              }).error(function (errors, status) {
                  __promiseHandler.Error(errors, status, deferer);
              })

          return deferer.promise;
      }

      var _validateExternalRegistrationInfo = function (model) {
          var deferer = $q.defer();

          $http.post('/Api/Account/ValidateExternalRegistrationInfo', model)
              .success(function () {
                  deferer.resolve();
              }).error(function (errors, status) {
                  __promiseHandler.Error(errors, status, deferer);
              })

          return deferer.promise;
      }

      var _checkPIN = function (model) {
          var deferer = $q.defer();

          $http.post('/Api/Account/Verify/CheckPIN', model)
              .success(function () {
                  deferer.resolve();
              }).error(function (errors, status) {
                  __promiseHandler.Error(errors, status, deferer);
              })

          return deferer.promise;
      }

      var _isLinkedWithBusinessAccount = function (hideAjaxLoader) {
          var deferer = $q.defer();

          $http.get('/Api/Account/IsLinkedWithBusinessAccount', { params: { hideAjaxLoader: hideAjaxLoader } })
              .success(function (linked) {
                  deferer.resolve(linked);
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
          IsLinkedWithBusinessAccount: _isLinkedWithBusinessAccount
      }
  }])
function sessionCtrl($scope, $state, AuthorizationService, SweetAlert) {
  $scope.signin = function () {
    $state.go('user.signin');
  };
  $scope.email = "";
  $scope.password = "";
  $scope.submit = function () {

      AuthorizationService.SignInLocal($scope.email, $scope.password).then(function (response) {
          if (response.status=="1")
              $state.go('app.dashboard');
          else
          {
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

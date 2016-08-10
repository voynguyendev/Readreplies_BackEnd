'use strict';

/**
 * @ngdoc overview
 * @name urbanApp
 * @description
 * # urbanApp
 *
 * Main module of the application.
 */
angular
  .module('ReadrepliesAdmin', [
    'ui.router',
    'ngCookies',
    'ngAnimate',
    'ui.bootstrap',
    'oc.lazyLoad',
    'ngStorage',
    'ngSanitize',
    'ui.utils',
    'ngTouch',
    'oitozero.ngSweetAlert',
    'datatables',
    'ngMessages'
  ])
  .constant('COLORS', {
    'default': '#e2e2e2',
    primary: '#09c',
    success: '#2ECC71',
    warning: '#ffc65d',
    danger: '#d96557',
    info: '#4cc3d9',
    white: 'white',
    dark: '#4C5064',
    border: '#e4e4e4',
    bodyBg: '#e0e8f2',
    textColor: '#6B6B6B',
  })
 .constant('HOSTSERVER', {
     url: "http://realreplies.com/question_app_admin_api"
 })
;


var __promiseHandler = {
    Error: function (errors, status, deferer) {
        deferer.reject({ Status: status, Errors: errors })
    },

}

var __errorHandler = {
    ProcessErrors: function (errorObj) {
        if (errorObj.Status == 400) {
            if (errorObj.Errors.constructor == Array) {

                var messages = [];
                var codes = [];
                var exceptions = [];
                errorObj.Errors.forEach(function (error) {
                    messages.push(error.Message);
                    codes.push(error.Error);
                    if (error.Exception) {
                        exceptions.push(error.Exception);
                    }
                });

                return {
                    Messages: messages,
                    Codes: codes,
                    Status: errorObj.Status,
                    Exception: exceptions[0]
                }
            } else {
                return {
                    Messages: [],
                    Codes: [],
                    Status: errorObj.Status,
                    Exception: errorObj.Errors
                }
            }
        } else {
            return {
                Messages: [],
                Codes: [],
                Status: errorObj.Status,
                Exception: errorObj.Errors
            }
        }
    },
    Swal: function (errorObj, _sweetAlertService) {
        var message = '';
        if (errorObj.Messages.length) {
            message = '<ul style="text-align: left"><li>' + errorObj.Messages.join('</li><li>') + '</li></ul>';
        };

        if (errorObj.Exception) {
            message += '<div><div><b>Exception</b></div><textarea onclick="copyException(this)" class="form-control" rows="5">' + 'Exception Type: ' + errorObj.Exception.ClassName + '\nMessage: ' + errorObj.Exception.Message + '\nStack Trace: ' + errorObj.Exception.StackTraceString + '</textarea></div>';
        }

        _sweetAlertService.swal({
            title: 'errors',
            type: 'error',
            text: message,
            html: true
        })
    }
}


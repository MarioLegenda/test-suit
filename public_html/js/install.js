"use strict";


( function() {

    var suitApp = angular.module("InstallSuiteApp", []).config(['$provide', function($provide) {
        $provide.factory('$', function() {
            return jQuery;
        });

        $provide.factory('installFactory', function($http) {
            return {
                config : {},

                setConfig: function(config) {
                    this.config = config;
                    return this;
                },

                install: function() {
                    return $http(this.config);
                }
            };

        });
    }]);

    /*suitApp.factory('$', function() {
        return jQuery;
    });*/

    suitApp.controller('InformationController', function($scope, $) {
        $scope.showForm = function() {
            $('.InstallInformation').animate({
                top: '150%'
            }, 500);

            $('.InstallForm').animate({
                left: '50%'
            }, 500);
        }
    });

    suitApp.controller('InstallFormController', function($scope, installFactory) {
        $scope.install = {
            name: '',
            lastname: '',
            username: '',
            userPassword: '',
            userPassRepeat: '',

            installApp: function() {
                var promise = installFactory.setConfig({
                    method: 'POST',
                    url: '/installment',
                    data: $scope.install
                }).install();

                promise.success(function(data, status, headers, config) {
                });

                promise.error(function(data, status, headers, config) {

                });
            }
        };

        $scope.submit = function($event) {
            $event.preventDefault();

            $scope.name_invalid = $scope.insForm['name'].$invalid;
            $scope.lastname_invalid = $scope.insForm['lastname'].$invalid;
            $scope.username_invalid = $scope.insForm['username'].$invalid;
            $scope.email_invalid = $scope.insForm['username'].$error.email;
            $scope.no_password = $scope.insForm['password'].$error.required;
            $scope.illegal_password = $scope.insForm['password'].$error.minlength;
            $scope.no_pass_repeat = $scope.insForm['pass_repeat'].$error.required;
            $scope.illegal_pass_repeat = $scope.insForm['pass_repeat'].$error.minlength;
            $scope.pass_unequal = $scope.insForm['pass_repeat'].$viewValue !== $scope.insForm['password'].$viewValue;

            if($scope.name_invalid ||
                $scope.lastname_invalid ||
                $scope.username_invalid ||
                $scope.email_invalid ||
                $scope.no_password ||
                $scope.illegal_password ||
                $scope.no_pass_repeat ||
                $scope.illegal_pass_repeat ||
                $scope.pass_unequal) {
                $event.preventDefault();
                return false;
            }


            $scope.install.installApp();
        }
    });

} () );



"use strict";


( function() {

    var suitApp = angular.module("InstallSuiteApp", []).config(
        ['$provide', '$compileProvider', '$interpolateProvider', function($provide, $compileProvider, $interpolateProvider) {
            $interpolateProvider.startSymbol('{[{').endSymbol('}]}');

            $provide.factory('$', function () {
                return jQuery;
            });

            $provide.factory('installFactory', function ($http) {
                return {
                    config: {},

                    setConfig: function (config) {
                        this.config = config;
                        return this;
                    },

                    install: function () {
                        return $http(this.config);
                    }
                };
            });

            $provide.factory('$', function () {
                return jQuery;
            });

    }]);

    suitApp.controller('InformationController', ['$scope', '$', function($scope, $) {
        $scope.showForm = function() {
            $('.InstallInformation').animate({
                top: '150%'
            }, 500);

            $('.InstallForm').animate({
                left: '50%'
            }, 500);
        }
    }]);

    suitApp.controller('InstallFormController', ['$scope', 'installFactory', function($scope, installFactory) {
        $scope.globalErrors = {
            errors: [],
            show: false
        };

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
                promise.then(function(data, status, headers, config) {


                }, function(data, status, headers, config) {
                    var parsedData = JSON.parse(data.data);

                    $scope.globalErrors.errors = parsedData;

                    $scope.globalErrors.show = true;

                    console.log(parsedData[0].exception);
                });
            }
        };

        $scope.submit = function($event) {
            $event.preventDefault();

            $scope.name_invalid = $scope.insForm['name'].$invalid;
            $scope.lastname_invalid = $scope.insForm['lastname'].$invalid;
            $scope.username_invalid = $scope.insForm['username'].$error.required;
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
                return false;
            }

            return true;
        }
    }]);

} () );



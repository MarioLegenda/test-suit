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

            $provide.factory('formHandler', function() {
                var initForm = {};

                initForm.init = function($scope, formName) {
                    return {
                        equalChecks: 0,

                        notExists: function(prop) {
                            return $scope[formName][prop].$error.required && $scope[formName][prop].$dirty;
                        },

                        notMinLength: function(prop) {
                            return $scope[formName][prop].$error.minlength;
                        },

                        notEmail: function(prop) {
                            return $scope[formName][prop].$error.email;
                        },

                        notEquals: function(prop1, prop2) {
                            if($scope[formName][prop1].$viewValue !== $scope[formName][prop2].$viewValue) {
                                this.equalChecks++;
                                return true;
                            }

                            this.equalChecks = 0;
                            return false;
                        },

                        isValidForm: function() {
                            if($scope[formName].$valid && this.equalChecks == 0) {
                                return true;
                            }

                            return false;
                        }
                    };
                };

                return initForm;
            });

            $compileProvider.directive('processUiInstalling', function() {
                return {
                    restrict: 'EA',
                    replace: true,
                    template: "<p class='Process  Process--installing' ng-model='process.installing' ng-show='!process.installed && process.installing'>Please wait...</p>",
                    link: function(scope, elem, attrs) {
                    }
                }
            });

            $compileProvider.directive('processUiInstalled', function($timeout, $window) {
                return {
                    restrict: 'EA',
                    replace: true,
                    template: "<p class='Process  Process--installed' ng-show='process.installed'>" +
                    "Registration complete. You will be redirected to login page</p>",
                    link: function(scope, elem, attrs) {
                        scope.$watch(function() { return scope.process.installed }, function(oldValue, newValue, scope) {
                            if(scope.process.installed === true) {
                                $timeout(function() {
                                    $window.location.replace('/app_dev.php/suit-up');
                                }, 2000);
                            }
                        });
                    }
                }
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

    suitApp.controller('InstallFormController', ['$scope', 'installFactory', 'formHandler', function($scope, installFactory, formHandler) {
        $scope.globalErrors = {
            errors: [],
            show: false
        };

        $scope.process = {
            installing: false,
            installed: false
        };

        $scope.form = formHandler.init($scope, 'insForm');

        $scope.administrator = {
            name: '',
            lastname: '',
            username: '',
            userPassword: '',
            userPassRepeat: '',
        };

        $scope.install = {
            installApp: function() {
                $scope.process.installing = true;

                var promise = installFactory.setConfig({
                    method: 'POST',
                    url: '/installment',
                    data: $scope.administrator
                }).install();

                promise.then(function(data, status, headers, config) {
                    $scope.process.installed = true;
                    $scope.process.installing = false;
                }, function(data, status, headers, config) {
                    $scope.process.installing = false;
                    $scope.process.installed = false;
                    var parsedData = JSON.parse(data.data);

                    $scope.globalErrors.errors = parsedData;

                    $scope.globalErrors.show = true;

                    console.log(parsedData[0].exception);
                });
            }
        };

        $scope.install.submit = function($event) {
            $event.preventDefault();

            if($scope.form.isValidForm()) {
                $scope.install.installApp();
            }
        }
    }]);

} () );



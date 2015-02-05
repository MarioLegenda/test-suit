"use strict";

( function() {

    var suitApp = angular.module("inst.app", ['suite.directives', 'suite.factories']).config(['$interpolateProvider', function($interpolateProvider) {
            $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    }]);

    suitApp.controller('inst.informationCtrl', ['$scope', '$', function($scope, $) {
        $scope.showForm = function() {
            $('.InstallInformation').animate({
                top: '150%'
            }, 500);

            $('.InstallForm').animate({
                left: '50%'
            }, 500);
        }
    }]);

    suitApp.controller('inst.InstallFormCtrl', ['$scope', 'installFactory', 'formHandler', function($scope, installFactory, formHandler) {
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
            userPassRepeat: ''
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



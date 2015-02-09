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
            installed: false,
            url: false
        };

        $scope.theForm = formHandler.init($scope, 'insForm');

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
                    url: '/app_dev.php/installment',
                    data: $scope.administrator
                }).install();

                promise.then(function(data, status, headers, config) {
                    $scope.process.installed = true;
                    $scope.process.url = '/app_dev.php/suit-up'
                }, function(data, status, headers, config) {

                    var parsedData = JSON.parse(data.data);
                    $scope.globalErrors.errors = parsedData;

                });
            }
        };

        $scope.install.submit = function($event) {
            $event.preventDefault();

            if($scope.theForm.isValidForm()) {
                $scope.install.installApp();
            }
        }
    }]);

} () );



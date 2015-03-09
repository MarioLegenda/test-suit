"use strict";

( function() {

    var suitApp = angular.module("inst.app", ['suit.directives.components', 'suit.factories']).config(['$interpolateProvider', function($interpolateProvider) {
            $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    }]);

    suitApp.controller('inst.informationCtrl', ['$scope', '$', function($scope, $) {
        $scope.showForm = function() {
            $('.InstallInformation').animate({
                left: '-820px'
            }, 500);

            $('.InstallForm').animate({
                left: '260px'
            }, 500);
        }
    }]);

    suitApp.controller('inst.InstallFormCtrl', [
        '$scope',
        'installFactory',
        'formHandler',
        'DataMediator',
        'Path',
        '$timeout',
        function(
            $scope,
            installFactory,
            formHandler,
            DataMediator,
            Path,
            $timeout) {
        $scope.globalErrors = {
            errors: [],
            show: false
        };

        DataMediator.addMediateData('action-install', {
            message: 'Please wait...',
            submitProp: 'install',
            url: 'app_dev.php/suit-up',
            userSpecificClasses: 'ActionMoveRight',
            awaitClasses: 'ActionDecision',
            textValue: 'Install',
            awaitEvent: 'await-install'
        });

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
                $scope.$broadcast('await-install', {
                    awaiting: true
                });

                var promise = installFactory.setConfig({
                    method: 'POST',
                    url: Path.namespace('install.installment').construct(),
                    data: $scope.administrator
                }).install();

                promise.then(function(data, status, headers, config) {
                    $scope.$broadcast('await-install', {
                        awaiting: true,
                        message: 'Test suite is successfully installed. You will be redirected to login page in a moment'
                    });

                    $timeout(function() {
                        $scope.$broadcast('action-finished', {
                            awaiting: true,
                            redirect: true,
                            finished: true,
                            url: Path.namespace('install.login').construct()
                        });
                    }, 3000);
                }, function(data, status, headers, config) {
                    $scope.$broadcast('await-install', {
                        awaiting: false
                    });

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



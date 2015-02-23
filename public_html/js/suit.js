"use strict";

( function() {



    var suitApp = angular.module("suite.app", ['suite.directives', 'suite.factories']).config(['$interpolateProvider', function($interpolateProvider) {
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    }]);

    suitApp.controller('suite.userManagmentCtrl', ['$scope', 'User', function($scope, User) {

        $scope.users = [];
        $scope.userInfo = {};

        $scope.loading = {
            loading: false,
            loadingText: 'Loading user...',
            loaded: false
        };

        var promise = User.getAllUsers();
        promise.then(function(data, status, headers, config) {
            $scope.users = data.data.users;
        }, function(data, status, headers, config) {
            console.log('Something bad happend', data, status);
        });

    }]).controller('suite.testCreateCtrl', ['$scope', 'formHandler', 'TestControl', function($scope, formHandler, TestControl) {
        $scope.globalErrors = {
            errors: [],
            show: false
        };

        $scope.process = {
            installing: false,
            installed: false,
            redirectAfter: false,
            url: false
        };

        $scope.test = {
            test_name: '',
            current_permission: 'public',
            permission_items: ['public', 'restricted'],
            allowed_users: ['public'],
            remarks: '',
            submit: function($event) {
                $event.preventDefault();

                if($scope.theForm.isValidForm()) {
                    $scope.process.installing = true;
                    $scope.process.installing = true;
                    var promise = TestControl.saveTest();

                    promise.then(function(data, status, headers, config) {
                        console.log(data, status);
                        $scope.process.installing = true;
                        $scope.process.installed = true;
                    }, function(data, status, headers, config) {
                        console.log(data, status);
                        $scope.process.installing = false;
                        $scope.process.installed = false;
                        //$scope.globalErrors.show = true;

                        /*$scope.globalErrors.errors = data.data['errors'];
                        window.scrollTo(0 ,0);*/
                    });
                }
            }
        };

        $scope.theForm = formHandler.init($scope, 'CreateTestForm');
    }]);

} () );
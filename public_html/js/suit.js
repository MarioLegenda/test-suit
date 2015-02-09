"use strict";

( function() {



    var suitApp = angular.module("suite.app", ['suite.directives', 'suite.factories']).config(['$interpolateProvider', function($interpolateProvider) {
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    }]);

    suitApp.controller('suite.addUserCtrl', ['$scope', 'formHandler', 'User', '$window', function($scope, formHandler, User, $window) {

        $scope.globalErrors = {
            errors: [],
            show: false
        };

        $scope.process = {
            installing: false,
            installed: false,
            redirectAfter: true,
            url: false
        };

        $scope.theForm = formHandler.init($scope, 'AddUserForm');

        $scope.user = {
            name: '',
            lastname: '',
            username: '',
            userPassword: '',
            userPassRepeat: '',
            userPermissions: {
                role_test_solver: false,
                role_test_creator: false,
                role_user_manager: false ,
                examinePermissions: function(role) {
                    var roleInverse = function(arrToInverse, inverser) {
                        for(var i = 0; i < arrToInverse.length; i++) {
                            $scope.user.userPermissions[arrToInverse[i]] = inverser;
                        }
                    };

                    if(role === 'role_user_manager') {
                        roleInverse(['role_test_solver', 'role_test_creator'], $scope.user.userPermissions[role]);
                    }
                    else if(role === 'role_test_creator') {
                        roleInverse(['role_test_solver'], $scope.user.userPermissions[role]);
                        this.role_user_manager = false;
                    }
                }
            },
            fields: '',
            programming_languages: '',
            tools: '',
            years_of_experience: '',
            future_plans: '',
            description: '',
            submit: function($event) {
                $event.preventDefault();

                if($scope.theForm.isValidForm()) {
                    $scope.process.installing = true;
                    var promise = User.save($scope.user);

                    promise.then(function(data, status, headers, config) {
                        $scope.process.installing = true;
                        $scope.process.installed = true;
                        $scope.process.url = '/app_dev.php/user-managment';
                    }, function(data, status, headers, config) {
                        $scope.process.installing = false;
                        $scope.process.installed = false;
                        $scope.globalErrors.show = true;

                        $scope.globalErrors.errors = data.data['errors'];
                        window.scrollTo(0 ,0);
                    });
                }
            }
        };
    }]).controller('suite.userManagmentCtrl', ['$scope', 'User', function($scope, User) {

        $scope.users = [];

        $scope.process = {
            installing: false,
            installed: false,
            redirectAfter: true,
            url: false
        };

        $scope.managment = {
            expandAction: function($event, userId) {
                $event.preventDefault();


            }
        };

        var promise = User.getAllUsers();
        promise.then(function(data, status, headers, config) {
            $scope.users = data.data.users;
        }, function(data, status, headers, config) {
            console.log('Something bad happend', data, status);
        })

    }]);

} () );
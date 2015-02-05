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

        $scope.theForm = formHandler.init($scope, 'AddUserForm');

        $scope.user = {
            name: '',
            lastname: '',
            username: '',
            userPassword: '',
            userPassRepeat: '',
            userPermissions: {
                role_user: false,
                role_admin: false,
                role_super_admin: false ,
                examinePermissions: function(role) {
                    var roleInverse = function(arrToInverse, inverser) {
                        for(var i = 0; i < arrToInverse.length; i++) {
                            $scope.user.userPermissions[arrToInverse[i]] = inverser;
                        }
                    };

                    if(role === 'role_super_admin') {
                        roleInverse(['role_user', 'role_admin'], $scope.user.userPermissions[role]);
                    }
                    else if(role === 'role_admin') {
                        roleInverse(['role_user'], $scope.user.userPermissions[role]);
                        this.role_super_admin = false;
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
                    var promise = User.save($scope.user);

                    promise.then(function(data, status, headers, config) {
                    }, function(data, status, headers, config) {
                        $scope.globalErrors.show = true;

                        $scope.globalErrors.errors = data.data['errors'];
                        window.scrollTo(0 ,0);
                    });
                }
            }
        };
    }]);

} () );
"use strict";

angular.module('suite.directives', []).directive('processUiInstalling', function($window, $timeout) {
    return {
        restrict: 'EA',
        replace: true,
        template: "<p class='Process  Process--installing' ng-model='process.installing' ng-show='!process.installed && process.installing'>Please wait...</p>",
        link: function(scope, elem, attrs) {
            scope.$watch(function() { return scope.process.installed }, function(oldValue, newValue, scope) {
                if(scope.process.installed === true && scope.process.redirectAfter === true) {
                    $timeout(function() {
                        $window.location.replace(scope.process.url);
                    }, 1000);
                }
            });
        }
    }
}).directive('processUiInstalled', function($timeout, $window) {
    return {
        restrict: 'EA',
        replace: true,
        template: "<p class='Process  Process--installed' ng-show='process.installed'>" +
        "Registration complete. You will be redirected to login page</p>",
        link: function(scope, elem, attrs) {
            scope.$watch(function() { return scope.process.installed }, function(oldValue, newValue, scope) {
                if(scope.process.installed === true) {
                    $timeout(function() {
                        $window.location.replace(scope.process.url);
                    }, 2000);
                }
            });
        }
    }
}).directive('actionGlobalError', function() {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'globalError.html',
        controller: function($scope) {
            $scope.globalErrors = {
                show: false,
                errors: []
            };

            var scopeAssigner = function(values) {
                for(var action in values) {
                    if($scope.globalErrors.hasOwnProperty(action)) {
                        $scope.globalErrors[action] = values[action];
                    }
                }
            };

            $scope.$on('action-error', function(event, values) {
                scopeAssigner(values);
            });
        },
        link: function(scope, elem, attrs) {

        }
    }
}).directive('actionSubmit', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: true,
        templateUrl: 'actionSubmit.html',
        controller: function($scope, $window) {
            $scope.action = {
                awaiting: false,
                finished: false,
                redirect: false,
                url: null,
                submitProp: null,
                userSpecificClasses: null,
                message: 'Please wait...',
                submit: function($event) {
                    $scope.$parent[this.submitProp].submit($event);
                }
            };

            var scopeAssigner = function(values) {
                for(var action in values) {
                    if($scope.action.hasOwnProperty(action)) {
                        $scope.action[action] = values[action];
                    }
                }
            };

            $scope.$on('action-await', function(event, values) {
                scopeAssigner(values);
            });

            $scope.$on('action-initialization', function(event, values) {
                scopeAssigner(values);
            });

            $scope.$on('action-finished', function(event, values) {
                scopeAssigner(values);

                if($scope.action.awaiting === true && $scope.action.finished === true) {
                    if($scope.action.redirect === true) {
                        if(({}).toString.call($scope.action.url).match(/\s([a-zA-Z]+)/)[1].toLowerCase() !== 'string') {
                            throw new Error('If provided, url il actionSubmit directive has to be a url string')
                        }

                        $window.location.replace($scope.action.url);
                    }
                }
            });
        },
        link: function(scope, elem, attrs) {
        }
    };
}).directive('loading', function() {
    return {
        restrict: 'E',
        replace: true,
        link: function(scope, elem, attrs) {

        }
    }
}).directive('expandSection', function($, User, $timeout) {
    return {
        restrict: 'A',
        link : function(scope, elem, attrs) {
            var userId = attrs.userId;

            var animation = ( function () {

                var show = false;
                return {
                    toggle: function(elem, clickedElem) {
                        var h = elem.find('.UserManagment--info').outerHeight() + 50;
                        if(show === false) {
                            elem.animate({
                                height: h + 'px'
                            });

                            elem.css({
                                border: '1px solid #2C84EE'
                            });

                            clickedElem.find('.ExpandableClick').css({
                                backgroundColor: '#2C84EE'
                            });

                            show = true;
                        }
                        else if(show === true) {
                            elem.animate({
                                height: 45 + 'px'
                            });


                            elem.css({
                                border: '1px solid white'
                            });

                            clickedElem.find('.ExpandableClick').css({
                                backgroundColor: 'transparent'
                            });

                            show = false;
                        }
                    }
                };

            } () );

            elem.click(function(event) {
                event.preventDefault();

                var localThis = $(this);

                if( ! scope.userInfo.hasOwnProperty(userId)) {
                    scope.loading.loading = true;

                    var promise = User.getUsersById({
                        id: userId
                    }, true);

                    promise.then(function(data, status, headers, config) {
                        var userInfo = data.data.user;
                        scope.userInfo[userInfo.user_id] = userInfo;

                        scope.loading.loaded = !scope.loading.loaded;
                        scope.loading.loading = false;

                        $timeout(function() {
                            animation.toggle(elem, localThis);
                        }, 100);

                    }, function(data, status, headers, config) {
                        console.log(data, status);
                        animation.toggle(elem, localThis);
                    });
                }
                else {
                    $timeout(function() {
                        animation.toggle(elem, localThis);
                    }, 100);
                }

                return false;
            });
        }
    }
}).directive('testNaming', function(User, List, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'createTest.html',
        controller: function($scope, formHandler, TestControl) {
            $scope.theForm = formHandler.init($scope, 'CreateTestForm');

            $scope.globalErrors = {
                errors: [],
                show: false
            };

            $timeout(function() {
                $scope.$broadcast('action-initialization', {
                    message: 'Please wait...',
                    userSpecificClasses: 'TempSubmitButton',
                    submitProp: 'test'
                });
            }, 200);

            $scope.available_users = null;

            $scope.test = {
                test_name: '',
                current_permission: 'public',
                permission_items: ['public', 'restricted'],
                allowed_users: [],
                remarks: '',
                submit: function($event) {

                    $event.preventDefault();
                    if($scope.theForm.isValidForm()) {
                        $scope.$broadcast('action-await', {
                            awaiting: true
                        });

                        var promise = TestControl.saveTest({
                            test_name: $scope.test.test_name,
                            test_solvers: $scope.test.allowed_users,
                            remarks: $scope.test.remarks
                        });

                        promise.then(function (data, status, headers, config) {
                            $scope.$broadcast('action-finished', {
                                awaiting: true,
                                finished: true
                            });
                        }, function (data, status, headers, config) {
                            $scope.$broadcast('action-await', {
                                awaiting: false,
                                finished: false,
                                redirect: false
                            });

                            $scope.$broadcast('action-error', {
                                show: true,
                                errors: data.data['errors']
                            });

                            window.scrollTo(0, 0);
                        });
                    }
                }
            };

            $scope.managment = {
                addUser: function (user) {
                    $scope.test.allowed_users = List.add(user, 'user_id');
                },
                removeUser: function (user) {
                    $scope.test.allowed_users = List.remove(user, 'user_id');
                }
            };

            $scope.$watch(function () { return $scope.test.current_permission }, function (newVal, oldVal, scope) {
                if (scope.available_users === null && scope.test.current_permission === 'restricted') {
                    var promise = User.getAllUsers();
                    promise.then(function (data, status, headers, config) {
                        scope.available_users = data.data.users;
                    }, function (data, status, headers, config) {
                        console.log('Something bad happend', data, status);
                    });
                }
            });
        },
        link: function(scope, elem, attrs) {
        }
    }
}).directive('createUser', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: true,
        templateUrl: 'createUserTemplate.html',
        controller: function($scope, formHandler, User, $timeout) {
            $timeout(function() {
                $scope.$broadcast('action-initialization', {
                    message: 'Please wait...',
                    submitProp: 'user'
                });
            }, 200);

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
                    role_test_solver: false,
                    role_test_creator: false,
                    role_user_manager: false,
                    examinePermissions: function (role) {
                        var roleInverse = function (arrToInverse, inverser) {
                            for (var i = 0; i < arrToInverse.length; i++) {
                                $scope.user.userPermissions[arrToInverse[i]] = inverser;
                            }
                        };

                        if (role === 'role_user_manager') {
                            roleInverse(['role_test_solver', 'role_test_creator'], $scope.user.userPermissions[role]);
                        }
                        else if (role === 'role_test_creator') {
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
                submit: function ($event) {
                    $event.preventDefault();

                    $event.preventDefault();
                    if($scope.theForm.isValidForm()) {
                        $scope.$broadcast('action-await', {
                            awaiting: true
                        });

                        var promise = User.save($scope.user);

                        promise.then(function (data, status, headers, config) {
                            $scope.$broadcast('action-finished', {
                                awaiting: true,
                                finished: true,
                                redirect: true,
                                url: '/app_dev.php/user-managment'
                            });
                        }, function (data, status, headers, config) {
                            $scope.$broadcast('action-await', {
                                awaiting: false,
                                finished: false,
                                redirect: false
                            });

                            $scope.globalErrors.show = true;

                            $scope.globalErrors.errors = data.data['errors'];
                            window.scrollTo(0, 0);
                        });
                    }
                }
            };
        },
        link: function(scope, elem, attrs) {

        }
    }
});
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
}).directive('userRestriction', function($, User, List) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            permission: '@permission',
            allowedUsers: '=allowedUsers'
        },
        templateUrl: 'permissionTemplate.html',
        link: function(scope, elem, attrs) {
            scope.available_users = null;
            scope.managment = {
                addUser: function(user) {
                    scope.allowedUsers = List.add(user, 'user_id');
                },
                removeUser: function(user) {
                    console.log('idiot');
                    scope.allowedUsers = List.remove(user, 'user_id');
                }
            };

            scope.$watch(function() { return scope.permission }, function(newVal, oldVal, scope) {
                if(scope.available_users === null && scope.permission === 'restricted') {
                    var promise = User.getAllUsers();
                    promise.then(function(data, status, headers, config) {
                        scope.available_users = data.data.users;
                    }, function(data, status, headers, config) {
                        console.log('Something bad happend', data, status);
                    });
                }
            });
        }
    }
});
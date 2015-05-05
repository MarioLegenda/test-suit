"use strict";

angular.module('suit.directives.actions')
    .directive('testListing', ['Test', 'Pagination', function(Test, Pagination) {
        return {
            restrict: 'E',
            scope: true,
            replace: true,
            templateUrl: 'testListing.html',
            controller: function($scope) {
                $scope.directiveData = {
                    modify: false,
                    testInfo: {},
                    tests: [],
                    loaded: false,
                    loadMore: true,
                    directiveType: 'test-row',

                    deleteTest: function($event, id) {
                        $event.preventDefault();

                        var promise = Test.deleteTest(id);

                        promise.then(function(data, status, headers, config) {
                            $scope.directiveData.tests = data.data.tests;
                        }, function(data, status, headers, config) {
                            console.log('Something bad happend', data, status);
                        });
                    }
                };

                var pagination = Pagination.init(1, 10);

                var promise = Test.getTestsListing();
                promise.then(function(data, status, headers, config) {
                    var tests = data.data.tests;
                    $scope.directiveData.tests = tests;
                    $scope.directiveData.loaded = true;
                    if (tests.length < pagination.constant.end) {
                        $scope.directiveData.loadMore = false;
                    }
                }, function(data, status, headers, config) {
                    console.log('Something bad happend', data, status);
                });

                $scope.$on('action-load-more', function (event, data) {
                    var promise = Test.getPaginatedUsers(pagination.nextPagination());

                    promise.then(function (data, status, headers, config) {
                        var users = data.data.users;
                        if (users.length < pagination.constant.end) {
                            $('.LoadMore').remove();
                        }

                        for (var user in users) {
                            if (users.hasOwnProperty(user)) {
                                $scope.directiveData.users.push(users[user]);
                            }
                        }

                        $scope.directiveData.loaded = true;
                    }, function (data, status, headers, config) {

                    });
                });

                $scope.$on('action-delete-test', function(event, data) {
                    var promise = Test.deleteTest(data.id);

                    promise.then(function(data, status, headers, config) {
                        $scope.directiveData.tests = data.data.tests;
                    }, function(data, status, headers, config) {
                        console.log('Something bad happend', data, status);
                    });
                });
            },
            link: function(scope, elem, attrs) {
            }
        }
    }]).directive('userListing', ['User', 'Pagination', function(User, Pagination) {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'userListing.html',
            controller: function ($scope) {
                $scope.directiveData = {
                    users: [],
                    loaded: false,
                    directiveType: 'user-row',
                    loadMore: true
                };

                var pagination = Pagination.init(1, 10);

                var promise = User.getPaginatedUsers(pagination.currentPagination());
                promise.then(function (data, status, headers, config) {
                    var users = data.data.users;
                    $scope.directiveData.users = users;
                    $scope.directiveData.loaded = true;

                    if (users.length < pagination.constant.end) {
                        $scope.directiveData.loadMore = false;
                    }
                }, function (data, status, headers, config) {
                    console.log('Something bad happend', data, status);
                });

                $scope.$on('action-load-more', function (event, data) {
                    if ($scope.directiveData.loadMore === true) {
                        var promise = User.getPaginatedUsers(pagination.nextPagination());

                        promise.then(function (data, status, headers, config) {
                            var users = data.data.users;
                            if (users.length < pagination.constant.end) {
                                $scope.directiveData.loadMore = false;
                                $('.LoadMore').remove();
                            }

                            for (var user in users) {
                                if (users.hasOwnProperty(user)) {
                                    $scope.directiveData.users.push(users[user]);
                                }
                            }

                            $scope.directiveData.loaded = true;
                        }, function (data, status, headers, config) {

                        });
                    }
                });

                $scope.$on('action-user-filter', function (event, data) {
                    var promise = User.filter(data);

                    promise.then(function (data, status, headers, config) {
                        $scope.directiveData.users = data.data.users;
                    }, function (data, status, headers, config) {
                        console.log(data, data.status);
                    });
                });
            }
        }
    }]).directive('assignedTestsListing', ['Pagination', 'Test', function(Pagination, Test) {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'assignedTestListing.html',
            controller: function($scope) {
                $scope.directiveData = {
                    tests: [],
                    loaded: false,
                    directiveType: 'assigned-test-row',
                    loadMore: true
                };

                var pagination = Pagination.init(1, 10);
                var promise = Test.getPaginatedPermittedTests(pagination.currentPagination());

                promise.then(function(data, status, headers, config) {
                    $scope.directiveData.tests = data.data.tests;
                    $scope.directiveData.loaded = true;

                    if (data.data.tests.length < pagination.constant.end) {
                        $scope.directiveData.loadMore = false;
                    }
                }, function(data, status, headers, config) {
                    console.log('Something went wrong', data);
                });
            },
            link: function($scope, elem, attrs) {

            }
        }
    }]);
angular.module('suit.factories')
    .factory('User', ['$http', 'Path', function($http, Path) {
        return {
            save: function(user) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('user.saveUrl').construct(),
                    data: user
                });
            },

            getUsersById: function(id, cache) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('user.userInfo').construct(),
                    data: {id: id}
                });
            },

            getPaginatedUsers: function(pagination) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('user.paginatedUsers').construct(),
                    data: pagination
                });
            },

            filter: function(filterData) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('user.userFilter').construct(),
                    data: filterData
                });
            },

            getUserByUsername: function(username) {

            },

            deleteUserById: function(id) {

            },

            deleteUserByUsername: function(username) {

            },

            getAllUsers: function() {
                return $http({
                    method: 'POST',
                    url: Path.namespace('user.allUsersUrl').construct()
                });
            }
        };
    }])
    .factory('Test', ['$http', 'Path', function($http, Path) {

        function Test() {
            this.createTest =  function(data) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.createTestUrl').construct(),
                    data: data
                });
            };

            this.saveTest = function(id, data) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.saveTestUrl').construct() + id,
                    data: data
                });
            };

            this.deleteTest = function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.deleteTest').construct(),
                    data: {id: id}
                });
            };

            this.modifyTest = function(data) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.modifyTest').construct(),
                    data: data
                });
            };

            this.updateTest = function(id, data) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.updateTest').construct(),
                    data: {
                        id: id,
                        test: data
                    }
                });
            };

            this.deleteQuestion = function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.deleteQuestion').construct(),
                    data: {id: id}
                });
            };

            this.finishTest = function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.finishTest').construct(),
                    data: {id: id}
                });
            };

            this.getTest = function(id, testControlId) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getTestUrl').construct(),
                    data: ( function() {
                        return (testControlId === null) ? {test_id: id} : {test_id: id, test_control_id: testControlId};
                    } () )
                });
            };

            this.getTestPermissions = function(testControlId) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.testPermissions').construct(),
                    data: {test_control_id: testControlId}
                });
            };

            this.workspaceData = function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.workspaceData').construct(),
                    data: {id: id}
                });
            };

            this.getBasicTests = function() {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getBasicTests').construct()
                });
            };

            this.getPermittedUsers = function(userIds) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getPermittedUsers').construct(),
                    data: userIds
                });
            };

            this.getBasicTestById = function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getBasicTestById').construct(),
                    data: {test_control_id : id}
                });
            }
        }

        return new Test();

    }]);

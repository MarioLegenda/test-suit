angular.module('suit.factories')
    .factory('User', ['$http', 'Path', function($http, Path) {
        return {
            createUser: function(user) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('user.createUser').construct(),
                    data: user
                });
            },

            getUserInfoById: function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('user.userInfo').construct(),
                    data: {user_id: id}
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
            }
        };
    }])
    .factory('Test', ['$http', 'Path', function($http, Path) {

        function Test() {
            this.createTest =  function(data) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.createTest').construct(),
                    data: data
                });
            };

            this.deleteTest = function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.deleteTest').construct(),
                    data: {test_control_id: id}
                });
            };

            this.modifyTest = function(data) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.modifyTest').construct(),
                    data: data
                });
            };

            this.getPermittedUsers = function(testControlId) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getPermittedUsers').construct(),
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

            this.getTestsListing = function() {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getTestsListing').construct()
                });
            };

            this.getTestPermissionType = function(testId) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getPermissionType').construct(),
                    data: {test_control_id: testId}
                });
            };

            this.getAssignedTestUsersIds = function(testControlId) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.assignedUsersIds').construct(),
                    data: {test_control_id: testControlId}
                });
            };

            this.getPermittedUsers = function(userIds) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getPermittedUsers').construct(),
                    data: userIds
                });
            };

            this.getPaginatedPermittedTests = function(pagination) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('test.getPermittedTests').construct(),
                    data: pagination
                });
            }
        }

        return new Test();

    }])
    .factory('Workspace', ['$http', 'Path', function($http, Path) {
        function Workspace() {
            this.workspaceData = function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('workspace.workspaceData').construct(),
                    data: {test_control_id: id}
                });
            };

            this.finishTest = function(id, status) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('workspace.finishTest').construct(),
                    data: {
                        test_control_id: id,
                        status: status
                    }
                });
            };

            this.getTest = function(id, testControlId) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('workspace.getTest').construct(),
                    data: ( function() {
                        return (testControlId === null) ? {test_id: id} : {test_id: id, test_control_id: testControlId};
                    } () )
                });
            };

            this.saveTest = function(data) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('workspace.saveTest').construct(),
                    data: data
                });
            };

            this.updateTest = function(id, data) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('workspace.updateTest').construct(),
                    data: {
                        test_id: id,
                        test_data: data
                    }
                });
            };

            this.deleteQuestion = function(id) {
                return $http({
                    method: 'POST',
                    url: Path.namespace('workspace.deleteQuestion').construct(),
                    data: {test_id: id}
                });
            };
        }

        return new Workspace();
    }]);

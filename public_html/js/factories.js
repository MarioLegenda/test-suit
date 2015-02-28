"use strict";

angular.module('suite.factories', []).factory('$', function () {
    return jQuery;
}).factory('installFactory', function ($http) {
    return {
        config: {},

        setConfig: function (config) {
            this.config = config;
            return this;
        },

        install: function () {
            return $http(this.config);
        }
    };
}).factory('formHandler', function() {
    var initForm = {};

    initForm.init = function($scope, formName) {
        return {
            invalidSum: 0,
            invalidForm: null,

            notExists: function(prop) {
                return $scope[formName][prop].$error.required && $scope[formName][prop].$dirty;
            },

            notMinLength: function(prop) {
                return $scope[formName][prop].$error.minlength;
            },

            notEmail: function(prop) {
                return $scope[formName][prop].$error.email;
            },

            notEquals: function(prop1, prop2) {
                if($scope[formName][prop1].$viewValue !== $scope[formName][prop2].$viewValue) {
                    this.invalidSum++;
                    return true;
                }

                this.invalidSum = 0;
                this.invalidForm = true;
                return false;
            },

            enforceChecked: function(numOfCheckboxes, arrOfBoxes) {
                var checked = 0;
                for(var i = 0; i < arrOfBoxes.length; i++) {
                    if($scope[formName][arrOfBoxes[i]].$modelValue == true) {
                        checked++;
                    }
                }

                return checked === 0;
            },

            hasArrayValues: function(arr) {
                var isArray = ({}).toString.call(arr).match(/\s([a-zA-Z]+)/)[1].toLowerCase()
                if(isArray === 'array') {
                    return arr.length === 0;
                }

                return false;
            },

            notValidArray: function(arr, value) {
                for(var i = 0; i < arr.length; i++) {
                    if(arr[i] === value) {
                        return true;
                    }
                }

                return false;
            },

            notEmpty: function(arr, value) {
                if(arr.length === 0) {
                    this.invalidForm = true;
                    return true;
                }

                this.invalidForm = false;
                return false;
            },

            regexValid: function(prop) {
                return $scope[formName][prop].$error.pattern;
            },

            isValidForm: function() {
                if($scope[formName].$valid && this.invalidForm === true) {
                    return true;
                }

                if($scope[formName].$valid && this.invalidForm === null) {
                    return true;
                }

                if($scope[formName].$valid && this.invalidForm === false) {
                    return true;
                }

                return false;
            }
        };
    };

    return initForm;
}).factory('User', function($http) {
    return {
        urls: {
            saveUrl: '/app_dev.php/user-managment/save-user',
            allUsersUrl: '/app_dev.php/user-managment/user-list',
            userInfo: '/app_dev.php/user-managment/user-info'
        },

        save: function(user) {
            return $http({
                method: 'POST',
                url: this.urls.saveUrl,
                data: user
            });
        },

        getUsersById: function(id, cache) {
            cache = typeof cache !== 'undefined';
            return $http({
                method: 'POST',
                url: this.urls.userInfo,
                data: id,
                cache: cache
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
                url: this.urls.allUsersUrl
            });
        }
    };
}).factory('List', function() {
    function List() {
        var list = [],
            index = 0;

        this.add = function(object, compareWith) {
            if(list.length === 0) {
                list[0] = object;
                return list;
            }

            var itemExists = false;
            for(var i = 0; i < list.length; i++) {
                var item = list[i];

                if(item[compareWith] === object[compareWith]) {
                    itemExists = true;
                }
            }

            if( ! itemExists) {
                list[list.length] = object;
            }

            return list;
        };

        this.remove = function(object, compareWith) {
            for(var i = 0; i < list.length; i++) {
                var item = list[i];

                if(item[compareWith] === object[compareWith]) {
                    list.splice(i, 1);
                    break;
                }
            }

            return list;
        };

        this.clear = function() {

        };
    }

    return new List();
}).factory('Test', function($http) {

    function Test() {
        var urls = {
            createTestUrl: '/app_dev.php/test-managment/create-test',
            saveTestUrl: '/app_dev.php/test-managment/save-test/',
            getTestUrl: '/app_dev.php/test-managment/get-test',
            getBasicTests: '/app_dev.php/test-managment/get-tests-basic',
            getBasicTestById: '/app_dev.php/test-managment/get-test-basic',
            deleteTest: '/app_dev.php/test-managment/delete-test',
            modifyTest: '/app_dev.php/test-managment/modify-test'
        };

        this.createTest =  function(data) {
            return $http({
                method: 'POST',
                url: urls.createTestUrl,
                data: data
            });
        };

        this.saveTest = function(id, data) {
            console.log(data);
            return $http({
                method: 'POST',
                url: urls.saveTestUrl + id,
                data: data
            });
        };

        this.deleteTest = function(id) {
            return $http({
                method: 'POST',
                url: urls.deleteTest,
                data: {id: id}
            });
        };

        this.modifyTest = function(data, type) {
            return $http({
                method: 'POST',
                url: ( function() {
                    return (type === 'create') ? urls.createTestUrl : urls.modifyTest
                } () ),
                data: data
            });
        };

        this.getTest = function(id) {
            return $http({
                method: 'POST',
                url: urls.getTestUrl,
                data: [id],
                cache: true
            });
        };

        this.getBasicTests = function() {
            return $http({
                method: 'POST',
                url: urls.getBasicTests,
                cache: true
            });
        };

        this.getBasicTestById = function(id) {
            return $http({
                method: 'POST',
                url: urls.getBasicTestById,
                data: {id : id}
            });
        }
    }

    return new Test();

}).factory('DataShepard', function($) {
    function DataShepard() {
        var counter = 0,
            heard = [],
            precompiled = false;

        this.setPrecompiled = function() {
            precompiled = true;
        };

        this.isPreCompiled = function() {
            return precompiled;
        };

        this.current = function() {
            return counter;
        };

        this.remove = function(id) {
            heard.splice(id, 1);
        };

        this.next = function() {
            counter++;
        };

        this.add = function(id, object) {
            heard[id] = object;
            return this;
        };

        this.get = function(id) {
            for(var i = 0; i < heard.length; i++) {
                var block = heard[i];
                if(block.blockId == id) {
                    return heard[i];
                }
            }

            throw new Error('DataShepard: ' + id + ' was not found');
        };

        this.all = function() {
            return heard;
        };

        this.clear = function() {
            counter = 0;
            heard = [];
        };
    }

    return new DataShepard();
}).factory('Types', function() {
    function Types() {
        var metadata = {
            answer: {
                placeholders: {
                    'plain-text-block': "Your answer goes here. Leave it blank beacuse this element is meant for the test solver...",
                    'select-block': "Text for one select element goes here...",
                    'checkbox-block': "Text for one checkbox element goes here...",
                    'radio-block': "Text for one radio button goes here..."
                }
            },
            question: {
                placeholders: {
                    'code-block': null,
                    'plain-text-block': "Type your question here..."
                }
            }
        };

        this.createType = function(id, type, dataType, directiveType) {
            var constructingType = {};

            constructingType.dataType = dataType;
            constructingType.blockType = type;
            constructingType.element = 'textarea';
            constructingType.blockId = id;
            constructingType.placeholder = metadata[type].placeholders[directiveType];
            constructingType.directiveType = directiveType;
            constructingType.data = {
                type: directiveType,
                data: null
            };

            if(dataType === 'object') {
                constructingType.data.selected = null;
                constructingType.data.data = [];
            }

            return constructingType;
        }
    }

    return new Types();
}).factory('CompileCommander', function($compile) {
    function CompileCommander() {
        this.compile = function($scope) {

            switch($scope.directiveData.directiveType) {
                case 'plain-text-block':
                    return $compile("<plain-text-suit-block block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></plain-text-suit-block>")($scope.$new(false, $scope));
                case 'code-block':
                    return $compile("<code-block-suite block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></code-block-suite>")($scope.$new(false, $scope));
                case 'select-block':
                case 'checkbox-block':
                case 'radio-block':
                    return $compile("<generic-block-suit block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></generic-block-suit>")($scope.$new(false, $scope));

            }
        }
    }

    return new CompileCommander();
}).factory('DataMediator', function() {
    function DataMediator() {
        var mediateData = {};

        this.addMediateData = function(dataKey, data) {
            mediateData[dataKey] = data;
        };

        this.getMediateData = function(dataKey) {
            if(mediateData.hasOwnProperty(dataKey)) {
                return mediateData[dataKey];
            }

            return null;
        };

        this.clear = function() {
            mediateData = {};
        }
    }

    return new DataMediator();
});
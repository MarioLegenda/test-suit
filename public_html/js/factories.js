"use strict";

angular.module('suit.factories', []).factory('$', function () {
    return jQuery;
}).factory('Path', ['$location', function($location) {
    function Path() {
        var environment = null,
            domain = null,
            path = null,
            url = null;

        var namespaces = {
            user: {
                saveUrl : 'user-managment/save-user',
                allUsersUrl: 'user-managment/user-list',
                userInfo: 'user-managment/user-info'
            },
            test: {
                createTestUrl: 'test-managment/create-test',
                saveTestUrl: 'test-managment/save-test/',
                getTestUrl: 'test-managment/get-test',
                getBasicTests: 'test-managment/get-tests-basic',
                getBasicTestById: 'test-managment/get-test-basic',
                deleteTest: 'test-managment/delete-test',
                deleteQuestion: 'test-managment/delete-question',
                modifyTest: 'test-managment/modify-test',
                updateTest: 'test-managment/update-test',
                finishTest: 'test-managment/finish-test',
                workspaceData: 'test-managment/workspace-data'
            },
            install: {
                installment: 'installment',
                login: 'suit-up'
            }
        };

        this.namespace = function(nms) {
            var splitted = nms.split('.');
            var namespaceType = splitted[0],
                namespaceUrl = splitted[1];

            if( ! namespaces.hasOwnProperty(namespaceType)) {
                throw new Error('Unknown namespace ' + namespaceType);
            }

            if( ! namespaces[namespaceType].hasOwnProperty(namespaceUrl)) {
                throw new Error('Unknown url ' + namespaceUrl);
            }

            url = namespaces[namespaceType][namespaceUrl];
            return this;
        };

        this.construct = function() {
            if(environment === null || path === null || url === null) {
                throw new Error('Url cannot be constructed beacuse some of the parameters are null');
            }
            
            return domain + path + environment + url;
        };

        if($location.host() === 'localhost') {
            domain = $location.protocol() + '://' + $location.host() + ':' + $location.port();
            path = $location.absUrl().slice(domain.length);

            if(/install\/sign-up/.test(path)) {
                path = path.replace(/install\/sign-up/, '');
            }

            if(/app_dev.php\/?/.test(path)) {
                path = path.replace(/app_dev.php\/?/, '');
                environment = 'app_dev.php/';
            }
            else {
                environment = '';
            }

            return this;
        }

        domain = $location.protocol() + '://' + $location.host() + ':' + $location.port();
        path = '/';
        environment = (/app_dev.php\/?/.test($location.absUrl())) ? 'app_dev.php/' : '';
    }

    return new Path();
}]).factory('installFactory', function ($http) {
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
}).factory('User', ['$http', 'Path', function($http, Path) {
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
}]).factory('List', function() {
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
}).factory('Test', ['$http', 'Path', function($http, Path) {

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

        this.modifyTest = function(data, type) {
            return $http({
                method: 'POST',
                url: ( function() {
                    return (type === 'create') ?
                        Path.namespace('test.createTestUrl').construct() :
                        Path.namespace('test.modifyTest').construct();
                } () ),
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
                },
                cache: true
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

        this.workspaceData = function(id) {
            return $http({
                method: 'POST',
                url: Path.namespace('test.workspaceData').construct(),
                data: {id: id},
                cache: true
            });
        };

        this.getBasicTests = function() {
            return $http({
                method: 'POST',
                url: Path.namespace('test.getBasicTests').construct(),
                cache: true
            });
        };

        this.getBasicTestById = function(id) {
            return $http({
                method: 'POST',
                url: Path.namespace('test.getBasicTestById').construct(),
                data: {id : id}
            });
        }
    }

    return new Test();

}]).factory('DataShepard', function($) {
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

        this.syncCurrentId = function() {
            var highest = 0;
            for(var i = 0; i < heard.length; i++) {
                var blockId = parseInt(heard[i].blockId);
                if(blockId > highest) {
                    highest = blockId;
                }
            }

            counter = ++highest;
        };

        this.current = function() {
            return counter;
        };

        this.remove = function(id) {
            for(var i = 0; i < heard.length; i++) {
                var block = heard[i];
                if(block.blockId == id) {
                    heard.splice(i, 1);
                    break;
                }
            }
        };

        this.next = function() {
            counter++;
        };

        this.add = function(id, object) {
            heard[heard.length] = object;
            return this;
        };

        this.update = function(id, object) {
            for(var i = 0; i < heard.length; i++) {
                var block = heard[i];
                if(block.blockId == id) {
                    heard[i] = object;
                    break;
                }
            }
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
            var tempHeard = [];
            for(var i = 0; i < heard.length; i++) {
                if(typeof heard[i] !== 'undefined') {
                    tempHeard.push(heard[i]);
                }
            }

            return tempHeard;
        };

        this.arangeBlockIds = function() {
            for(var i = 0; i < heard.length; i++) {
                heard[i].blockId = i;
            }
        };

        this.clearHeard = function() {
            heard = [];
            return this;
        };

        this.clearCounter = function() {
            counter = 0;
            return this;
        };

        this.instance = function() {
            return new DataShepard();
        }
    }

    return new DataShepard();
}).factory('RangeIterator', function(){
    return {
        initIterator: function(arr) {
            return ( function(arr) {
                function RangeIterator(arr) {
                    var range = arr,
                        counter = 0;

                    this.currentCounter = function() {
                        return counter + 1;
                    };

                    this.lastCounter = function() {
                        return range.length + 1;
                    };

                    this.current = function() {
                        return range[counter];
                    };

                    this.next = function() {
                        counter++;

                        if(typeof range[counter] === 'undefined') {
                            counter = 0;
                            return range[counter];
                        }

                        return range[counter];
                    };

                    this.previous = function() {
                        counter--;
                        if(counter < 0) {
                            counter = range.length - 1;
                        }

                        if(typeof range[counter] === 'undefined') {
                            counter = 0;
                            return range[counter];
                        }

                        return range[counter];
                    };
                }

                return new RangeIterator(arr);
            } (arr) )
        }
    }
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
                case 'code-answer-block':
                    return $("<code-suit-answer-block non-compiled='{[{ directiveData.nonCompiled }]}' block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></code-suit-answer-block>");
                case 'plain-text-answer-block':
                    return $("<plain-text-suit-answer-block non-compiled='{[{ directiveData.nonCompiled }]}'  block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></plain-text-suit-answer-block>");
                case 'plain-text-block':
                    return $("<plain-text-suit-block non-compiled='{[{ directiveData.nonCompiled }]}'  block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></plain-text-suit-block>");
                case 'code-block':
                    return $("<code-block-suite non-compiled='{[{ directiveData.nonCompiled }]}'  block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></code-block-suite>");
                case 'select-block':
                case 'checkbox-block':
                case 'radio-block':
                    return $("<generic-block-suit non-compiled='{[{ directiveData.nonCompiled }]}'  block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></generic-block-suit>");

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
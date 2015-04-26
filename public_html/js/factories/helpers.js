angular.module('suit.factories')
    .factory('Path', ['$location', function ($location) {
        function Path() {
            var environment = null,
                domain = null,
                path = null,
                url = null;

            var namespaces = {
                user: {
                    createUser: 'user-managment/create-user',
                    userInfo: 'user-managment/user-info',
                    paginatedUsers: 'user-managment/user-list-paginated',
                    userFilter: 'user-managment/user-filter'
                },
                test: {
                    assignedUsersIds: 'test-managment/get-assigned-users-ids',
                    createTest: 'test-managment/create-test',
                    getTestsListing: 'test-managment/get-tests-listing',
                    getPermissionType: 'test-managment/get-permission-type',
                    getBasicTestById: 'test-managment/get-test-basic',
                    deleteTest: 'test-managment/delete-test',
                    modifyTest: 'test-managment/create-test',
                    getPermittedUsers: 'test-managment/get-permitted-users'
                },
                workspace: {
                    workspaceData: 'test-managment/workspace-data',
                    finishTest: 'test-managment/finish-test',
                    getTest: 'test-managment/get-test',
                    saveTest: 'test-managment/save-test',
                    updateTest: 'test-managment/update-test',
                    deleteQuestion: 'test-managment/delete-question'
                },
                install: {
                    installment: 'installment',
                    login: 'suit-up'
                }
            };

            this.namespace = function (nms) {
                var splitted = nms.split('.');
                var namespaceType = splitted[0],
                    namespaceUrl = splitted[1];

                if (!namespaces.hasOwnProperty(namespaceType)) {
                    throw new Error('Unknown namespace ' + namespaceType);
                }

                if (!namespaces[namespaceType].hasOwnProperty(namespaceUrl)) {
                    throw new Error('Unknown url ' + namespaceUrl);
                }

                url = namespaces[namespaceType][namespaceUrl];
                return this;
            };

            this.construct = function () {
                if (environment === null || path === null || url === null) {
                    throw new Error('Url cannot be constructed beacuse some of the parameters are null');
                }

                return domain + path + environment + url;
            };

            if ($location.host() === 'localhost') {
                domain = $location.protocol() + '://' + $location.host() + ':' + $location.port();
                path = $location.absUrl().slice(domain.length);

                if (/install\/install-test-suit/.test(path)) {
                    path = path.replace(/install\/install-test-suit/, '');
                }

                if (/app_dev.php\/?/.test(path)) {
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
    }])
    .factory('formHandler', function() {
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
    })
    .factory('List', function() {
        function List() {
            this.add = null;
            this.remove = null;
        }

        List.prototype.list = [];
        List.prototype.index = 0;

        List.prototype.clear = function() {
            this.list = [];
            this.index = [];
        };

        List.prototype.all = function() {
            return this.list;
        };

        List.prototype.isEmpty = function() {
            return this.list.length === 0;
        };

        List.prototype.hasEntry = function(value) {
            for(var i = 0; i < this.list.length; i++) {
                if(value === this.list[i]) {
                    return true;
                }
            }

            return false;
        };

        function ListFactory() {
            var list = new List();

            this.createSimple = function() {
                list.add = function (value) {
                    if(this.hasEntry(value)) {
                        return;
                    }

                    this.list[this.list.length] = value;
                };

                list.remove = function (value) {
                    for (var i = 0; i < this.list.length; i++) {
                        if (this.list[i] === value) {
                            this.list.splice(i, 1);
                        }
                    }
                };

                list.multipleAdd = function(multiArr) {
                    for(var i = 0; i < multiArr.length; i++) {
                        if( ! this.hasEntry(multiArr[i])) {
                            this.list[this.list.length] = multiArr[i];
                        }
                    }
                };

                return list;
            };

            this.createComplex = function() {
                list.add = function (object, compareWith) {
                    var list = new List();

                    if (list.length === 0) {
                        list[0] = object;
                        return list;
                    }

                    var itemExists = false;
                    for (var i = 0; i < list.length; i++) {
                        var item = list[i];

                        if (item[compareWith] === object[compareWith]) {
                            itemExists = true;
                        }
                    }

                    if (!itemExists) {
                        list[list.length] = object;
                    }

                    return list;
                };

                list.remove = function (object, compareWith) {
                    for (var i = 0; i < list.length; i++) {
                        var item = list[i];

                        if (item[compareWith] === object[compareWith]) {
                            list.splice(i, 1);
                            break;
                        }
                    }

                    return this;
                };
            };

            this.include = function(obj) {
                ListFactory.prototype = obj;
            }
        }

        return new ListFactory();
    })
    .factory('Types', function() {
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
    })
    .factory('CompileCommander', function() {
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
                    case 'user-row':
                        return $("<user-row></user-row>")

                }
            }
        }

        return new CompileCommander();
    }).factory('Pagination', [function() {
        function Pagination(s, e) {
            this.start = s;
            this.end = e;

            this.constant = {
                start: s,
                end: e
            };

            this.currentPagination = function() {
                return {
                    start: this.start,
                    end: this.end
                }
            };

            this.nextPagination = function() {
                return {
                    start: this.start + 10,
                    end: this.end + 10
                }
            };

            this.reset = function() {
                this.start = this.constant.start;
                this.end = this.constant.end;
            }
        }

        return {
            init: function(start, end) {
                return new Pagination(start, end)
            }
        }
    }])
    .factory('Toggle', [function() {
        function Toggle() {
            var faze = false, config = {},
                valueIs = function (value, type) {
                    if (({}).toString.call(value).match(/\s([a-zA-Z]+)/)[1].toLowerCase() == type) {
                        return true;
                    }

                    return false;
                };

            this.create = function(name, c) {
                if( ! c.hasOwnProperty('enter')) {
                    throw new Error('Toggle: Toggle should have a \'enter\' property');
                }

                if( ! c.hasOwnProperty('exit')) {
                    throw new Error('Toggle: Toggle should have a \'exit\' property');
                }

                if( ! valueIs(c.enter, 'function') || ! valueIs(c.exit, 'function')) {
                    throw new Error('Toggle: Toggle.enter and Toggle.exit should be functions');
                }

                config[name] = c;
                config[name].faze = false;

                return this;
            };

            this.toggle = function(name, context) {
                if(config[name].faze === false) {
                    if(typeof context !== 'undefined') {
                        config[name].enter.call(context);
                    }
                    else {
                        config[name].enter();
                    }

                    config[name].faze = true;
                }
                else if(config[name].faze === true) {
                    if(typeof context !== 'undefined') {
                        config[name].exit.call(context);
                    }
                    else {
                        config[name].exit();
                    }

                    config[name].faze = false;
                }
            };

            this.isEntered = function() {
                return faze;
            };

            this.isExited = function() {
                return faze;
            };
        }

        return new Toggle();
    }]);

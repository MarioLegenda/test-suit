"use strict";

var suitApp = angular.module("suite.app", ['suite.directives', 'suite.factories']).config(['$interpolateProvider', function($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
}]);

suitApp.controller('suit.workspaceBuilder', ['$scope', '$timeout', function($scope, $timeout) {
    $scope.builder = {
        recompile: true
    };

    $scope.$on('action-recompile', function(event, data) {
        $scope.builderrecompile = false;

        $timeout(function() {
            $scope.builder.recompile = true;
        }, 500);
    });
}]);


angular.module('suite.directives', [])
    .directive('workspaceBuilder', function($timeout) {
        return {
            restrict: 'A',
            controller: function($scope) {
            },
            link: function($scope, elem, attrs) {
                $scope.currentTestId = attrs.currentTestId;
                $scope.minId = attrs.minId;
                $scope.maxId = attrs.maxId;

                $scope.$on('action-recompile', function(event, data) {
                    $scope.builder.recompile = false;

                    $timeout(function() {
                        $scope.builder.recompile = true;
                    }, 500);
                });

                $scope.$on('action-next-recompile', function(event, data) {

                });

                $scope.$on('action-previous-recompile', function(event, data) {

                });

                $scope.builder = {
                    recompile: true
                };
            }
        }
    })
    .directive('plainTextSuitBlock', function(DataShepard, Types, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            blockId: '@blockId',
            dataType: '@blockDataType',
            blockType: '@blockType',
            directiveType: '@blockDirectiveType',
            dataShepard: '=shepard'
        },
        templateUrl: 'plainTextSuite.html',
        controller: function($scope) {
            console.log('ulazak');
            if($scope.dataShepard.isPreCompiled() === true) {
                var block = $scope.dataShepard.get($scope.blockId);
                console.log(block);
                $scope.block = block;
            }
            else {
                $scope.block = Types.createType(
                    $scope.blockId,
                    $scope.dataType,
                    $scope.blockType,
                    $scope.directiveType
                );

                $scope.dataShepard.add($scope.block.blockId, $scope.block);
            }
        },
        link: function(scope, elem, attrs) {
            scope.blockEvents = {
                remove: function() {
                    scope.dataShepard.remove(scope.block.blockId);
                    scope.$destroy();
                    elem.remove();
                }
            };

            scope.$on("destroy-directive", function(event, data) {
                $timeout(function() {
                    scope.dataShepard.remove(scope.block.blockId);
                    scope.$destroy();
                    elem.remove();
                }, 500);
            });
        }
    }
}).directive('codeBlockSuite', function(DataShepard, Types, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            blockId: '@blockId',
            dataType: '@blockDataType',
            blockType: '@blockType',
            directiveType: '@blockDirectiveType',
            dataShepard: '=shepard'
        },
        templateUrl: 'codeBlockSuite.html',
        controller: function($scope) {
            if($scope.dataShepard.isPreCompiled() === true) {
                var block = $scope.dataShepard.get($scope.blockId);
                $scope.block = block;
            }
            else {
                $scope.block = Types.createType(
                    $scope.blockId,
                    $scope.dataType,
                    $scope.blockType,
                    $scope.directiveType
                );

                $scope.dataShepard.add($scope.block.blockId, $scope.block);
            }
        },
        link: function(scope, elem, attrs) {

            var editor = ace.edit(elem.find('.AceEditor').get(0));
            var session = editor.getSession();

            editor.getSession().setMode("ace/mode/javascript");
            editor.renderer.setShowGutter(true);
            editor.setFontSize(18);
            editor.setHighlightActiveLine(true);
            editor.setWrapBehavioursEnabled(true);
            editor.setOption('firstLineNumber', 1);
            editor.setValue(scope.block.data.data);

            scope.code = editor.getValue();
            scope.$watch(function() {
                scope.code = editor.getValue();
                return scope.code;
            }, function(newVal, oldVal, scope) {
                scope.block.data.data = newVal;
                scope.dataShepard.add(scope.block.blockId, scope.block);
            });

            scope.blockEvents = {
                remove: function() {
                    scope.dataShepard.remove(scope.block.blockId);
                    scope.$destroy();
                    elem.remove();
                }
            };

            scope.$on("destroy-directive",function(event, data) {
                $timeout(function() {
                    scope.dataShepard.remove(scope.block.blockId);
                    scope.$destroy();
                    elem.remove();
                }, 500);
            });
        }
    }
}).directive('genericBlockSuit', function(DataShepard, Types, $timeout) {
    return {
        restrict: 'E',
        scope: {
            blockId: '@blockId',
            dataType: '@blockDataType',
            blockType: '@blockType',
            directiveType: '@blockDirectiveType',
            dataShepard: '=shepard'
        },
        templateUrl: 'selectBlockSuit.html',
        controller: function($scope) {
            if($scope.dataShepard.isPreCompiled() === true) {
                var block = $scope.dataShepard.get($scope.blockId);
                $scope.block = block;
            }
            else {
                $scope.block = Types.createType(
                    $scope.blockId,
                    $scope.dataType,
                    $scope.blockType,
                    $scope.directiveType
                );

                $scope.dataShepard.add($scope.block.blockId, $scope.block);
            }

            console.log($scope.block);
        },
        link: function(scope, elem, attrs) {

            scope.blockEvents = {
                remove: function() {
                    scope.dataShepard.remove(scope.block.blockId);
                    scope.$destroy();
                    elem.remove();
                },
                addSelectBox: function() {
                    scope.block.data.data[scope.block.data.data.length] = '';
                }
            };

            scope.$on("destroy-directive",function(event, data) {
                $timeout(function() {
                    scope.dataShepard.remove(scope.block.blockId);
                    scope.$destroy();
                    elem.remove();
                }, 500);
            });
        }
    }
}).directive('processUiInstalling', function($window, $timeout) {
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
        scope: {
            initKey: '@initKey'
        },
        templateUrl: 'actionSubmit.html',
        controller: function($scope, $window, DataMediator) {
            var initData = DataMediator.getMediateData($scope.initKey);

            if(initData === null) {
                throw new Error("DataMedisator: initData is not properly initialized");
            }

            $scope.action = {
                awaiting: false,
                finished: false,
                redirect: false,
                url: null,
                submitProp: initData.submitProp,
                userSpecificClasses: initData.userSpecificClasses,
                awaitClasses: initData.awaitClasses,
                message: initData.message,
                textValue: initData.textValue,
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

            $scope.$on(initData.awaitEvent, function(event, values) {
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
}).directive('userListing', function(User) {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'userListing.html',
        controller: function($scope) {
            $scope.users = [];
            $scope.userInfo = {};
            $scope.type = 'user';

            $scope.loading = {
                loading: false,
                loadingText: 'Loading user...',
                loaded: false
            };

            $scope.factoryType = 'user';

            var promise = User.getAllUsers();
            promise.then(function(data, status, headers, config) {
                $scope.users = data.data.users;
            }, function(data, status, headers, config) {
                console.log('Something bad happend', data, status);
            });
        }
    }
}).directive('expandSection', function($, User, Test, $timeout) {
    return {
        restrict: 'A',
        scope: {
            factoryType: '@type',
            savingObject: '=savingObject',
            expandingId: '@id'
        },
        link : function(scope, elem, attrs) {

            var animation = ( function () {
                var show = false;
                return {
                    toggle: function(elem, clickedElem) {
                        var h = elem.find('.Expandable--info').outerHeight() + 50;
                        if(show === false) {
                            elem.animate({
                                height: h + 'px'
                            });

                            elem.css({
                                border: '1px solid #2C84EE'
                            });

                            clickedElem.css({
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

                            clickedElem.css({
                                backgroundColor: 'transparent'
                            });

                            show = false;
                        }
                    }
                };

            } () );

            elem.find('.Expandable--click').click(function(event) {
                event.preventDefault();

                var clickedElem = $(this);

                if( ! scope.savingObject.hasOwnProperty(scope.expandingId)) {

                    var promise = null;
                    if(scope.factoryType === 'user') {
                        promise = User.getUsersById({
                            id: scope.expandingId
                        }, true);
                    }
                    if(scope.factoryType === 'test') {
                        promise = Test.getBasicTests();
                    }

                    if(promise !== null) {
                        promise.then(function(data, status, headers, config) {
                            var dataReturned = data.data.user;
                            scope.savingObject[dataReturned.user_id] = dataReturned;

                            $timeout(function() {
                                animation.toggle(elem, clickedElem);
                            }, 100);

                        }, function(data, status, headers, config) {
                            animation.toggle(elem, clickedElem);
                        });
                    }
                    else {
                        animation.toggle(elem, clickedElem);
                    }
                }
                else {
                    $timeout(function() {
                        animation.toggle(elem, clickedElem);
                    }, 100);
                }

                return false;
            });
        }
    }
}).directive('testCreation', function(Test, DataMediator, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        scope: {},
        templateUrl: 'createTest.html',
        controller: function($scope, formHandler, $timeout, List, User) {
            $scope.theForm = formHandler.init($scope, 'CreateTestForm');


            $scope.globalErrors = {
                errors: [],
                show: false
            };

            DataMediator.addMediateData('action-create-test', {
                message: 'Please wait...',
                submitProp: 'test',
                userSpecificClasses: 'ActionMoveRight',
                awaitClasses: 'AwaitRight',
                textValue: 'Create/modify test',
                awaitEvent: 'await-create-user'
            });

            $scope.available_users = null;

            $scope.test = {
                test_control_id: null,
                test_name: '',
                current_permission: 'public',
                permission_items: ['public', 'restricted'],
                allowed_users: ['public'],
                remarks: '',
                preloaded: false,
                test_mutation: 'create'
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

            $scope.$on('action-preload-test', function(event, data) {
                $timeout(function() {
                    var promise = Test.getBasicTestById(data.testId);

                    promise.then(function(data, status, headers, config) {
                        $scope.test.preloaded = true;
                        $scope.test.test_mutation = 'modify';
                        var test = data.data.test;

                        $scope.test.test_name = test.test_name;
                        $scope.test.test_control_id = test.test_control_id;
                        if( test.visibility[0] !== 'public') {
                            $scope.test.current_permission = 'restricted';
                        }

                        $scope.test.remarks = test.remarks;

                    }, function(data, status, headers, config) {
                        console.log('Something bad happend', data, status);
                    });
                }, 100);
            });
        },
        link: function(scope, elem, attrs) {
            scope.test.submit = function($event) {
                $event.preventDefault();
                if (scope.theForm.isValidForm()) {
                    scope.$broadcast('await-create-user', {
                        awaiting: true
                    });

                    var promise = Test.modifyTest({
                        test_control_id: scope.test.test_control_id,
                        test_name: scope.test.test_name,
                        test_solvers: ( function() {
                            if(scope.test.allowed_users.length === 1 && scope.test.allowed_users[0] === 'public') {
                                return scope.test.allowed_users;
                            }
                            else if(scope.test.allowed_users.length === 0) {
                                return ['public']
                            }

                            var idsArray = [];
                            for(var i = 0; i < scope.test.allowed_users.length; i++) {
                                idsArray[idsArray.length] = scope.test.allowed_users[i].username;
                            }

                            return idsArray;
                        } () ),
                        remarks: scope.test.remarks
                    }, scope.test.test_mutation);

                    promise.then(function (data, status, headers, config) {
                        if(scope.test.preloaded === true) {
                            scope.$broadcast('action-finished', {
                                awaiting: false
                            });
                        }
                        else {
                            scope.$broadcast('action-finished', {
                                awaiting: true,
                                finished: true,
                                redirect: true,
                                url: '/app_dev.php' + data.data.redirectUrl
                            });
                        }
                    }, function (data, status, headers, config) {
                        scope.$broadcast('await-create-user', {
                            awaiting: false,
                            finished: false
                        });

                        scope.$broadcast('action-error', {
                            show: true,
                            errors: data.data['errors']
                        });

                        window.scrollTo(0, 0);
                    });
                }
            }
        }
    }
}).directive('createUser', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: true,
        templateUrl: 'createUserTemplate.html',
        controller: function($scope, formHandler, User, $timeout, DataMediator) {
            DataMediator.addMediateData('action-create-user', {
                message: 'Please wait...',
                submitProp: 'user',
                userSpecificClasses: 'ActionMoveRight',
                awaitClasses: 'AwaitRight',
                textValue: 'Create user',
                awaitEvent: 'await-create-test'
            });

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
                        $scope.$broadcast('await-create-test', {
                            awaiting: true
                        });

                        var promise = User.save($scope.user);

                        promise.then(function (data, status, headers, config) {
                            $scope.$emit('action-user-created', {});
                        }, function (data, status, headers, config) {
                            $scope.$broadcast('await-create-test', {
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
}).directive('builderModifier', function($, $compile, DataShepard, DataMediator, Test, CompileCommander, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            currentTestId: '@currentTestId',
            minId: '@minId',
            maxId: '@maxId'
        },
        templateUrl: 'workspace.html',
        controller: function($scope) {
            $scope.minId = parseInt($scope.minId);
            $scope.maxId = parseInt($scope.maxId);
            $scope.dataShepard = DataShepard;

            DataMediator.addMediateData('action-next-test', {
                message: 'Please wait...',
                submitProp: 'next',
                userSpecificClasses: 'ActionMoveRight',
                awaitClasses: 'AwaitRight',
                textValue: 'Next/save question',
                awaitEvent: 'await-next'
            });

            DataMediator.addMediateData('action-previous-test', {
                message: 'Please wait...',
                submitProp: 'previous',
                userSpecificClasses: 'ActionMoveLeft',
                awaitClasses: 'AwaitLeft',
                textValue: 'Previous question',
                awaitEvent: 'await-previous'
            });
        },
        link: function($scope, elem, attrs) {
            var promise = Test.getTest($scope.minId);

            $scope.directiveData = {
                blockId: null,
                dataType: null,
                type: null,
                directiveType: null
            };

            promise.then(function(data, status, headers, config) {
                var test = data.data.test,
                    elements = [];
                $scope.dataShepard.setPrecompiled();
                for(var i = 0; i < test.length; i++) {
                    var t = test[i];
                    $scope.dataShepard.add(i, t);
                }

                var trueTest = $scope.dataShepard.all();
                for(i = 0; i < trueTest.length; i++) {
                    var newScope = $scope.$new();
                    t = trueTest[i];
                    newScope.directiveData = {
                        blockId: t.blockId,
                        dataType: t.dataType,
                        type: t.type,
                        directiveType: t.directiveType
                    };

                    var jqEl = null;
                    switch(newScope.directiveData.directiveType) {
                        case 'plain-text-block':
                            jqEl = $("<plain-text-suit-block block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></plain-text-suit-block>");
                            break;
                        case 'code-block':
                            jqEl = $("<code-block-suite block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></code-block-suite>");
                            break;
                        case 'select-block':
                        case 'checkbox-block':
                        case 'radio-block':
                            jqEl = $("<generic-block-suit block-id='{[{ directiveData.blockId }]}' block-data-type='{[{ directiveData.dataType }]}' block-type='{[{ directiveData.type }]}' block-directive-type='{[{ directiveData.directiveType }]}' shepard='dataShepard'></generic-block-suit>");
                            break;

                    }

                    elem.find('.SuitBlocks').append(jqEl);
                    $compile(jqEl)(newScope);
                }

            }, function(data, status, headers, config) {

            });
        }
    }
}).directive('builder', function($, $compile, DataShepard, DataMediator, Test, CompileCommander) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            currentTestId: '@currentTestId',
            minId: '@minId',
            maxId: '@maxId'
        },
        templateUrl: 'workspace.html',
        controller: function($scope) {
            $scope.minId = parseInt($scope.minId);
            $scope.maxId = parseInt($scope.maxId);
            $scope.dataShepard = DataShepard;

            $scope.directiveData = {
                blockId: null,
                dataType: null,
                type: null,
                directiveType: null
            };

            DataMediator.addMediateData('action-next-test', {
                message: 'Please wait...',
                submitProp: 'next',
                userSpecificClasses: 'ActionMoveRight',
                awaitClasses: 'AwaitRight',
                textValue: 'Next question',
                awaitEvent: 'await-next'
            });

            DataMediator.addMediateData('action-finish-test', {
                message: 'Please wait...',
                submitProp: 'finished',
                userSpecificClasses: 'ActionMoveLeft',
                awaitClasses: 'AwaitLeft',
                textValue: 'Finish test',
                awaitEvent: 'await-finish'
            });

            $scope.builder = {
                build: function(dataType, type, directiveType) {
                    $scope.directiveData = {
                        blockId: $scope.dataShepard.current(),
                        dataType: dataType,
                        type: type,
                        directiveType: directiveType
                    };

                    var el = CompileCommander.compile($scope);
                    $('.SuitBlocks').append(el);
                    $scope.dataShepard.next();
                }
            };
        },
        link: function(scope, $elem, $attrs) {

            var createFirstElement = function() {
                scope.directiveData = {
                    blockId: scope.dataShepard.current(),
                    dataType: 'question',
                    type: 'text',
                    directiveType: 'plain-text-block'
                };

                var el = CompileCommander.compile(scope);
                $('.SuitBlocks').append(el);
                scope.dataShepard.next();
            };

            createFirstElement();

            scope.next = {
                submit: function($event) {
                    $event.preventDefault();

                    if(scope.dataShepard.current() > 1) {
                        scope.$broadcast('await-next', {
                            awaiting: true
                        });

                        var promise = Test.saveTest(scope.currentTestId, scope.dataShepard.all());

                        promise.then(function(data, status, headers, config) {
                            scope.$broadcast('await-next', {
                                awaiting: false
                            });

                            scope.$broadcast('destroy-directive', {});
                            scope.dataShepard.clear();

                            scope.$emit('action-recompile', {});

                        }, function(data, status, headers, config) {
                            console.log(data, status);

                            scope.$broadcast('await-next', {
                                awaiting: false
                            });
                        });


                    }
                }
            };

            scope.previous = {
                submit: function($event) {
                    $event.preventDefault();
                    scope.$broadcast('await-previous', {
                        awaiting: true
                    });
                }
            };

            scope.finished = {
                submit: function($event) {
                    $event.preventDefault();

                    scope.$broadcast('await-finish', {
                        awaiting: true
                    });
                }
            };
        }
    }
}).directive('managmentMenu', function($, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'managmentMenuTemplate.html',
        controller: function($scope) {
            $scope.menus = {
                userMenu: false,
                testMenu: false,

                toggle: function(type) {
                    if( ! this.hasOwnProperty(type)) {
                        throw Error('ManagmentMenu: Wrong type ' + type);
                    }

                    this[type] = !this[type];
                    var menus = ['userMenu', 'testMenu'];
                    menus.splice(menus.indexOf(type), 1);

                    for(var i = 0; i < menus.length; i++) {
                        this[menus[i]] = false;
                    }
                }
            };

            $scope.managment = {
                createUser: false,
                listUsers: false,
                createTest: false,
                testList: false,

                toggle: function(type) {
                    if( ! this.hasOwnProperty(type)) {
                        throw Error('ManagmentMenu: Wrong type ' + type);
                    }

                    this[type] = !this[type];
                    var menus = ['createUser', 'listUsers', 'createTest', 'testList'];
                    menus.splice(menus.indexOf(type), 1);

                    for(var i = 0; i < menus.length; i++) {
                        this[menus[i]] = false;
                    }
                }
            };

            $scope.$on('action-test-metadata-change', function(event, data) {
                $scope.managment.toggle('createTest');
                $timeout(function() {
                    $scope.$broadcast('action-preload-test', data);
                }, 1000);
            });

            $scope.$on('action-user-created', function(event, data) {
                $scope.managment.createUser = false;
                $scope.managment.listUsers = true;
            });
        },
        link: function(scope, elem, attrs) {

        }
    }
}).directive('testList', function(Test) {
    return {
        restrict: 'E',
        scope: true,
        replace: true,
        templateUrl: 'testList.html',
        controller: function($scope) {
            $scope.test = {
                modify: false,
                testInfo: {},
                tests: [],

                deleteTest: function($event, id) {
                    $event.preventDefault();

                    var promise = Test.deleteTest(id);

                    promise.then(function(data, status, headers, config) {
                        $scope.test.tests = data.data.tests;
                    }, function(data, status, headers, config) {
                        console.log('Something bad happend', data, status);
                    });
                },
                continueTest: function(url) {
                    window.location.replace('/app_dev.php' + url);
                },
                changeMetadata: function(testId) {
                    $scope.$emit('action-test-metadata-change', {'testId': testId});
                },
                modifyTest: function(url) {
                    window.location.replace('/app_dev.php' + url);
                }
            };

            $scope.factoryType = 'test';

            var promise = Test.getBasicTests();
            promise.then(function(data, status, headers, config) {
                $scope.test.tests = data.data.tests;
            }, function(data, status, headers, config) {
                console.log('Something bad happend', data, status);
            });
        },
        link: function(scope, elem, attrs) {
        }
    }
});
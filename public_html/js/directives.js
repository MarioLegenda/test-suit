"use strict";

angular.module("suite.app", [
    'suit.directives.actions',
    'suit.directives.blocks',
    'suit.directives.components',
    'suit.factories'])
    .config([
        '$interpolateProvider',
        '$provide',
        function ($interpolateProvider, $provide) {
            $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
        }]);


angular.module('suit.directives.actions', [])
   .directive('userListing', function(User) {
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
                awaitEvent: 'await-create-test'
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
                    scope.$broadcast('await-create-test', {
                        awaiting: true,
                        message: 'Please wait...'
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
                                idsArray[idsArray.length] = scope.test.allowed_users[i].user_id;
                            }

                            return idsArray;
                        } () ),
                        remarks: scope.test.remarks
                    }, scope.test.test_mutation);

                    promise.then(function (data, status, headers, config) {
                        if(data.status === 205) {
                            scope.$broadcast('await-create-user', {
                                awaiting: false,
                                finished: false
                            });

                            scope.$broadcast('action-error', {
                                show: true,
                                errors: data.data['errors']
                            });

                            window.scrollTo(0, 0);
                            return;
                        }

                        if(scope.test.preloaded === true) {
                            scope.$broadcast('await-create-test', {
                                awaiting: true,
                                message: 'Modified'
                            });

                            $timeout(function() {
                                scope.$broadcast('await-create-test', {
                                    awaiting: false
                                });
                            }, 2000)
                        }
                        else {
                            scope.$emit('action-create-test', {});
                        }
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
                awaitEvent: 'await-create-user'
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
                        $scope.$broadcast('await-create-user', {
                            awaiting: true
                        });

                        var promise = User.save($scope.user);

                        promise.then(function (data, status, headers, config) {
                            $scope.$emit('action-user-created', {});
                        }, function (data, status, headers, config) {
                            $scope.$broadcast('await-create-user', {
                                awaiting: false,
                                finished: false,
                                redirect: false
                            });

                            $scope.globalErrors.show = true;

                            $scope.globalErrors.errors = data.data.errors.errors;
                            console.log($scope.globalErrors);
                            window.scrollTo(0, 0);
                        });
                    }
                }
            };
        },
        link: function(scope, elem, attrs) {

        }
    }
}).directive('workspace', function() {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'workspace.html',
            scope: {
                testControlId: '@testControlId'
            },
            controller: function ($scope) {
            },
            link: function ($scope, elem, attrs) {

            }
        }
}).directive('workspaceBuilder', function($timeout, Test, DataShepard, RangeIterator) {
        return {
            restrict: 'A',
            replace: true,
            controller: function($scope) {
            },
            link: function($scope, elem, attrs) {
                $scope.currentTestId = parseInt(attrs.currentTestId);
                $scope.dataShepard = DataShepard;

                $scope.controller = {
                    minId: null,
                    maxId: null,
                    testId: null,
                    rangeIterator: null
                };

                var workspacePromise = Test.workspaceData($scope.currentTestId);

                workspacePromise.then(function(data, status, headers, config) {
                    $scope.controller = {
                        minId: parseInt(data.data.test.min),
                        maxId: parseInt(data.data.test.max),
                        testId: parseInt(data.data.test.min)
                    };
                }).then(function() {
                    var testPromise = Test.getTest($scope.controller.testId, $scope.currentTestId);

                    testPromise.then(function(data, status, headers, config) {
                        if(data.status === 205) {
                            $scope.controller.rangeIterator = RangeIterator.initIterator([]);
                            $scope.builder = {
                                builderCreator: true,
                                builderModifier: false
                            };

                            return;
                        }

                        var test = data.data.test;
                        $scope.dataShepard.syncCurrentId();
                        $scope.controller.rangeIterator = RangeIterator.initIterator(data.data.range);

                        $scope.builder = {
                            builderCreator: true,
                            builderModifier: false
                        };

                    });
                });

                $scope.$on('action-builder-change', function (event, eventData) {
                    var menus = ['builderCreator', 'builderModifier'];

                    for (var i = 0; i < menus.length; i++) {
                        $scope.builder[menus[i]] = false;
                    }

                    var workspacePromise = Test.workspaceData($scope.currentTestId);

                    workspacePromise.then(function(data, status, headers, config) {
                        $scope.controller = {
                            minId: parseInt(data.data.test.min),
                            maxId: parseInt(data.data.test.max),
                            testId: parseInt(data.data.test.min)
                        };

                        var testPromise = Test.getTest($scope.controller.testId, $scope.currentTestId);

                        testPromise.then(function(data, status, headers, config) {
                            if(data.status === 205) {
                                $scope.controller.rangeIterator = RangeIterator.initIterator([]);
                                $scope.builder = {
                                    builderCreator: true,
                                    builderModifier: false
                                };

                                return;
                            }

                            var test = data.data.test;
                            $scope.dataShepard.syncCurrentId();
                            $scope.dataShepard.clearHeard();
                            $scope.controller.rangeIterator = RangeIterator.initIterator(data.data.range);

                            var change = eventData.change;

                            $timeout(function () {
                                $scope.builder[change] = !$scope.builder[change];
                            }, 500);

                        }, function(data, status, headers, config) {
                            $scope.controller.rangeIterator = RangeIterator.initIterator([]);
                            $scope.builder = {
                                builderCreator: true,
                                builderModifier: false
                            };
                        });
                    }, function(data, status, headers, config) {
                        console.log(data, status);
                    });
                });

                $scope.$on('action-next-recompile', function(event, eventData) {
                    var change = eventData.change;

                    var menus = ['builderCreator', 'builderModifier'];

                    for (var i = 0; i < menus.length; i++) {
                        $scope.builder[menus[i]] = false;
                    }

                    $scope.controller.testId = $scope.controller.rangeIterator.next();

                    $timeout(function () {
                        $scope.builder[change] = !$scope.builder[change];
                    }, 200);
                });

                $scope.$on('action-previous-recompile', function(event, eventData) {
                    var change = eventData.change;

                    var menus = ['builderCreator', 'builderModifier'];

                    for (var i = 0; i < menus.length; i++) {
                        $scope.builder[menus[i]] = false;
                    }

                    $scope.controller.testId = $scope.controller.rangeIterator.previous();

                    $timeout(function () {
                        $scope.builder[change] = !$scope.builder[change];
                    }, 200);
                });

                $scope.$on('action-builder-delete', function(event, eventData) {
                    var change = eventData.change,
                        id = eventData.id;

                    var deletePromise = Test.deleteQuestion(id);

                    deletePromise.then(function(data, status, headers, config) {
                        var workspacePromise = Test.workspaceData($scope.currentTestId);

                        workspacePromise.then(function(data, status, headers, config) {
                            $scope.controller = {
                                minId: parseInt(data.data.test.min),
                                maxId: parseInt(data.data.test.max),
                                testId: parseInt(data.data.test.min)
                            };

                            var testPromise = Test.getTest($scope.controller.testId, $scope.currentTestId);

                            testPromise.then(function(data, status, headers, config) {
                                if(data.status === 205) {
                                    $scope.controller.rangeIterator = RangeIterator.initIterator([]);
                                    $scope.builder.builderModifier = false;

                                    $timeout(function() {
                                        $scope.builder.builderCreator = true;
                                    }, 500);

                                    return;
                                }

                                var test = data.data.test;
                                $scope.dataShepard.syncCurrentId();
                                $scope.dataShepard.clearHeard();
                                $scope.controller.rangeIterator = RangeIterator.initIterator(data.data.range);

                                var menus = ['builderCreator', 'builderModifier'];

                                for (var i = 0; i < menus.length; i++) {
                                    $scope.builder[menus[i]] = false;
                                }

                                $scope.controller.testId = $scope.controller.minId;

                                $timeout(function () {
                                    $scope.builder[change] = !$scope.builder[change];
                                }, 200);

                            }, function(data, status, headers, config) {
                                $scope.builder = {
                                    builderCreator: true,
                                    builderModifier: false
                                };
                            });
                        }, function(data, status, headers, config) {
                            console.log(data, status);
                        });
                    });
                });
            }
        }
    })
.directive('builderModifier', function($, $compile, DataMediator, Test, CompileCommander, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            currentTestId: '@currentTestId',
            dataShepard: '=shepard',
            rangeIterator: '=rangeIterator'
        },
        templateUrl: 'builderModifier.html',
        controller: function($scope) {
            window.scrollTo(0, 550);

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

            $scope.builder = {
                build: function(dataType, type, directiveType) {
                    var newScope = $scope.$new();
                    newScope.directiveData = {
                        blockId: $scope.dataShepard.current(),
                        dataType: dataType,
                        type: type,
                        directiveType: directiveType,
                        nonCompiled: true
                    };

                    var el = CompileCommander.compile(newScope);
                    $('.SuitBlocks').append(el);
                    $compile(el)(newScope);

                    $scope.dataShepard.next();
                },
                builderChange: function($event, builder) {
                    $scope.$emit('action-builder-change', {
                        change: builder
                    });
                },
                builderDelete: function($event, builder) {
                    $scope.$emit('action-builder-delete', {
                        id: $scope.currentTestId,
                        change: builder
                    })
                }
            };
        },
        link: function($scope, elem, attrs) {
            $('.ChoiceBlock').find('.Modify').css({
                'background-color': '#2C84EE'
            });

            var testPromise = Test.getTest($scope.currentTestId, null);

            testPromise.then(function(data, status, headers, config) {
                var test = data.data.test,
                    elements = [];
                $scope.dataShepard.setPrecompiled();
                $scope.dataShepard.clearHeard().clearCounter();
                for(var i = 0; i < test.length; i++) {
                    var t = test[i];
                    $scope.dataShepard.add(i, t);
                }
                $scope.dataShepard.syncCurrentId();

                //console.log($scope.dataShepard.current(), $scope.dataShepard.all());
                var trueTest = $scope.dataShepard.all();
                for(i = 0; i < trueTest.length; i++) {
                    var newScope = $scope.$new();
                    t = trueTest[i];
                    newScope.directiveData = {
                        testId: $scope.testId,
                        blockId: t.blockId,
                        dataType: t.dataType,
                        type: t.type,
                        directiveType: t.directiveType
                    };

                    var jqEl = CompileCommander.compile(newScope);

                    elem.find('.SuitBlocks').append(jqEl);
                    $compile(jqEl)(newScope);
                }

            });


            $scope.directiveData = {
                blockId: null,
                dataType: null,
                type: null,
                directiveType: null
            };

            $scope.next = {
                submit: function($event) {
                    $event.preventDefault();

                    $scope.$broadcast('await-next', {
                        awaiting: true
                    });

                    $scope.dataShepard.arangeBlockIds();
                    var promise = Test.updateTest($scope.currentTestId, $scope.dataShepard.all());

                    promise.then(function (data, status, headers, config) {
                        $scope.$broadcast('await-next', {
                            awaiting: false
                        });

                        $scope.$broadcast('destroy-directive', {});
                        $scope.dataShepard.clearHeard().clearCounter();

                        $scope.$emit('action-next-recompile', {
                            change: 'builderModifier'
                        });

                    }, function (data, status, headers, config) {

                        $scope.$broadcast('await-next', {
                            awaiting: false
                        });
                    });
                }
            };

            $scope.previous = {
                submit: function($event) {
                    $event.preventDefault();
                    $scope.$emit('action-previous-recompile', {
                        change: 'builderModifier'
                    });
                }
            };
        }
    }
}).directive('builderCreator', function($, $compile, DataMediator, Test, CompileCommander) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            currentTestId: '@currentTestId',
            dataShepard: '=shepard',
            rangeIterator: '=rangeIterator'
        },
        templateUrl: 'builderCreator.html',
        controller: function($scope) {
            window.scrollTo(0, 550);

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
                textValue: 'Next/save question',
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
                    var newScope = $scope.$new();
                    newScope.directiveData = {
                        blockId: $scope.dataShepard.current(),
                        dataType: dataType,
                        type: type,
                        directiveType: directiveType,
                        nonCompiled: true
                    };

                    var el = CompileCommander.compile(newScope);
                    $('.SuitBlocks').append(el);
                    $compile(el)(newScope);

                    $scope.dataShepard.next();
                },
                builderChange: function($event, builder) {
                    $scope.$emit('action-builder-change', {
                        change: builder
                    });
                }
            };
        },
        link: function(scope, $elem, $attrs) {

            $('.ChoiceBlock').find('.Create').css({
                'background-color': '#2C84EE'
            });

            var createFirstElement = function() {
                var newScope = scope.$new();
                newScope.directiveData = {
                    blockId: scope.dataShepard.current(),
                    dataType: 'question',
                    type: 'text',
                    directiveType: 'plain-text-block',
                    nonCompiled: true
                };

                var el = CompileCommander.compile(newScope);
                $('.SuitBlocks').append(el);
                $compile(el)(newScope);
                scope.dataShepard.next();
            };

            createFirstElement();

            scope.next = {
                submit: function($event) {
                    $event.preventDefault();

                    scope.$broadcast('await-next', {
                        awaiting: true
                    });

                    scope.dataShepard.arangeBlockIds();
                    var promise = Test.saveTest(scope.currentTestId, scope.dataShepard.all());

                    promise.then(function (data, status, headers, config) {
                        scope.$broadcast('await-next', {
                            awaiting: false
                        });

                        scope.$broadcast('destroy-directive', {});
                        scope.dataShepard.clearHeard().clearCounter();

                        scope.$emit('action-builder-change', {
                            change: 'builderCreator'
                        });

                    }, function (data, status, headers, config) {
                        console.log(data, status);

                        scope.$broadcast('await-next', {
                            awaiting: false
                        });
                    });
                }
            };

            scope.finished = {
                submit: function($event) {
                    $event.preventDefault();

                    scope.$broadcast('await-finish', {
                        awaiting: true
                    });

                    var finishPromise = Test.finishTest(scope.currentTestId);
                    finishPromise.then(function(data, status, headers, config) {
                        scope.$emit('action-test-finished', {});
                    }, function(data, status, headers, config) {
                        console.log(data, status);
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
            $scope.importance = {
                testControlId: null
            };

           $scope.menus = {
                userMenu: false,
                testMenu: false,

                toggle: function($event, type) {
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
                workspace: false,

                toggle: function($event, type) {
                    if( ! this.hasOwnProperty(type)) {
                        throw Error('ManagmentMenu: Wrong type ' + type);
                    }

                    this[type] = !this[type];
                    var menus = ['createUser', 'listUsers', 'createTest', 'testList', 'workspace'];
                    menus.splice(menus.indexOf(type), 1);

                    for(var i = 0; i < menus.length; i++) {
                        this[menus[i]] = false;
                    }
                }
            };

            $scope.$on('action-test-metadata-change', function(event, data) {
                $scope.managment.toggle({}, 'createTest');
                $timeout(function() {
                    $scope.$broadcast('action-preload-test', data);
                }, 1000);
            });

            $scope.$on('action-user-created', function(event, data) {
                $scope.managment.createUser = false;
                $scope.managment.listUsers = true;
            });

            $scope.$on('action-workspace', function(event, data) {
                $scope.importance.testControlId = data.testId;
                $scope.managment.testList = false;
                $scope.managment.workspace = true;
            });

            $scope.$on('action-test-finished', function($event, data) {
                $scope.managment.workspace = false;
                $scope.managment.testList = true;
            });

            $scope.$on('action-create-test', function($event, data) {
                $scope.managment.createTest = false;
                $scope.managment.testList = true;
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
                changeMetadata: function(testId) {
                    $scope.$emit('action-test-metadata-change', {'testId': testId});
                },
                workspace: function(testId) {
                    $scope.$emit('action-workspace', {testId: testId});
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
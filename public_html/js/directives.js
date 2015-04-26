"use strict";

angular.module("suite.app", [
    'suit.directives.actions',
    'suit.directives.blocks',
    'suit.directives.components',
    'suit.factories'])
    .config([
        '$interpolateProvider',
        '$filterProvider',
        function ($interpolateProvider, $filterProvider) {
            $interpolateProvider.startSymbol('{[{').endSymbol('}]}');

            $filterProvider.register('dateParse', function(dateString){
                return function() {
                    var d, date;

                    d = new Date(dateString);
                    date = d.getDate() + '.' + (d.getMonth() + 1) + '.' + d.getFullYear();

                    return date;
                };
            });
        }]);


angular.module('suit.directives.actions', [])
.directive('help', ['$timeout', function($timeout) {
        return {
            restirct: 'E',
            replace: true,
            templateUrl: 'help.html',
            controller: function($scope) {
                $scope.help = {
                    introduction: true,
                    creating_users: false,
                    user_permissions: false,
                    informational_fields: false,
                    creating_tests: false,
                    test_permissions: false,
                    question_suit: false,
                    question_blocks: false,

                    toggle: function($event, type) {
                        if( ! this.hasOwnProperty(type)) {
                            throw Error('ManagmentMenu: Wrong type ' + type);
                        }

                        this[type] = !this[type];
                        var menus = [
                            'introduction',
                            'creating_users',
                            'user_permissions',
                            'informational_fields',
                            'creating_tests',
                            'test_permissions',
                            'question_suit',
                            'question_blocks'
                        ];
                        menus.splice(menus.indexOf(type), 1);

                        for(var i = 0; i < menus.length; i++) {
                            this[menus[i]] = false;
                        }

                        $timeout(function() {
                            window.scrollTo(0, 830);
                        }, 200);
                    }
                }
            },
            link: function($scope, elem, attrs) {

            }
        }
}]).directive('testCreation', ['Test', 'DataMediator', 'List', function(Test, DataMediator, List) {
    return {
        restrict: 'E',
        replace: true,
        scope: {},
        templateUrl: 'createTest.html',
        controller: function($scope, formHandler) {

            $scope.globalErrors = {
                errors: [],
                show: false
            };

            DataMediator.addMediateData('action-create-test', {
                message: 'Please wait...',
                submitProp: 'newTest',
                userSpecificClasses: 'ActionMoveRight',
                awaitClasses: 'AwaitRight',
                textValue: 'Create/modify test',
                awaitEvent: 'await-create-test'
            });

            $scope.newTest= {
                theForm: formHandler.init($scope, 'CreateTestForm'),
                assignedList: List.createSimple(),
                removedList: List.createSimple(),
                assigned_users: [],
                removed_users: [],
                test_control_id: null,
                test_name: '',
                remarks: '',
                highlighting: {
                    public: true,
                    restricted: false
                },
                preloaded: false,
                changePermission: function(permission, disable) {
                    this.highlighting[permission] = !this.highlighting[permission];
                    this.highlighting[disable] = false;

                    if(permission === 'public') {
                        $scope.newTest.assignedList.clear();
                    }
                },
                directiveQuery: {
                    isAssigned: function(userId) {
                        return $scope.newTest.assignedList.hasEntry(parseInt(userId));
                    }
                }
            };

            $scope.newTest.assignedList.clear();
            $scope.newTest.removedList.clear();

            $scope.$on('action-preload-test', function(event, data) {

                $scope.initialData = {
                    test_control_id: data.testId,
                    test_name: data.test_name,
                    remarks: data.remarks,
                    permition: data.permission
                };

                $scope.newTest.preloaded = true;
                $scope.newTest.test_control_id = data.testId;
                $scope.newTest.test_name = data.test_name;
                $scope.newTest.remarks = data.remarks;

                if(data.permission === 'public') {
                    $scope.newTest.highlighting.restricted = false;
                    $scope.newTest.highlighting.public = true;
                    return;
                }

                if(data.permission === 'restricted') {
                    var promise = Test.getAssignedTestUsersIds($scope.newTest.test_control_id);

                    promise.then(function(data, status, headers, config) {
                        $scope.newTest.highlighting.restricted = true;
                        $scope.newTest.highlighting.public = false;
                        $scope.newTest.assignedList.multipleAdd(data.data.ids);
                    }, function(data, status, headers, config) {
                        console.log('Something went wrong', data);
                    });
                }
            });

            $scope.$on('action-assign-user', function(event, data) {
                $scope.newTest.assignedList.add(data.user_id);
            });

            $scope.$on('action-remove-user', function(event, data) {
                $scope.newTest.assignedList.remove(data.user_id);
            });
        },
        link: function($scope, elem, attrs) {
            $scope.newTest.submit = function($event) {
                $event.preventDefault();
                var promise, scenario = {};
                if($scope.newTest.theForm.isValidForm()) {
                    if($scope.newTest.preloaded === false) {
                        scenario = {
                            scenario: {
                                condition: ( function() {
                                    if($scope.newTest.assignedList.isEmpty()) {
                                        return 'create-public-test';
                                    }

                                    if( ! $scope.newTest.assignedList.isEmpty()) {
                                        return 'create-restricted-test'
                                    }
                                } () ),
                                data: {
                                    test_name: $scope.newTest.test_name,
                                    remarks: $scope.newTest.remarks,
                                    test_solvers: ( function() {
                                        if( ! $scope.newTest.assignedList.isEmpty()) {
                                            return $scope.newTest.assignedList.all();
                                        }

                                        return null;
                                    } () )
                                }
                            }
                        };

                        promise = Test.createTest(scenario);

                        promise.then(function(data, status, headers, config) {
                            $scope.$emit('action-create-test', {});
                        }, function(data, status, headers, config) {
                            console.log('Something went wrong', data);
                        })
                    }
                    else if($scope.newTest.preloaded === true) {
                        var isPublic = $scope.newTest.assignedList.isEmpty();

                        scenario = {
                            scenario: {
                                condition: ( function() {
                                    if(isPublic && $scope.initialData.permition === 'restricted') {
                                        return 'restricted-to-public';
                                    }

                                    if(isPublic && $scope.initialData.permition === 'public') {
                                        return 'public-to-public';
                                    }

                                    if( ! isPublic && $scope.initialData.permition === 'public') {
                                        return 'public-to-restricted';
                                    }

                                    if( ! isPublic && $scope.initialData.permition === 'restricted') {
                                        return 'restricted-to-restricted';
                                    }
                                } () ),
                                data: {
                                    test_control_id: $scope.newTest.test_control_id,
                                    test_name: $scope.newTest.test_name,
                                    remarks: $scope.newTest.remarks,
                                    test_solvers: ( function() {
                                        if($scope.newTest.assignedList.isEmpty()) {
                                            return false;
                                        }

                                        return $scope.newTest.assignedList.all();
                                    } () )
                                }
                            }
                        };

                        promise = Test.modifyTest(scenario);

                        promise.then(function(data, status, headers, config) {
                            $scope.$emit('action-create-test', {});
                        }, function(data, status, headers, config) {
                            console.log('Something went wrong', data);
                        });
                    }
                }
            }
        }
    }
}]).directive('permissionHandler', ['User', function(User) {
    return {
        restrict: 'AE',
        replace: false,
        controller: function($scope) {
            $scope.directiveData = {
                users: []
            };

            $scope.$on('action-user-filter', function($event, data) {
                var promise = User.filter(data);

                promise.then(function (data, status, headers, config) {
                    $scope.directiveData.users = data.data.users;
                }, function (data, status, headers, config) {
                    console.log(data, data.status);
                });
            });
        },
        link: function($scope, elem, attrs) {

        }
    }
}]).directive('permissionHandlerPreloaded', [function() {
    return {
        restrict: 'EA',
        replace: false,
        scope: {},
        controller: function($scope) {

        },
        link: function($scope, elem, attrs) {

        }
    }
}]).directive('createUser', function() {
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

                        var promise = User.createUser($scope.user);

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
}).directive('workspaceBuilder', ['$timeout', 'Test', 'Workspace', 'DataShepard', 'RangeIterator', function($timeout, Test, Workspace, DataShepard, RangeIterator) {
        return {
            restrict: 'A',
            replace: true,
            controller: function($scope) {
            },
            link: function($scope, elem, attrs) {
                $scope.currentTestId = parseInt(attrs.testControlId);
                $scope.dataShepard = DataShepard;

                $scope.controller = {
                    minId: null,
                    maxId: null,
                    testId: null,
                    rangeIterator: null
                };

                var workspacePromise = Workspace.workspaceData($scope.currentTestId);

                workspacePromise.then(function(data, status, headers, config) {
                    $scope.controller = {
                        minId: parseInt(data.data.test.min),
                        maxId: parseInt(data.data.test.max),
                        testId: parseInt(data.data.test.min)
                    };
                }).then(function() {
                    var testPromise = Workspace.getTest($scope.controller.testId, $scope.currentTestId);

                    testPromise.then(function(data, status, headers, config) {
                        if(data.status === 205) {
                            $scope.controller.rangeIterator = RangeIterator.initIterator([]);
                            $scope.builder = {
                                builderCreator: true,
                                builderModifier: false
                            };

                            return;
                        }

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

                    var workspacePromise = Workspace.workspaceData($scope.currentTestId);

                    workspacePromise.then(function(data, status, headers, config) {
                        $scope.controller = {
                            minId: parseInt(data.data.test.min),
                            maxId: parseInt(data.data.test.max),
                            testId: parseInt(data.data.test.min)
                        };

                        var testPromise = Workspace.getTest($scope.controller.testId, $scope.currentTestId);

                        testPromise.then(function(data, status, headers, config) {
                            if(data.status === 205) {
                                $scope.dataShepard.clearHeard().clearCounter();
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

                    var deletePromise = Workspace.deleteQuestion(id);

                    deletePromise.then(function(data, status, headers, config) {
                        $scope.dataShepard.clearHeard().clearCounter();
                        var workspacePromise = Workspace.workspaceData($scope.currentTestId);

                        workspacePromise.then(function(data, status, headers, config) {
                            $scope.controller = {
                                minId: parseInt(data.data.test.min),
                                maxId: parseInt(data.data.test.max),
                                testId: parseInt(data.data.test.min)
                            };

                            var testPromise = Workspace.getTest($scope.controller.testId, $scope.currentTestId);

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
}]).directive('builderModifier', ['$', '$compile', 'DataMediator', 'Test', 'Workspace', 'CompileCommander', '$timeout', function($, $compile, DataMediator, Test, Workspace, CompileCommander, $timeout) {
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

            var testPromise = Workspace.getTest($scope.currentTestId, null);

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
                    var promise = Workspace.updateTest($scope.currentTestId, $scope.dataShepard.all());

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
}]).directive('builderCreator', ['$', '$compile', 'DataMediator', 'Test', 'Workspace', 'CompileCommander', function($, $compile, DataMediator, Test, Workspace, CompileCommander) {
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

                    var promise = Workspace.saveTest({
                        test_control_id: scope.currentTestId,
                        data: scope.dataShepard.all()
                    });

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

                    var finishPromise = Workspace.finishTest(scope.currentTestId);
                    finishPromise.then(function(data, status, headers, config) {
                        scope.$emit('action-test-finished', {});
                    }, function(data, status, headers, config) {
                        console.log(data, status);
                    });
                }
            };
        }
    }
}]).directive('managmentMenu', ['$', '$timeout', function($, $timeout) {
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
                assignedTests: false,
                workspace: false,
                helpMenu: false,

                toggle: function($event, type) {
                    if( ! this.hasOwnProperty(type)) {
                        throw Error('ManagmentMenu: Wrong type ' + type);
                    }

                    this[type] = !this[type];
                    var menus = [
                        'createUser',
                        'listUsers',
                        'createTest',
                        'testList',
                        'workspace',
                        'helpMenu',
                        'assignedTests'
                    ];

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
                }, 500);
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
}]).directive('testListing', ['Test', 'Pagination', function(Test, Pagination) {
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
}]).directive('assignedTestsListing', ['Pagination', function(Pagination) {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'assignedTestListing.html',
        controller: function($scope) {
            $scope.directiveData = {
                users: [],
                loaded: false,
                directiveType: 'assigned-test-row',
                loadMore: true
            };

            var pagination = Pagination.init(1, 10);
        },
        link: function($scope, elem, attrs) {

        }
    }
}]);
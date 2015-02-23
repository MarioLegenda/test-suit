"use strict";

String.prototype.cFirstUpper = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

angular.module('suite.directives', [])
    .directive('plainTextSuitBlock', function(BlockType, DataShepard, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            block: '@block'
        },
        templateUrl: 'plainTextSuite.html',
        controller: function($scope) {
            DataShepard.add($scope.block.block_id, $scope.block);
        },
        link: function(scope, elem, attrs) {
            scope.blockEvents = {
                remove: function() {
                    DataShepard.remove(scope.block.block_id);
                    scope.$destroy();
                    elem.remove();
                }
            };

            scope.$on("destroy-directive", function(event, data) {
                $timeout(function() {
                    DataShepard.remove(scope.block.block_id);
                    scope.$destroy();
                    elem.remove();
                }, 500);
            });
        }
    }
}).directive('codeBlockSuite', function(BlockType, DataShepard, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            block: '@block'
        },
        templateUrl: 'codeBlockSuite.html',
        controller: function($scope) {
        },
        link: function(scope, elem, attrs) {
            DataShepard.add(scope.block.block_id, scope.block);

            var editor = ace.edit(elem.find('.AceEditor').get(0));
            var session = editor.getSession();

            editor.getSession().setMode("ace/mode/javascript");
            editor.renderer.setShowGutter(true);
            editor.setFontSize(18);
            editor.setHighlightActiveLine(true);
            editor.setWrapBehavioursEnabled(true);
            editor.setOption('firstLineNumber', 1);
            editor.setValue(scope.block.data.data);

            scope.blockEvents = {
                remove: function() {
                    DataShepard.remove(scope.block.block_id);
                    scope.$destroy();
                    elem.remove();
                }
            };

            scope.$on("destroy-directive",function(event, data) {
                $timeout(function() {
                    DataShepard.remove(scope.block.block_id);
                    scope.$destroy();
                    elem.remove();
                }, 500);
            });
        }
    }
}).directive('genericBlockSuit', function(DataShepard, BlockType, $timeout) {
    return {
        restrict: 'E',
        scope: {
            block: '@block'
        },
        templateUrl: 'selectBlockSuit.html',
        controller: function($scope) {
        },
        link: function(scope, elem, attrs) {
            DataShepard.add(scope.block.block_id, scope.block);

            scope.innerBlock = {
                data: []
            };

            scope.block.data.data[scope.block.data.data.length] = '';

            scope.blockEvents = {
                remove: function() {
                    DataShepard.remove(scope.block.block_id);
                    scope.$destroy();
                    elem.remove();
                },
                addSelectBox: function() {
                    scope.block.data.data[scope.block.data.data.length] = '';
                }
            };

            scope.$on("destroy-directive",function(event, data) {
                $timeout(function() {
                    DataShepard.remove(scope.block.block_id);
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
}).directive('testNaming', function(TestControl, DataMediator) {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'createTest.html',
        controller: function($scope, formHandler, $timeout, List, User) {
            $scope.theForm = formHandler.init($scope, 'CreateTestForm');

            $scope.globalErrors = {
                errors: [],
                show: false
            };

            DataMediator.addMediateData('action-create-test', {
                message: 'Please wait...',
                userSpecificClasses: 'TempSubmitButton',
                submitProp: 'test',
                textValue: 'Create test'
            });

            $scope.available_users = null;

            $scope.test = {
                test_name: '',
                current_permission: 'public',
                permission_items: ['public', 'restricted'],
                allowed_users: ['public'],
                remarks: ''
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
        },
        link: function(scope, elem, attrs) {
            scope.test.submit = function($event) {
                $event.preventDefault();
                if (scope.theForm.isValidForm()) {
                    scope.$broadcast('action-await', {
                        awaiting: true
                    });

                    var promise = TestControl.createTest({
                        test_name: scope.test.test_name,
                        test_solvers: ( function() {
                            if(scope.test.allowed_users.length === 1 && scope.test.allowed_users[0] === 'public') {
                                return scope.test.allowed_users;
                            }

                            var idsArray = [];
                            for(var i = 0; i < scope.test.allowed_users.length; i++) {
                                var idObject = {};
                                idObject.user_id = scope.test.allowed_users[i].user_id;

                                idsArray[idsArray.length] = idObject;
                            }

                            return idsArray;
                        } () ),
                        remarks: scope.test.remarks
                    });

                    promise.then(function (data, status, headers, config) {
                        scope.$broadcast('action-finished', {
                            awaiting: true,
                            finished: true,
                            redirect: true,
                            url: '/app_dev.php' + data.data.redirectUrl
                        });
                    }, function (data, status, headers, config) {
                        scope.$broadcast('action-await', {
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
        controller: function($scope, formHandler, User, $timeout) {
            $timeout(function() {
                $scope.$broadcast('action-initialization', {
                    message: 'Please wait...',
                    submitProp: 'user'
                });
            }, 200);

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
                        $scope.$broadcast('action-await', {
                            awaiting: true
                        });

                        var promise = User.save($scope.user);

                        promise.then(function (data, status, headers, config) {
                            $scope.$broadcast('action-finished', {
                                awaiting: true,
                                finished: true,
                                redirect: true,
                                url: '/app_dev.php/user-managment'
                            });
                        }, function (data, status, headers, config) {
                            $scope.$broadcast('action-await', {
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
}).directive('builder', function($, $compile, DataShepard, BlockType, DataMediator, Test, Types, $interval) {
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

            $scope.block = null;

            DataMediator.addMediateData('action-next-test', {
                message: 'Please wait...',
                submitProp: 'next',
                userSpecificClasses: 'ActionMoveRight',
                awaitClasses: 'AwaitRight',
                textValue: 'Next question',
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

            DataMediator.addMediateData('action-finish-test', {
                message: 'Please wait...',
                submitProp: 'finished',
                userSpecificClasses: 'BlockFinishButton',
                awaitClasses: 'AwaitRight',
                textValue: 'Finish test',
                awaitEvent: 'await-finish'
            });

            $scope.builder = {
                buildQuestion: function(directiveType) {
                    var el, type;
                    switch(directiveType) {
                        case "plain-text-block":
                            type = Types.createType($scope.dataShepard.current(), 'question', 'text', directiveType);
                            $scope.block = type;
                            el = $compile("<plain-text-suit-block block='{[{ block }]}'></plain-text-suit-block>")($scope.$new(true));
                            break;
                        case 'code-block':
                            type = Types.createType($scope.dataShepard.current(), 'question', 'text', directiveType);
                            $scope.block = type;
                            el = $compile("<code-block-suite block='{[{ block }]}'></code-block-suite>")($scope.$new(true));
                            break;
                    }

                    $('.SuitBlocks').append(el);
                    $scope.dataShepard.next();
                },
                buildAnswer: function(directiveType) {
                    var el, type;
                    switch(directiveType) {
                        case "plain-text-block":
                            type = Types.createType($scope.dataShepard.current(), 'answer', 'text', directiveType);
                            $scope.block = type;
                            el = $compile("<plain-text-suit-block block='{[{ block }]}'></plain-text-suit-block>")($scope.$new(true));
                            break;
                        case 'code-block':
                            type = Types.createType($scope.dataShepard.current(), 'answer', 'text', directiveType);
                            $scope.block = type;
                            el = $compile("<code-block-suite block='{[{ block }]}'></code-block-suite>")($scope.$new(true));
                            break;
                        case 'select-block':
                        case 'checkbox-block':
                        case 'radio-block':
                            type = Types.createType($scope.dataShepard.current(), 'answer', 'object', directiveType);
                            $scope.block = type;
                            el = $compile("<generic-block-suit block='{[{ block }]}'></generic-block-suit>")($scope.$new(true));
                            break;
                    }

                    $('.SuitBlocks').append(el);
                    $scope.dataShepard.next();
                }
            };
        },
        link: function(scope, $elem, $attrs) {

            var createFirstElement = function() {
                var type = Types.createType(scope.dataShepard.current(), 'question', 'text', 'plain-text-block');
                scope.block = type;

                var el = $compile("<plain-text-suit-block block='block'></plain-text-suit-block>")(scope.$new());
                $('.SuitBlocks').append(el);
                scope.dataShepard.next();
            };

            if(scope.minId > 0) {
                var promise = Test.getTest(scope.minId);

                promise.then(function(data, status, headers, config) {
                    var test = JSON.parse(data.data.test);
                    for(var i = 0; i < test.length; i++) {
                        var t = test[i];
                        var elem;

                        BlockType.save(t);

                        var blockType = t.data.type;
                        if(blockType === 'plain-text-block') {
                            elem = $compile("<plain-text-suit-block></plain-text-suit-block>")(scope.$new(true));
                        }
                        else if(blockType === 'code-block') {
                            elem = $compile("<code-block-suite></code-block-suite>")(scope.$new(true));
                        }

                        $('.SuitBlocks').append(elem);
                    }
                }, function(data, status, headers, config) {
                    createFirstElement();
                });
            }
            else if(scope.minId === 0) {
                createFirstElement();
            }

            scope.next = {
                submit: function($event) {
                    $event.preventDefault();

                    console.log(DataShepard.current());
                    if(DataShepard.current() > 1) {
                        scope.$broadcast('await-next', {
                            awaiting: true
                        });

                        var promise = Test.saveTest(scope.currentTestId, scope.dataShepard.all());

                        promise.then(function(data, status, headers, config) {
                            scope.$broadcast('await-next', {
                                awaiting: false
                            });

                            scope.$broadcast('destroy-directive', {});
                            DataShepard.clear();

                            createFirstElement();

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
});
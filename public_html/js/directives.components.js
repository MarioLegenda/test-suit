"use strict";

angular.module('suit.directives.components', [])
    .directive('absoluteInfoBox', ['$timeout', function($timeout) {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: 'absoluteInfoBox.html',
            scope: {},
            controller: function($scope) {
                $scope.infoBox = {
                    boxes: [],
                    signature: [],
                    listingData: [],
                    showItem: function(items, signature) {
                        if(items[signature].hasOwnProperty('date')) {
                            var d, date;

                            d = new Date(items[signature].date);
                            date = d.getDate() + '.' + (d.getMonth() + 1) + '.' + d.getFullYear();

                            return date;
                        }
                        return items[signature];
                    }
                }
            },
            link: function($scope, elem, attrs) {
                $scope.$on('action-populate-info-box', function($event, data) {
                    $scope.infoBox.boxes = data.boxes;
                    $scope.infoBox.signature = data.signature;
                    $scope.infoBox.listingData = data.listingData;

                    $timeout(function() {
                        var parH, elemH;

                        parH = elem.parent().outerHeight();
                        elemH = elem.outerHeight();

                        if(elemH <= parH) {
                            elem.css({
                                height: (parH - 2) + 'px'
                            });
                        }
                        else if(elemH > parH) {
                            elem.css({
                                height: elemH + 'px'
                            });
                        }

                        elem.show();
                    }, 300);
                });

                $scope.infoBox.close = function($event) {
                    $event.preventDefault();
                    elem.css({
                        height: 'auto'
                    });
                    elem.hide();
                }
            }
        }
    }])
    .directive('actionGlobalError', function() {
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
                            console.log($scope.action.url);
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
                        promise = User.getUsersById(scope.expandingId);
                    }
                    if(scope.factoryType === 'test') {
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
}).directive('permission', [function() {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'permission.html',
        controller: function($scope) {
        },
        link: function($scope, elem, attrs) {
            $scope.permission = {
                assigned: (function() {
                    if(typeof attrs.userId === 'undefined') {
                        return false;
                    }

                    return $scope.newTest.directiveQuery.isAssigned(attrs.userId) === true;
                } () ),
                notAssigned: (function() {
                    if(typeof attrs.userId === 'undefined') {
                        return false;
                    }

                    return $scope.newTest.directiveQuery.isAssigned(attrs.userId) === false;
                } () ),
                assign: function($event, userId) {
                    $event.preventDefault();

                    $scope.$emit('action-assign-user', {
                        user_id: userId
                    });

                    this.assigned = true;
                    this.notAssigned = false;
                },
                remove: function($event, userId) {
                    $scope.$emit('action-remove-user', {
                        user_id: userId
                    });

                    this.notAssigned = true;
                    this.assigned = false;
                }
            };
        }
    }
}]).directive('restrictedPermission', [function() {

}]).directive('filter', [function() {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'filter.html',
        controller: function($scope) {

            $scope.menus = {
                filterUsername: false,
                filterPersonal: false,

                toggle: function ($event, type) {
                    $scope.directiveData.username = '';
                    $scope.directiveData.name = '';
                    $scope.directiveData.lastname = '';
                    if (!this.hasOwnProperty(type)) {
                        throw Error('ManagmentMenu: Wrong type ' + type);
                    }

                    this[type] = !this[type];
                    var menus = ['filterUsername', 'filterPersonal'];
                    menus.splice(menus.indexOf(type), 1);

                    for (var i = 0; i < menus.length; i++) {
                        this[menus[i]] = false;
                    }
                }
            }
        },
        link: function($scope, elem, attrs) {
            var showOnStart = attrs.showOnStart;

            if(typeof showOnStart !== 'undefined') {
                $scope.menus[showOnStart] = true;
            }

            var decideEvnObject = function(type) {
                var eventObj = {
                    filterType: null,
                    key: null,
                    username: '',
                    personal: {
                        name: '',
                        lastname: ''
                    }
                };

                if(type === 'username') {
                    eventObj.filterType = 'username-filter';
                    eventObj.key = 'username';
                    eventObj.username =  $scope.directiveData.username;
                    delete eventObj.personal;
                }
                else {
                    eventObj.filterType = 'personal-filter';
                    eventObj.key = 'personal';
                    eventObj.personal.name = $scope.directiveData.name;
                    eventObj.personal.lastname =  $scope.directiveData.lastname;
                    delete eventObj.username;
                }

                return eventObj;
            };

            $scope.directiveData = {
                username: '',
                name: '',
                lastname: '',

                submit: function($event, type) {
                    $event.preventDefault();
                    var eventObj = decideEvnObject(type);
                    $scope.$emit('action-user-filter', eventObj);

                    return false;
                },

                interaction: function($event, type) {
                    var eventObj = decideEvnObject(type),
                        minStrLen = attrs.minimalWordLength;

                    if(typeof minStrLen !== 'undefined') {
                        if(this[type].length > parseInt(minStrLen)) {
                            $scope.$emit('action-user-filter', eventObj);
                        }
                    }
                    else if(this[type].length >= 0) {
                        $scope.$emit('action-user-filter', eventObj);
                    }
                }
            };
        }
    }
}]).directive('listingComponent', ['$compile', 'CompileCommander', function($compile, CompileCommander) {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                listing: '=listing',
                directiveType: '@directiveType',
                showLoadMore: '=showLoadMore'
            },
            templateUrl: 'listingTemplate.html',
            controller: function($scope) {
                $scope.directiveData = {
                    directiveType: $scope.directiveType,
                    loadMore: function($event) {
                        $scope.$emit('action-load-more', {});
                    },
                    showLoadMore: $scope.showLoadMore,
                    noData: false,
                    noDataMessage: ''
                };

                $scope.$watch(function() { return $scope.listing.length }, function(newVal, oldVal, scope) {
                    if(newVal === 0) {
                        scope.directiveData.noData = true;
                    }
                    else {
                        scope.directiveData.noData = false;
                    }
                });



            }
        };
}]).directive('userRow', ['User', 'Toggle', function(User, Toggle) {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'userRow.html',
        controller: function($scope) {
        },
        link: function($scope, elem, attrs) {
            var clicked = false;
            $scope.directiveData = {
                userInfo: {},
                expandSection: function($event, user_id) {
                    $event.preventDefault();
                    var promise,
                        userInfo;

                    if (clicked === false) {
                        if (typeof $scope.directiveData.userInfo[user_id] === 'undefined') {
                            promise = User.getUsersById(user_id);

                            promise.then(function (data, status, headers, config) {
                                userInfo = data.data.user;
                                $scope.directiveData.userInfo[userInfo.user_id] = userInfo;
                                elem.find('.Expandable--info').show();
                                elem.find('.Expandable--click').css({
                                    backgroundColor: '#2C84EE'
                                });
                                clicked = true;
                            });
                        }
                        else {
                            elem.find('.Expandable--info').show();
                            elem.find('.Expandable--click').css({
                                backgroundColor: '#2C84EE'
                            });
                            clicked = true;
                        }
                    }
                    else if (clicked === true) {
                        elem.find('.Expandable--info').hide();
                        elem.find('.Expandable--click').css({
                            backgroundColor: '#424242'
                        });
                        clicked = false;
                    }
                }
            };
        }
    }
}]).directive('testRow', ['Test', 'Toggle', function(Test, Toggle) {
    return {
        restring: 'E',
        replace: true,
        templateUrl: 'testRow.html',
        controller: function($scope) {
        },
        link: function($scope, elem, attrs) {
            Toggle.create('testRow',{
                enter: function() {
                    this.elem.find('.Expandable--info').show();
                    this.elem.find('.Expandable--click').css({
                        backgroundColor: '#2C84EE'
                    });
                },
                exit: function() {
                    this.elem.find('.Expandable--info').hide();
                    this.elem.find('.Expandable--click').css({
                        backgroundColor: '#424242'
                    });
                }
            });

            $scope.directiveData = {
                expandSection: function($event) {
                    var clickedElem = $($event.currentTarget),
                        parentElem = clickedElem.parent();

                    Toggle.toggle('testRow', {
                        elem: elem
                    });
                },
                deleteTest: function(testId) {
                    $scope.$emit('action-delete-test', {id: testId});
                },
                changeMetadata: function(testId) {
                    $scope.$emit('action-test-metadata-change', {testId: testId});
                },
                workspace: function(testId) {
                    $scope.$emit('action-workspace', {testId: testId});
                },
                absoluteInfoBox: function($event, permissions) {
                    $event.preventDefault();

                    var promise = Test.getPermittedUsers({
                        user_ids: permissions.assigned_users
                    });

                    promise.then(function(data, status, headers, config) {
                        var users = data.data.users;

                        $scope.$broadcast('action-populate-info-box', {
                            boxes: ['Name', 'Lastname', 'Username', 'Created'],
                            signature: ['name', 'lastname', 'username', 'logged'],
                            listingData: users
                        });
                    }, function(data, status, headers, config) {
                        console.log('Something went wrong', data);
                    });
                }
            }
        }
    }
}]).directive('assignedTestRow', [function() {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'assignedTestRow.html',
        controller: function($scope) {

        },
        link: function($scope, elem, attrs) {

        }
    }
}]);
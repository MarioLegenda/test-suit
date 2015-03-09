"use strict";

angular.module('suit.directives.components', [])
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
});
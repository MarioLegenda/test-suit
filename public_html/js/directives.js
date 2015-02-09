"use strict";

angular.module('suite.directives', []).directive('processUiInstalling', function($window, $timeout) {
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
                else if(scope.process.installed === true) {

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
}).directive('expandSection', function($) {
    return {
        restrict: 'A',
        scope: {
            expandHeight: '@expandHeight'
        },
        link : function(scope, elem, attrs) {
            var currHeight = elem.innerHeight();
            var show = false;

            elem.click(function() {
                if(show === false) {
                    show = true;
                    $(this).animate({
                        height: scope.expandHeight + 'px'
                    }, 500);

                    elem.css({
                        border:'1px solid #2C84EE'
                    });

                    elem.find('.ExpandableClick').css({
                        backgroundColor: '#2C84EE'
                    });
                }
                else if(show === true) {
                    show = false;
                    $(this).animate({
                        height: (currHeight + 2) + 'px'
                    }, 500);

                    elem.css({
                        border:'1px solid white'
                    });

                    elem.find('.ExpandableClick').css({
                        backgroundColor: 'transparent'
                    });
                }
            });
        }
    }
});
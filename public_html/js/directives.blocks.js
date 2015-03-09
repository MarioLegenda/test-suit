"use strict";

angular.module('suit.directives.blocks', [])
.directive('plainTextSuitAnswerBlock', function(DataShepard, Types, $timeout) {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            nonCompiled: '@nonCompiled',
            blockId: '@blockId',
            dataType: '@blockDataType',
            blockType: '@blockType',
            directiveType: '@blockDirectiveType',
            dataShepard: '=shepard'
        },
        templateUrl: 'plainTextSuiteAnswer.html',
        controller: function($scope) {
            if($scope.nonCompiled === 'true') {
                $scope.block = Types.createType(
                    $scope.blockId,
                    $scope.dataType,
                    $scope.blockType,
                    $scope.directiveType
                );

                $scope.dataShepard.add($scope.block.blockId, $scope.block);
            }
            else if($scope.dataShepard.isPreCompiled() === true) {
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
})
    .directive('codeSuitAnswerBlock', function(DataShepard, Types, $timeout) {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                nonCompiled: '@nonCompiled',
                blockId: '@blockId',
                dataType: '@blockDataType',
                blockType: '@blockType',
                directiveType: '@blockDirectiveType',
                dataShepard: '=shepard'
            },
            templateUrl: 'codeSuiteAnswer.html',
            controller: function($scope) {
                if($scope.nonCompiled === 'true') {
                    $scope.block = Types.createType(
                        $scope.blockId,
                        $scope.dataType,
                        $scope.blockType,
                        $scope.directiveType
                    );

                    $scope.dataShepard.add($scope.block.blockId, $scope.block);
                }
                else if($scope.dataShepard.isPreCompiled() === true) {
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
    })
    .directive('plainTextSuitBlock', function(DataShepard, Types, $timeout) {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                nonCompiled: '@nonCompiled',
                blockId: '@blockId',
                dataType: '@blockDataType',
                blockType: '@blockType',
                directiveType: '@blockDirectiveType',
                dataShepard: '=shepard'
            },
            templateUrl: 'plainTextSuite.html',
            controller: function($scope) {
                if($scope.nonCompiled === 'true') {
                    $scope.block = Types.createType(
                        $scope.blockId,
                        $scope.dataType,
                        $scope.blockType,
                        $scope.directiveType
                    );

                    $scope.dataShepard.add($scope.block.blockId, $scope.block);
                }
                else if($scope.dataShepard.isPreCompiled() === true) {
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
                nonCompiled: '@nonCompiled',
                blockId: '@blockId',
                dataType: '@blockDataType',
                blockType: '@blockType',
                directiveType: '@blockDirectiveType',
                dataShepard: '=shepard'
            },
            templateUrl: 'codeBlockSuite.html',
            controller: function($scope) {
                if($scope.nonCompiled === 'true') {
                    $scope.block = Types.createType(
                        $scope.blockId,
                        $scope.dataType,
                        $scope.blockType,
                        $scope.directiveType
                    );

                    $scope.dataShepard.add($scope.block.blockId, $scope.block);
                }
                else if($scope.dataShepard.isPreCompiled() === true) {
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

                editor.on('change', function(e) {
                    scope.block.data.data = editor.getValue();
                    scope.dataShepard.update(scope.block.blockId, scope.block);
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
                nonCompiled: '@nonCompiled',
                blockId: '@blockId',
                dataType: '@blockDataType',
                blockType: '@blockType',
                directiveType: '@blockDirectiveType',
                dataShepard: '=shepard'
            },
            templateUrl: 'selectBlockSuit.html',
            controller: function($scope) {
                if($scope.nonCompiled === 'true') {
                    $scope.block = Types.createType(
                        $scope.blockId,
                        $scope.dataType,
                        $scope.blockType,
                        $scope.directiveType
                    );

                    $scope.dataShepard.add($scope.block.blockId, $scope.block);
                }
                else if($scope.dataShepard.isPreCompiled() === true) {
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
            link: function($scope, elem, attrs) {

                if($scope.nonCompiled === 'true') {
                    $scope.block.data.data[$scope.block.data.data.length] = '';
                }

                $scope.blockEvents = {
                    remove: function() {
                        $scope.dataShepard.remove($scope.block.blockId);
                        $scope.$destroy();
                        elem.remove();
                    },
                    addSelectBox: function() {
                        $scope.block.data.data[$scope.block.data.data.length] = '';
                    }
                };

                $scope.$on("destroy-directive",function(event, data) {
                    $timeout(function() {
                        $scope.dataShepard.remove($scope.block.blockId);
                        $scope.$destroy();
                        elem.remove();
                    }, 500);
                });
            }
        }
    });
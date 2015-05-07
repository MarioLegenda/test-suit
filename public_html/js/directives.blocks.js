"use strict";

angular.module('suit.directives.blocks', [])
.directive('plainTextSuitAnswerBlock', function(Types, $timeout) {
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
    .directive('codeSuitAnswerBlock', function(Types, $timeout) {
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
    .directive('plainTextSuitBlock', function(Types, $timeout) {
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
    }).directive('codeBlockSuite', function(Types, $timeout) {
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
    }).directive('genericBlockSuit', function(Types, $timeout) {
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
    }).directive('questionCodeBlock', [function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                data: '=data'
            },
            templateUrl: 'question.codeQuestionBlock.html',
            controller: function($scope) {
                $scope.directiveData = {
                    data: $scope.data
                };

                $scope.directiveData.data.answer = true;
            },
            link: function($scope, elem, attrs) {

                var editor = ace.edit(elem.find('.AceEditor').get(0));
                var session = editor.getSession();

                editor.getSession().setMode("ace/mode/javascript");
                editor.renderer.setShowGutter(true);
                editor.setFontSize(18);
                editor.setHighlightActiveLine(true);
                editor.setWrapBehavioursEnabled(true);
                editor.setReadOnly(true);
                editor.setOption('firstLineNumber', 1);
                editor.setValue($scope.directiveData.data.data);
            }
        }
    }]).directive('questionTextBlock', [function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                data: '=data'
            },
            templateUrl: 'question.textQuestionBlock.html',
            controller: function($scope) {
                $scope.directiveData = {
                    data: $scope.data
                };
            }
        }
    }]).directive('answerRadioBlock', [function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                data: '=data'
            },
            templateUrl: 'answer.radioQuestionBlock.html',
            controller: function($scope) {
                $scope.directiveData = {
                    data: $scope.data,
                    answer: function(index, answer) {
                        $scope.data.answer = { index: index, answer: answer };
                    },
                    check: function(index) {
                        if($scope.data.hasOwnProperty('answer')) {
                            var answer = $scope.data.answer.index;

                            return index === parseInt(answer);
                        }
                    }
                };
            }
        }
    }]).directive('answerSelectBlock', ['$', '$timeout', function($, $timeout) {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                data: '=data'
            },
            templateUrl: 'answer.selectQuestionBlock.html',
            controller: function($scope) {
                $scope.directiveData = {
                    selected: null,
                    data: ( function() {
                        var options = [];
                        $scope.data.data.forEach(function(value, index, array) {
                            var temp = {
                                id: index,
                                value: value
                            };

                            options.push(temp);
                        });

                        return options;
                    } () )
                };

                $scope.directiveData.selected = $scope.directiveData.data[0];
            },
            link: function($scope, elem, attr) {
                elem.find('.Select').change(function() {
                    var index = parseInt($(this).find('option:selected').val()),
                        answer = $(this).find('option:selected').text();
                    $scope.data.answer = { index: index, answer: answer };
                    $scope.$apply();
                });

                if ($scope.data.hasOwnProperty('answer')) {
                    var answer = $scope.data.answer.index;
                    $timeout(function() {
                        elem.find('.Select option[value=\'' + answer + '\']').attr('selected', 'selected');
                    }, 1000);
                }
            }
        }
    }]).directive('codeAnswerBlock', [function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                data: '=data'
            },
            templateUrl: 'answer.codeQuestionBlock.html',
            controller: function($scope) {
                $scope.directiveData = {
                    data: $scope.data
                };
            },
            link: function($scope, elem, attrs) {
                var editor = ace.edit(elem.find('.AceEditor').get(0));

                editor.getSession().setMode("ace/mode/javascript");
                editor.renderer.setShowGutter(true);
                editor.setFontSize(18);
                editor.setHighlightActiveLine(true);
                editor.setWrapBehavioursEnabled(true);
                editor.setOption('firstLineNumber', 1);
                editor.setValue((function() {
                    if($scope.data.hasOwnProperty('answer')) {
                        return $scope.data.answer.answer;
                    }
                } () ));

                editor.on('change', function(e) {
                    $scope.data.answer = { index: null, answer: editor.getValue() };
                });
            }
        }
    }]).directive('answerTextBlock', [function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                data: '=data'
            },
            templateUrl: 'answer.textQuestionBlock.html',
            controller: function($scope) {
                $scope.directiveData = {
                    data: $scope.data
                };

                $scope.data.answer = {index: null, answer: ( function() {
                    if($scope.data.hasOwnProperty('answer')) {
                        return $scope.data.answer.answer;
                    }

                    return '';
                } () )};
            }
        }
    }]).directive('answerCheckboxBlock', ['$timeout', function($timeout) {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                data: '=data'
            },
            templateUrl: 'answer.checkboxQuestionBlock.html',
            controller: function($scope) {
                $scope.directiveData = {
                    data: $scope.data,
                    answer: function(index, answer) {
                        $scope.data.answer.push({
                            index: index,
                            answer: answer
                        });
                    }
                };
            },
            link: function($scope, elem, attrs) {
                if ($scope.data.hasOwnProperty('answer')) {
                    var answer = $scope.data.answer;
                    $timeout(function() {
                        var checkbox = elem.find('.CheckboxRow input[type=checkbox]');

                        checkbox.each(function(i, v, a) {
                            var context = $(v);
                            answer.forEach(function(value, index, arr) {
                                if(context.val() === value.answer) {
                                    context.attr('checked', 'checked');
                                }
                            });
                        });
                    }, 1000);
                }
            }
        }
    }]);
angular.module('suit.factories', []).factory('$', [function () {
    return jQuery;
}]).factory('installFactory', function ($http) {
    return {
        config: {},

        setConfig: function (config) {
            this.config = config;
            return this;
        },

        install: function () {
            return $http(this.config);
        }
    };
}).factory('DataShepard', function() {
    function DataShepard() {
        var counter = 0,
            heard = [],
            precompiled = false;

        this.setPrecompiled = function() {
            precompiled = true;
        };

        this.isPreCompiled = function() {
            return precompiled;
        };

        this.syncCurrentId = function() {
            var highest = 0;
            for(var i = 0; i < heard.length; i++) {
                var blockId = parseInt(heard[i].blockId);
                if(blockId > highest) {
                    highest = blockId;
                }
            }

            counter = ++highest;
        };

        this.current = function() {
            return counter;
        };

        this.remove = function(id) {
            for(var i = 0; i < heard.length; i++) {
                var block = heard[i];
                if(block.blockId == id) {
                    heard.splice(i, 1);
                    break;
                }
            }
        };

        this.next = function() {
            counter++;
        };

        this.add = function(id, object) {
            heard[heard.length] = object;
            return this;
        };

        this.update = function(id, object) {
            for(var i = 0; i < heard.length; i++) {
                var block = heard[i];
                if(block.blockId == id) {
                    heard[i] = object;
                    break;
                }
            }
        };

        this.get = function(id) {
            for(var i = 0; i < heard.length; i++) {
                var block = heard[i];
                if(block.blockId == id) {
                    return heard[i];
                }
            }

            throw new Error('DataShepard: ' + id + ' was not found');
        };

        this.all = function() {
            var tempHeard = [];
            for(var i = 0; i < heard.length; i++) {
                if(typeof heard[i] !== 'undefined') {
                    tempHeard.push(heard[i]);
                }
            }

            return tempHeard;
        };

        this.arangeBlockIds = function() {
            for(var i = 0; i < heard.length; i++) {
                heard[i].blockId = i;
            }
        };

        this.clearHeard = function() {
            heard = [];
            return this;
        };

        this.clearCounter = function() {
            counter = 0;
            return this;
        };

        this.instance = function() {
            return new DataShepard();
        }
    }

    return new DataShepard();
}).factory('RangeIterator', function(){
    return {
        initIterator: function(arr) {
            return ( function(arr) {
                function RangeIterator(arr) {
                    var range = arr,
                        counter = 0;

                    this.currentCounter = function() {
                        return counter + 1;
                    };

                    this.lastCounter = function() {
                        return range.length + 1;
                    };

                    this.current = function() {
                        return range[counter];
                    };

                    this.next = function() {
                        counter++;

                        if(typeof range[counter] === 'undefined') {
                            counter = 0;
                            return range[counter];
                        }

                        return range[counter];
                    };

                    this.previous = function() {
                        counter--;
                        if(counter < 0) {
                            counter = range.length - 1;
                        }

                        if(typeof range[counter] === 'undefined') {
                            counter = 0;
                            return range[counter];
                        }

                        return range[counter];
                    };
                }

                return new RangeIterator(arr);
            } (arr) )
        }
    }
}).factory('DataMediator', function() {
    function DataMediator() {
        var mediateData = {};

        this.addMediateData = function(dataKey, data) {
            mediateData[dataKey] = data;
        };

        this.getMediateData = function(dataKey) {
            if(mediateData.hasOwnProperty(dataKey)) {
                return mediateData[dataKey];
            }

            return null;
        };

        this.clear = function() {
            mediateData = {};
        }
    }

    return new DataMediator();
});

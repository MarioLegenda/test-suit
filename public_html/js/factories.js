"use strict";

angular.module('suite.factories', []).factory('$', function () {
    return jQuery;
}).factory('installFactory', function ($http) {
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
}).factory('formHandler', function() {
    var initForm = {};

    initForm.init = function($scope, formName) {
        return {
            invalidSum: 0,
            invalidForm: null,

            notExists: function(prop) {
                return $scope[formName][prop].$error.required && $scope[formName][prop].$dirty;
            },

            notMinLength: function(prop) {
                return $scope[formName][prop].$error.minlength;
            },

            notEmail: function(prop) {
                return $scope[formName][prop].$error.email;
            },

            notEquals: function(prop1, prop2) {
                if($scope[formName][prop1].$viewValue !== $scope[formName][prop2].$viewValue) {
                    this.invalidSum++;
                    return true;
                }

                this.invalidSum = 0;
                this.invalidForm = true;
                return false;
            },

            enforceChecked: function(numOfCheckboxes, arrOfBoxes) {
                var checked = 0;
                for(var i = 0; i < arrOfBoxes.length; i++) {
                    if($scope[formName][arrOfBoxes[i]].$modelValue == true) {
                        checked++;
                    }
                }

                return checked === 0;
            },

            hasArrayValues: function(arr) {
                var isArray = ({}).toString.call(arr).match(/\s([a-zA-Z]+)/)[1].toLowerCase()
                if(isArray === 'array') {
                    return arr.length === 0;
                }

                return false;
            },

            notValidArray: function(arr, value) {
                for(var i = 0; i < arr.length; i++) {
                    if(arr[i] === value) {
                        return true;
                    }
                }

                return false;
            },

            notEmpty: function(arr, value) {
                if(arr.length === 0) {
                    this.invalidForm = true;
                    return true;
                }

                this.invalidForm = false;
                return false;
            },

            regexValid: function(prop) {
                return $scope[formName][prop].$error.pattern;
            },

            isValidForm: function() {
                if($scope[formName].$valid && this.invalidForm === true) {
                    return true;
                }

                if($scope[formName].$valid && this.invalidForm === null) {
                    return true;
                }

                if($scope[formName].$valid && this.invalidForm === false) {
                    return true;
                }

                return false;
            }
        };
    };

    return initForm;
}).factory('User', function($http) {
    return {
        urls: {
            saveUrl: '/app_dev.php/save-user',
            allUsersUrl: '/app_dev.php/user-managment/user-list',
            userInfo: '/app_dev.php/user-managment/user-info'
        },

        save: function(user) {
            return $http({
                method: 'POST',
                url: this.urls.saveUrl,
                data: user
            });
        },

        getUsersById: function(id, cache) {
            cache = typeof cache !== 'undefined';
            return $http({
                method: 'POST',
                url: this.urls.userInfo,
                data: id,
                cache: cache
            });
        },

        getUserByUsername: function(username) {

        },

        deleteUserById: function(id) {

        },

        deleteUserByUsername: function(username) {

        },

        getAllUsers: function() {
            return $http({
                method: 'POST',
                url: this.urls.allUsersUrl
            });
        }
    };
}).factory('List', function() {
    function List() {
        var list = [],
            index = 0;

        this.add = function(object, compareWith) {
            if(list.length === 0) {
                list[0] = object;
                return list;
            }

            var itemExists = false;
            for(var i = 0; i < list.length; i++) {
                var item = list[i];

                if(item[compareWith] === object[compareWith]) {
                    itemExists = true;
                }
            }

            if( ! itemExists) {
                list[list.length] = object;
            }

            return list;
        };

        this.remove = function(object, compareWith) {
            for(var i = 0; i < list.length; i++) {
                var item = list[i];

                if(item[compareWith] === object[compareWith]) {
                    list.splice(i, 1);
                    break;
                }
            }

            return list;
        };

        this.clear = function() {

        };
    }

    return new List();
}).factory('TestControl', function($http) {
    return {
        urls: {
            saveTestUrl: '/app_dev.php/create-test'
        },

        saveTest: function(data) {
            return $http({
                method: 'POST',
                url: this.urls.saveTestUrl,
                data: data
            });
        }
    };
}).factory('DataShepard', function() {
    function DataShepard() {
        var counter = 0,
            heard = [];

        this.current = function() {
            return counter;
        };

        this.reverse = function() {
            counter--;
        };

        this.exact = function() {
            return counter - 1;
        };

        this.next = function() {
            counter++;
        };

        this.add = function(object) {
            heard[heard.length] = object;
            return this;
        };

        this.get = function(index) {
            if((index in list) === false) {
                return null;
            }

            return heard[index];
        };

        this.all = function() {
            return heard;
        };

        this.clear = function() {
            counter = 0;
            heard = [];
        };
    }

    return new DataShepard();
}).factory('BlockType', function() {
    return {
        initObj: null,
        save: function(initObj) {
            this.initObj = initObj;
        },
        create: function() {
            var immutableTemp =  {
                type: this.initObj.type,
                block_type: this.initObj.block_type,
                element: this.initObj.element,
                block_id: this.initObj.block_id,
                placeholder: this.initObj.placeholder,
                data: {
                    type: this.initObj.data.type,
                    data: null
                }
            };

            this.initObj = null;

            return immutableTemp;
        }
    }
});
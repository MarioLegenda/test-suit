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
            equalChecks: 0,

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
                    this.equalChecks++;
                    return true;
                }

                this.equalChecks = 0;
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

            isValidForm: function() {
                if($scope[formName].$valid && this.equalChecks == 0) {
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
            saveUrl: '/app_dev.php/save-user'
        },

        save: function(user) {
            return $http({
                method: 'POST',
                url: this.urls.saveUrl,
                data: user
            });
        },

        getUserById: function(id) {

        },

        getUserByUsername: function(username) {

        },

        deleteUserById: function(id) {

        },

        deleteUserByUsername: function(username) {

        }
    };
});
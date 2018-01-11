angular.module('myApp', ['ngMaterial', 'ngMessages', 'ngFileUpload'])
.config(function ($mdThemingProvider) {
    $mdThemingProvider.theme('default')
    .primaryPalette('deep-orange')
    .accentPalette('orange');
})
.controller('AppCtrl', function($scope, Upload, $timeout) {
    $scope.question = {};
    $scope.uploadfiles = null;
    $scope.postQuestion = function() {
        console.log('Post!');
        console.log($scope.question);
    };
    $scope.uploadFiles = function(file, errFiles) {
        $scope.f = file;
        $scope.errFile = errFiles && errFiles[0];
        $scope.progress = false;
        $scope.question.image = null;
        if (file) {
        file.upload = Upload.upload({
            url: 'https://yaimapp.38qa.net/easy-ask-file-upload',
            data: {file: file}
        });

        file.upload.then(function (response) {
            $timeout(function() {
            file.result = response.data;
            console.log(response.data.files[0]);
            $scope.question.image = response.data.files[0].url;
            $scope.progress = false;
            });
        }, function (response) {
            if (response.status > 0) {
            $scope.errorMsg = response.status + ': ' + response.data;
            $scope.question.image = null;
            $scope.progress = false;
            }
        }, function (evt) {
            $scope.progress = true;
        });
        }
    }
});
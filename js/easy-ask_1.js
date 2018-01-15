angular.module('myApp', ['ngMaterial', 'ngMessages', 'ngFileUpload'])
.config(function ($mdThemingProvider) {
    $mdThemingProvider.theme('default')
    .primaryPalette('deep-orange')
    .accentPalette('orange');
})
.controller('AppCtrl', function($scope, Upload, $timeout, $http, $mdDialog) {
    $scope.question = {
        'image': []
    };
    $scope.uploadfiles = null;
    $scope.postQuestion = function(ev) {
        console.log($scope.question);
        
        var confirm = $mdDialog.confirm()
          .parent(angular.element(document.body))
          .title(easyask.lang.confirm_title)
          .textContent(easyask.lang.confirm_content)
          .ariaLabel('post-question')
          .targetEvent(ev)
          .ok(easyask.lang.label_post)
          .cancel(easyask.lang.label_cancel);

        $mdDialog.show(confirm).then(function() {
            var content = getContent($scope.question);

            var params = {};
            params.title = easyask.lang.q1_title+$scope.question.place.substr(0, 20);
            params.content = content;
            params.category_id = 38;
            params.code = easyask.code;
            $http({
                method: 'POST',
                url: '/easy-ask-post-question',
                data: params
            }).success(function(data, status, headers, config) {
                console.log('success');
                console.log(data);
                console.log(status);
            }).error(function(data, status, headers, config) {
                console.log('error');
                console.log(data);
                console.log(status);
            });
        });
    };
    $scope.uploadFiles = function(file, idx, errFiles) {
        $scope.f = file;
        $scope.errFile = errFiles && errFiles[0];
        $scope.progress = false;
        $scope.question.image[idx] = null;
        if (file) {
            file.upload = Upload.upload({
                url: '/easy-ask-file-upload',
                data: {file: file}
            });

            file.upload.then(function (response) {
                $timeout(function() {
                    file.result = response.data;
                    $scope.question.image[idx] = response.data.files[0].url;
                    $scope.progress = false;
                });
            }, function (response) {
                if (response.status > 0) {
                    $scope.errorMsg = response.status + ': ' + response.data;
                    $scope.question.image[idx] = null;
                    $scope.progress = false;
                }
            }, function (evt) {
                $scope.progress = true;
            });
        }
    }
    var getContent = function(question) {

        var content = '';
        if (question.image[0]) {
            content += '<div class="medium-insert-images">';
            content += '<div class="image-url">[image="'+question.image[0]+'"]</div>';
            content += '</div>';
        }
        if (question.image[1]) {
            content += '<div class="medium-insert-images">';
            content += '  <div class="image-url">[image="'+question.image[1]+'"]</div>';
            content += '</div>';
        }
        if (question.image[2]) {
            content += '<div class="medium-insert-images">';
            content += '  <div class="image-url">[image="'+question.image[2]+'"]</div>';
            content += '</div>';
        }
        content += '<p>'+easyask.lang.q1_place+': <br>';
        content += question.place;
        content += '</p>';
        content += '<p>'+easyask.lang.q1_owned+': '+question.owned+'</p>';
        content += '<p>'+easyask.lang.q1_wind+': '+question.strong_wind+'</p>';
        content += '<p>'+easyask.lang.q1_sunlight+': '+question.direct_sunlight+'</p>';
        content += '<p>'+easyask.lang.q1_pesticide+': '+question.pesticide+'</p>';
        content += '<p>'+easyask.lang.q1_others+': '+question.other_people+'</p>';
        if (question.comment) {
            content += '<p>';
            content += easyask.lang.comment+':<br>';
            content += question.comment;
            content += '</p>';
        }
        content += '<p></p><p>'+easyask.lang.question_footer+'</p>';
        return content;
    }
});
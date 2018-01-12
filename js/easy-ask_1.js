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
          .title('質問を投稿しますか？')
          .textContent('この内容で質問を投稿しますか？内容を変更する場合は、いいえを押して入力内容を変更してください。投稿した質問は、後から編集することができます。')
          .ariaLabel('post-question')
          .targetEvent(ev)
          .ok('投稿する')
          .cancel('いいえ');

        // $mdDialog.show(confirm).then(function() {
            // var content = getContent($scope.question);

            // var params = {};
            // params.title = "捕獲用の巣箱はどこにおけばいいですか？"+$scope.question.place.substr(0, 20);
            // params.content = content;
            // params.category_id = 38;
            // params.code = $scope.question.security_code;
            // $http({
            //     method: 'POST',
            //     url: '/easy-ask-post-question',
            //     data: params
            // }).success(function(data, status, headers, config) {
            //     console.log('success');
            //     console.log(data);
            //     console.log(status);
            // }).error(function(data, status, headers, config) {
            //     console.log('error');
            //     console.log(data);
            //     console.log(status);
            // });
        // });
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
        content += '<p>場所の概要: <br>';
        content += question.place;
        content += '</p>';
        content += '<p>自分の土地ですか？: '+question.owned+'</p>';
        content += '<p>風が強いですか？: '+question.strong_wind+'</p>';
        content += '<p>直射日光が強く当たる場所ですか？: '+question.direct_sunlight+'</p>';
        content += '<p>農薬の散布がされる場所ですか？: '+question.pesticide+'</p>';
        content += '<p>人が近づくような場所ですか？: '+question.other_people+'</p>';
        if (question.comment) {
            content += '<p>';
            content += 'コメント:<br>';
            content += question.comment;
            content += '</p>';
        }
        content += '<p>この質問は、簡単質問フォームで投稿されました。</p>';
        return content;
    }
});
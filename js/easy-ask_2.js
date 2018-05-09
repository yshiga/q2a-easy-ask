angular.module('myApp', ['ngMaterial', 'ngMessages', 'ngFileUpload'])
.config(function ($mdThemingProvider) {
    $mdThemingProvider.theme('default')
    .primaryPalette('deep-orange')
    .accentPalette('orange');
})
.controller('AppCtrl', function($scope, Upload, $timeout, $http, $mdDialog, $anchorScroll) {
    $scope.question = {
        'image': [],
    };
    $scope.uploadfiles = null;
    $scope.showForm = true;
    $scope.postDone = false;
    $scope.postID = null;
    $scope.warnOnLeave = false;
    $scope.uploadError = null;

    $scope.scrollToAnchor = function (anchor) {
        if (anchor !== null) {
            $anchorScroll(anchor);
        }
    };
     
    $scope.openQuestion = function () {
        if ($scope.postID) {
            location.href = '/'+$scope.postID;
        }
    };

    $scope.postQuestion = function(ev) {
        if ($scope.questionForm.$valid) {

            var confirm = $mdDialog.confirm()
            .parent(angular.element(document.body))
            .title(easyask.lang.confirm_title)
            .textContent(easyask.lang.confirm_content)
            .ariaLabel('post-question')
            .targetEvent(ev)
            .ok(easyask.lang.label_post)
            .cancel(easyask.lang.label_cancel);

            $mdDialog.show(confirm).then(function() {
                $scope.warnOnLeave = false;
                var content = getContent($scope.question);

                var params = {};
                var comment = $scope.question.comment;
                if (comment) {
                    comment = comment.replace(/\r?\n/g,"");
                }
                var today = new Date();
                var title = easyask.lang.title+' '+formatDate(today, easyask.lang.date_format)+' '+comment;
                params.title = title.substr(0, 50);
                params.content = content;
                params.category_id = 38;
                params.code = easyask.code;
                $http({
                    method: 'POST',
                    url: '/easy-ask-post-question',
                    data: params
                }).success(function(data, status, headers, config) {
                    $scope.postID = data.postid;
                    $scope.showForm = false;
                    $scope.postDone = true;
                }).error(function(data, status, headers, config) {
                    console.log('error');
                    var errorDialog = $mdDialog.alert()
                    .parent(angular.element(document.body))
                    .clickOutsideToClose(true)
                    .title(easyask.lang.error_title)
                    .textContent(easyask.lang.error_msg)
                    .ariaLabel('Error Happend')
                    .ok(easyask.lang.label_close);
                    $mdDialog.show(errorDialog);
                });
            });
        } else {
            if ($scope.questionForm.experience.$error.required) {
                $scope.scrollToAnchor('experience');
                return;
            }
            if ($scope.questionForm.hive_type.$error.required) {
                $scope.scrollToAnchor('hive_type');
                return;
            }
            if ($scope.questionForm.hive_num.$error.required) {
                $scope.scrollToAnchor('hive_num');
                return;
            }
            if ($scope.questionForm.hive_place.$error.required) {
                $scope.scrollToAnchor('hive_place');
                return;
            }
            if ($scope.questionForm.beeswax.$error.required) {
                $scope.scrollToAnchor('beeswax');
                return;
            }
            if ($scope.questionForm.use_lure.$error.required) {
                $scope.scrollToAnchor('use_lure');
                return;
            }
            if ($scope.questionForm.kinryohen.$error.required) {
                $scope.scrollToAnchor('kinryohen');
                return;
            }
            if ($scope.questionForm.comment.$error.required
             || $scope.questionForm.comment.$error.minlength
             || $scope.questionForm.comment.$error['md-maxlength']) {
                $scope.scrollToAnchor('comment');
                return;
            }
        }
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
                    var res = response.data.files[0];
                    if (res.name == 'error') {
                        $scope.uploadError = res.error;
                    } else {
                        $scope.question.image[idx] = res.url;
                        $scope.warnOnLeave = true;
                    }
                    $scope.progress = false;
                });
            }, function (response) {
                if (response.status > 0) {
                    $scope.uploadError = response.status + ': ' + response.data.files[0].error;
                    $scope.question.image[idx] = null;
                }
                $scope.progress = false;
            }, function (evt) {
                $scope.progress = true;
            });
        }
    }

    var getContent = function(question) {

        var content = '';
        content += '<p>'+easyask.lang.content_head+'</p>'
        content += '<p> &#8226; '+easyask.lang.experience+': '+question.experience+'</p>';
        content += '<p> &#8226; '+easyask.lang.hive_type+': '+question.hive_type+'</p>';
        content += '<p> &#8226; '+easyask.lang.hive_num+': '+question.hive_num+'</p>';
        content += '<p> &#8226; '+easyask.lang.hive_place+': '+question.hive_place+'</p>';
        content += '<p> &#8226; '+easyask.lang.beeswax+': '+question.beeswax+'</p>';
        content += '<p> &#8226; '+easyask.lang.use_lure+': '+question.use_lure+'</p>';
        content += '<p> &#8226; '+easyask.lang.kinryohen+': '+question.kinryohen+'</p>';
        if (question.comment) {
            content += '<p class="wrap-break-word">';
            content += easyask.lang.comment+':<br>';
            content += question.comment;
            content += '</p>';
        }
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
        content += '<p></p><p>'+easyask.lang.question_footer+'</p>';
        return content;
    }

    $('textarea[name="place"]').keypress(function() {
        $scope.warnOnLeave = true;
    });
    $('md-radio-button').click(function() {
        $scope.warnOnLeave = true;
    });
    $('textarea[name="comment"]').keypress(function() {
        $scope.warnOnLeave = true;
    });
    // 画面遷移時のイベント
    var onBeforeunloadHandler = function(e) {
        if($scope.warnOnLeave) {
            return '本当に移動しますか？'; 
        }
    };
    $(window).on('beforeunload', onBeforeunloadHandler);
});
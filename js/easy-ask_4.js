angular.module('myApp', ['ngMaterial', 'ngMessages', 'ngFileUpload'])
.config(function ($mdThemingProvider, $mdDateLocaleProvider) {
    $mdThemingProvider.theme('default')
    .primaryPalette('deep-orange')
    .accentPalette('orange');
    $mdDateLocaleProvider.formatDate = function(date) {
        return formatDate(date, easyask.lang.date_format);
    };
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
                var place = $scope.question.place;
                if (place) {
                    place = place.replace(/\r?\n/g,"");
                }
                var plan = $scope.question.plan;
                if (plan) {
                    plan = plan.replace(/\r?\n/g,"");
                }
                var title = easyask.lang.title+' '+place+' '+plan;
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
            if ($scope.questionForm.place.$error.required) {
                $scope.scrollToAnchor('place');
                return;
            }
            if ($scope.questionForm.petals.$error.required) {
                $scope.scrollToAnchor('petals');
                return;
            }
            if ($scope.questionForm.where_putting.$error.required) {
                $scope.scrollToAnchor('where_putting');
                return;
            }
            if ($scope.questionForm.temp.$error.required) {
                $scope.scrollToAnchor('temp');
                return;
            }
            if ($scope.questionForm.sun.$error.required) {
                $scope.scrollToAnchor('sun');
                return;
            }
            if ($scope.questionForm.water.$error.required) {
                $scope.scrollToAnchor('water');
                return;
            }
            if ($scope.questionForm.fertilizer.$error.required) {
                $scope.scrollToAnchor('fertilizer');
                return;
            }
            if ($scope.questionForm.when_bloom.$error.required) {
                $scope.scrollToAnchor('when_bloom');
                return;
            }
            if ($scope.questionForm.plan.$error.required
             || $scope.questionForm.plan.$error.minlength
             || $scope.questionForm.plan.$error['md-maxlength']) {
                $scope.scrollToAnchor('plan');
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
        content += '<p> &#8226; '+easyask.lang.place+': '+question.place+'</p>';
        content += '<p> &#8226; '+easyask.lang.petals+': '+question.petals+'</p>';
        content += '<p> &#8226; '+easyask.lang.where_putting+': '+question.where_putting+'</p>';
        content += '<p> &#8226; '+easyask.lang.temp+': '+question.temp+'</p>';
        content += '<p> &#8226; '+easyask.lang.sun+': '+question.sun+'</p>';
        content += '<p> &#8226; '+easyask.lang.water+': '+question.water+'</p>';
        content += '<p> &#8226; '+easyask.lang.fertilizer+': '+question.fertilizer+'</p>';
        content += '<p> &#8226; '+easyask.lang.when_bloom+': '+question.when_bloom+'</p>';
        if (question.plan) {
            content += '<p class="wrap-break-word">';
            content += easyask.lang.plan+':<br>';
            content += question.plan;
            content += '</p>';
        }
        if (question.image[0] || question.image[1] || question.image[2]) {
            content += '<p>'+easyask.lang.image+'</p>';
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

    $('input[name="place"]').keypress(function() {
        $scope.warnOnLeave = true;
    });
    $('input[name="where_putting"]').keypress(function() {
        $scope.warnOnLeave = true;
    });
    $('input[name="temp"]').keypress(function() {
        $scope.warnOnLeave = true;
    });
    $('input[name="sun"]').keypress(function() {
        $scope.warnOnLeave = true;
    });
    $('input[name="water"]').keypress(function() {
        $scope.warnOnLeave = true;
    });
    $('input[name="fertilizer"]').keypress(function() {
        $scope.warnOnLeave = true;
    });
    $('md-select').click(function() {
        $scope.warnOnLeave = true;
    });
    $('textarea[name="plan"]').keypress(function() {
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
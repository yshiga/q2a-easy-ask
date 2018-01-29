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
                console.log($scope.question);
                $scope.warnOnLeave = false;
                var content = getContent($scope.question);

                console.log(content);
                var params = {};
                var comment = $scope.question.comment;
                if (comment) {
                    comment = comment.replace(/\r?\n/g,"");
                }
                var title = easyask.lang.title+' '+comment;
                params.title = title.substr(0, 50);
                params.content = content;
                params.category_id = 38;
                params.code = easyask.code;
                console.log(params);
                return;
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
            if ($scope.questionForm.inspect_date.$error.required) {
                $scope.scrollToAnchor('inspect_date');
                return;
            }
            if ($scope.questionForm.inspect_time.$error.required) {
                $scope.scrollToAnchor('inspect_time');
                return;
            }
            if ($scope.questionForm.temp_weather.$error.required) {
                $scope.scrollToAnchor('temp_weather');
                return;
            }
            if ($scope.questionForm.when_breed.$error.required
             || $scope.questionForm.when_breed.$error.minlength
             || $scope.questionForm.when_breed.$error['md-maxlength']) {
                $scope.scrollToAnchor('when_breed');
                return;
            }
            if ($scope.questionForm.enter_exit.$error.required) {
                $scope.scrollToAnchor('enter_exit');
                return;
            }
            if ($scope.questionForm.pollen.$error.required) {
                $scope.scrollToAnchor('pollen');
                return;
            }
            if ($scope.questionForm.hive_size.$error.required) {
                $scope.scrollToAnchor('hive_size');
                return;
            }
            if ($scope.questionForm.growing.$error.required) {
                $scope.scrollToAnchor('growing');
                return;
            }
            if ($scope.questionForm.scrap.$error.required) {
                $scope.scrollToAnchor('scrap');
                return;
            }
            if ($scope.questionForm.sumushi.$error.required) {
                $scope.scrollToAnchor('sumushi');
                return;
            }
            if ($scope.questionForm.discard.$error.required) {
                $scope.scrollToAnchor('discard');
                return;
            }
            if ($scope.questionForm.drone.$error.required) {
                $scope.scrollToAnchor('drone');
                return;
            }
            if ($scope.questionForm.overflow.$error.required) {
                $scope.scrollToAnchor('overflow');
                return;
            }
            if ($scope.questionForm.wander.$error.required) {
                $scope.scrollToAnchor('wander');
                return;
            }
            if ($scope.questionForm.menthol.$error.required) {
                $scope.scrollToAnchor('menthol');
                return;
            }
            if ($scope.questionForm.collect.$error.required) {
                $scope.scrollToAnchor('collect');
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

    $scope.uploadInnerFile = function(file, errFiles) {
        $scope.f = file;
        $scope.errInnerFile = errFiles && errFiles[0];
        $scope.progressInner = false;
        $scope.question.inner_image = null;
        if (file) {
            file.upload = Upload.upload({
                url: '/easy-ask-file-upload',
                data: {file: file}
            });

            file.upload.then(function (response) {
                $timeout(function() {
                    var res = response.data.files[0];
                    if (res.name == 'error') {
                        $scope.uploadInnerError = res.error;
                    } else {
                        $scope.question.inner_image = res.url;
                        $scope.warnOnLeave = true;
                    }
                    $scope.progressInner = false;
                });
            }, function (response) {
                if (response.status > 0) {
                    $scope.uploadInnerError = response.status + ': ' + response.data.files[0].error;
                    $scope.question.inner_image = null;
                }
                $scope.progressInner = false;
            }, function (evt) {
                $scope.progressInner = true;
            });
        }
    }

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
        var inspect_date = formatDate($scope.question.inspect_date, 'M月d日');
        content += '<p>'+easyask.lang.content_head+'</p>'
        content += '<p> &#8226; '+easyask.lang.inspect_date+': '+inspect_date+'</p>';
        content += '<p> &#8226; '+easyask.lang.inspect_time+': '+question.inspect_time+'</p>';
        content += '<p> &#8226; '+easyask.lang.temp_weather+': '+question.temp_weather+'</p>';
        content += '<p> &#8226; '+easyask.lang.when_breed+': '+question.when_breed+'</p>';
        content += '<p> &#8226; '+easyask.lang.enter_exit+': '+question.enter_exit+'</p>';
        content += '<p> &#8226; '+easyask.lang.pollen+': '+question.pollen+'</p>';
        content += '<p> &#8226; '+easyask.lang.hive_size+': '+question.hive_size+'</p>';
        content += '<p> &#8226; '+easyask.lang.growing+': '+question.growing+'</p>';
        content += '<p> &#8226; '+easyask.lang.scrap+': '+question.scrap+'</p>';
        content += '<p> &#8226; '+easyask.lang.sumushi+': '+question.sumushi+'</p>';
        content += '<p> &#8226; '+easyask.lang.discard+': '+question.discard+'</p>';
        content += '<p> &#8226; '+easyask.lang.drone+': '+question.drone+'</p>';
        content += '<p> &#8226; '+easyask.lang.overflow+': '+question.overflow+'</p>';
        content += '<p> &#8226; '+easyask.lang.wander+': '+question.wander+'</p>';
        content += '<p> &#8226; '+easyask.lang.menthol+': '+question.menthol+'</p>';
        content += '<p> &#8226; '+easyask.lang.collect+': '+question.collect+'</p>';
        if (question.comment) {
            content += '<p style="word-wrap: break-word">';
            content += easyask.lang.comment+':<br>';
            content += question.comment;
            content += '</p>';
        }
        if (question.inner_image) {
            content += '<p>'+easyask.lang.inner_image+'</p>';
            content += '<div class="medium-insert-images">';
            content += '<div class="image-url">[image="'+question.inner_image+'"]</div>';
            content += '</div>';
        }
        if (question.image[0] || question.image[1]) {
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
<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    public function head_css()
    {
        qa_html_theme_base::head_css();
        if ($this->template==='easy-ask') {
            $styles =<<<EOS
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic">
EOS;
            $this->output($styles);
            $css = '';
            $this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.QA_HTML_THEME_LAYER_URLTOROOT.'css/easy-ask.css"/>');
            $this->output($css);
        }
    }

    public function body_footer()
    {
        $url = QA_HTML_THEME_LAYER_URLTOROOT;
        qa_html_theme_base::body_footer();
        if ($this->template === 'easy-ask') {
            $scripts =<<<EOS
    <!-- Angular Material requires Angular.js Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-animate.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-aria.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-messages.min.js"></script>

    <!-- Angular Material Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.js"></script>

    <script src="{$url}js/ng-file-upload-shim.min.js"></script>
    <script src="{$url}js/ng-file-upload.min.js"></script>

    <!-- Your application bootstrap  -->
    <script type="text/javascript">
        /**
         * You must include the dependency on 'ngMaterial' 
         */
        angular.module('myApp', ['ngMaterial', 'ngMessages', 'ngFileUpload'])
        .config(function (\$mdThemingProvider) {
            \$mdThemingProvider.theme('default')
            .primaryPalette('deep-orange')
            .accentPalette('orange');
        })
        .controller('AppCtrl', function(\$scope, Upload, \$timeout) {
            \$scope.question = {};
            \$scope.uploadfiles = null;
            \$scope.postQuestion = function() {
                console.log('Post!');
                console.log(\$scope.question);
            };
            \$scope.uploadFiles = function(file, errFiles) {
                \$scope.f = file;
                \$scope.errFile = errFiles && errFiles[0];
                \$scope.progress = false;
                \$scope.question.image = null;
                if (file) {
                file.upload = Upload.upload({
                    url: 'https://yaimapp.38qa.net/file-upload',
                    data: {file: file}
                });

                file.upload.then(function (response) {
                    \$timeout(function() {
                    file.result = response.data;
                    console.log(response.data.files[0]);
                    \$scope.question.image = response.data.files[0].url;
                    \$scope.progress = false;
                    });
                }, function (response) {
                    if (response.status > 0) {
                    \$scope.errorMsg = response.status + ': ' + response.data;
                    \$scope.question.image = null;
                    \$scope.progress = false;
                    }
                }, function (evt) {
                    \$scope.progress = true;
                });
                }
            }
        });
    </script>
EOS;
            $this->output($scripts);
        }
    }

    public function main_parts($content)
    {
        if($this->template === 'easy-ask') {
            $form_id = qa_request_part(1);
            $form_template = QEA_DIR.'/html/form_'.$form_id.'.html';
            if (file_exists($form_template)) {
                $tmpl = file_get_contents($form_template);
                $url = QA_HTML_THEME_LAYER_URLTOROOT;
                $html = strtr($tmpl, array(
                    '^url' => $url
                ));
                $this->output($html);
            } else {
                $this->output( 'form not found!!!' );
            }
        } else {
            qa_html_theme_base::main_parts($content);
        }
    }
}

<?php

class easy_ask_post_question
{
    private $code;
    private $output;

    function match_request($request)
    {
        return ($request === 'easy-ask-post-question');
    }

    function process_request($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (strtolower($method) === 'post') {
            $input_params = $this->get_input_params($method);

            if (!qa_check_form_security_code('easy-ask', $input_params['code'])) {
                $this->set_errors(401, 'Unauthorized', 'Security Code invalid');
            } else {
                $this->post_question($input_params);
            }
        } else {
            $this->set_errors(400, 'Bad Request', 'The Request URI not Match the API');
        }

        return $this->response();
    }

    /**
     * レスポンスを返す
     */
    private function response()
    {
        header ( 'Content-Type: application/json' );
        http_response_code($this->code);
        echo json_encode($this->output, JSON_PRETTY_PRINT);
    }

    /**
     * 入力パラメータ取得
     */
    private function get_input_params($method)
    {
        $input_params = null;
        if (strtolower($method) === 'post') {
            $inputJSON = file_get_contents('php://input');
            $input_params = json_decode($inputJSON, TRUE);
        }
        return $input_params;
    }
    
    /**
     * レスポンスのための値をセットする
     */
    private function set_response($code, $output)
    {
        $this->code = $code;
        $this->output = $output;
    }
    
    /**
     * エラーをセットする
     */
    private function set_errors($code, $message, $detail)
    {
        $this->code = $code;
        $this->output = array(
            'error' => array(
                'code' => $code,
                'message' => $message,
                'detail' => $detail
            ),
        );
    }

    /**
     * 質問投稿
     */
    private function post_question($input)
    {
        $in = $this->set_create_data($input);
        if (!$this->check_errors_for_post_question($in)) {
            return false;
        }

        try {
            $output = $this->create_question($in);
            $this->set_response(200, $output);
        } catch (Exception $e) {
            $errorstring = $e->getMessage();
            $this->set_errors(500, '', 'An Error Occured: '.$errorstring);
            return false;
        }
        return true;
    }

    /**
     * 投稿用のデータを作成する
     */
    private function set_create_data($input)
    {
        $in = array();
        $in['categoryid'] = $input['category_id'];
        $in['title'] = $input['title'];
        $in['content'] = $input['content'];

        $in['format'] = 'html';
        $in['tags'] = null;
        $in['extra'] = null;
        $in['notify'] = null;
        $in['name'] = null;
        $in['email'] = null;
        $in['queued'] = null;

        $this->get_post_content($in['content'], $in['text'], $in['format']);

        return $in;
    }

    /**
     * 本文を整形
     */
    private function get_post_content(&$incontent, &$intext, $informat)
    {
        require_once QA_INCLUDE_DIR.'util/string.php';

        $incontent = qa_remove_utf8mb4($incontent);
        $intext = qa_remove_utf8mb4(qa_viewer_text($incontent, $informat));
    }

    /**
     * 投稿データのエラーチェック
     */
    private function check_errors_for_post_question($in)
    {
        $errors = $this->filter_data($in, 'create');
        if (!empty($errors)) {
            $errorstring = $this->convert_errors($errors);
            $this->set_errors(400, 'Bad Request', $errorstring);
            return false;
        }
        return true;
    }

    /**
     * データのフィルター
     */
    private function filter_data($in, $type)
    {
        $errors = array();
        $post = $type == 'create' ? null : $this->question;
        $filtermodues = qa_load_modules_with('filter', 'filter_question');
        foreach ($filtermodues as $filtermodule) {
            $oldin = $in;
            $filtermodule->filter_question($in, $errors, $post);
            qa_update_post_text($in, $oldin);
        }
        return $errors;
    }

    /**
     * エラーを文字列に変換
     */
    private function convert_errors($errors)
    {
        $errorstring = "";
        foreach ($errors as $key => $error) {
            if ($key === 'text') {
                continue;
            }
            $errorstring .= $key . ':' . $error . ',';
        }
        return $errorstring;
    }

    /**
     * 質問を作成する
     */
    private function create_question($in)
    {
        $userid = qa_get_logged_in_userid();
        $handles = qa_db_user_get_userid_handles(array($userid));
        $handle = $handles[$userid];
        $followanswer = null;
        $cookieid = isset($this->userid) ? qa_cookie_get() : qa_cookie_get_create();

        $questionid = qa_question_create($followanswer, $userid, $handle, $cookieid,
            $in['title'], $in['content'], $in['format'],
            $in['text'], isset($in['tags']) ? qa_tags_to_tagstring($in['tags']) : '',
            $in['notify'], $in['email'], $in['categoryid'],
            $in['extra'], $in['queued'], $in['name']);

        if(isset($questionid)) {
            $output = array(
                'posted!'
            );
        } else {
            $errorstring = 'Failed db post question';
            $this->set_errors(500, 'Internal Server Error',$errorstring);
        }
        return $output;
    }
}
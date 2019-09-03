<?php

/**
 * This class stores a post up until it is saved to the DB
 */
class testPost
{
    private $title = '';
    private $post = '';
    private $tags;
    private $answers;
    private $currentAnswer;

    private $parserContext = null;

    function __construct()
    {
        $this->title = '';
        $this->post = '';
        $this->tags = array();
        $this->answers = array();
        $this->currentAnswer = '';
        $this->currentAnswerisCorrect = false;
        $this->currentComments = array();

        $this->parserContext = 'post';
    }

    function parseLine($str)
    {
        $key = substr($str, 0, 2);
        switch ($key)
        {
            case '-T': $this->setTitle($str); return false;
            case '-K': $this->setTags($str); return false;
            case '-C': $this->addComment($str); return false;
            case '-A': $this->currentAnswerisCorrect = true; return false;

            case '--': // new answer
                $this->parserContext = 'answer';
                if (!empty($this->currentAnswer))
                    $this->answers[] = array('answer' => $this->currentAnswer,
                                             'comments' => $this->currentComments,
                                             'correct' => $this->currentAnswerisCorrect);
                $this->currentAnswer = '';
                $this->currentComments = array();
                $this->currentAnswerisCorrect = false;
                return false;

            case '-E': // End of post
                if (!empty($this->currentAnswer))
                $this->answers[] = array('answer' => $this->currentAnswer,
                                         'comments' => $this->currentComments,
                                         'correct' => $this->currentAnswerisCorrect);
                $this->currentAnswer = '';
                $this->currentComments = array();
                $this->currentAnswerisCorrect = false;
                return true;
        }

        switch ($this->parserContext)
        {
            case 'post': $this->addPostText($str); break;
            case 'answer': $this->addAnswerText($str); break;
        }

        return false;
    }

    private function setTitle($str)
    {
        $this->title = str_replace('-T ', '', trim($str));
    }

    private function addComment($str)
    {
        $this->currentComments[] = str_replace('-C ', '', trim($str));
    }

    private function setTags($str)
    {
        $this->tags = str_replace('-K ', '', trim($str));
    }

    private function addPostText($str)
    {
        $this->post .= $str;
    }

    private function addAnswerText($str)
    {
        $this->currentAnswer .= $str;
    }

    public function storeInDB()
    {
        require_once QA_INCLUDE_DIR . 'app\posts.php';

        $categoryid = null;
        $notify = null;
        $postUserId = $this->getTestUserId();

        // Set $type to 'Q' for a new question, 'A' for an answer, or 'C' for a comment.
        $postid = qa_post_create('Q', null, $this->title, $this->post, '', $categoryid, $this->tags,
                                 $postUserId,
                                 $notify, null, 'testPost');

        $this->votePost($postid, $postUserId);

        foreach ($this->answers as $anAnswer)
        {
            $answerId = qa_post_create('A', $postid, '', $anAnswer['answer'], '', null, null,
                                       $this->getTestUserId(),
                                       $notify, null, 'testPost');

            if (!empty($anAnswer['correct']))
                qa_post_set_selchildid($postid, $answerId, $postUserId);

            $this->voteAnswer($postid, $postUserId, $answerId);

            if (!empty($anAnswer['comments']))
            {
                foreach ($anAnswer['comments'] as $aComment)
                {
                    qa_post_create('C', $answerId, '', $aComment, '', null, null,
                                   $this->getTestUserId(),
                                   $notify, null, 'testPost');
                }
            }
        }
    }

    private function votePost($postId, $authorId)
    {
        require_once QA_INCLUDE_DIR . 'app\votes.php';

        $post = array('postid' => $postId, 'basetype' => 'Q', 'userid' => $authorId);

        for ($i = 0; $i < rand(0, 5); $i++)
        {
            $userid = $this->getTestUserId();
            qa_vote_set($post, $userid, qa_userid_to_handle($userid), qa_cookie_get(), 1);
        }
    }

    private function voteAnswer($postId, $authorId, $answerId)
    {
        require_once QA_INCLUDE_DIR . 'app\votes.php';

        $post = array('postid' => $answerId, 'basetype' => 'A', 'userid' => $authorId, 'parentid' => $postId);

        for ($i = 0; $i < rand(0, 5); $i++)
        {
            $userid = $this->getTestUserId();
            qa_vote_set($post, $userid, qa_userid_to_handle($userid), qa_cookie_get(), 1);
        }
    }

    private function getTestUserId()
    {
        if (!isset($GLOBALS['TestUserIds']))
        {
            require_once QA_INCLUDE_DIR . 'db/users.php';

            $GLOBALS['TestUserIds'] = qa_db_read_all_values(qa_db_query_sub(
                'SELECT userid FROM ^usermetas WHERE title=$ AND content=$',
                'test', 'yes'
            ));
        }

        return $GLOBALS['TestUserIds'][rand(0, count($GLOBALS['TestUserIds']) - 1)];
    }
}
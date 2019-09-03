<?php

class neayi_test_data
{
    function __construct() {

    }

    public function addAllTestData()
    {
        $this->addTestUsers();
        $this->addTestPosts('questions-vigne.txt');
    }

    public function removeAllTestData()
    {
        $this->removeTestUsers();
        $this->removeTestQuestions();
    }

    public function addTestUsers($nbOfUsers = 50)
    {
        require_once QA_INCLUDE_DIR . 'app/users-edit.php';
		require_once QA_INCLUDE_DIR . 'db/users.php';

        $users = $this->createTestUsers($nbOfUsers);

        $ip = bin2hex(@inet_pton(qa_remote_ip_address()));

        // RrRqCp4cMtFy2u8
        $passHash = '$2y$10$e3xwVvCLtB4mKKKpJLTDSupYZ5wHt3YsklgBPR4zqwr.3NiOoRNEe';

        foreach ($users as $user)
        {
            $y = rand(2018, 2019);
            $m = '0' . rand(1, 9);
            $d = rand(10, 27);

            $date = "$y-$m-$d  11:51:49";
            $sql = 'INSERT INTO ^users (created, createip, email, handle,
                                        passhash, loggedin, loginip,
                                        sessioncode, flags)
                    VALUES ($, UNHEX($), $, $,
                            $, $, UNHEX($),
                            $, #)';

            qa_db_query_sub($sql,
            $date, $ip, $user['email'], $user['username'], $passHash, $date, $ip, qa_db_user_rand_sessioncode(), $user['flags']);

            $userid = qa_db_last_insert_id();
            if (!empty($user['picture']))
                qa_set_user_avatar($userid, file_get_contents($user['picture']));

            $sql = 'INSERT INTO ^userprofile (userid, title, content)
                    VALUES (#, $, $)';

            qa_db_query_sub($sql, $userid, 'name', $user['firstname'] . ' ' . $user['givenname']);

            $sql = 'INSERT INTO ^usermetas (userid, title, content)
            VALUES (#, $, $)';

            qa_db_query_sub($sql, $userid, 'test', 'yes');

            $sql = 'INSERT INTO ^userpoints (userid, points)
            VALUES (#, #)';

            qa_db_query_sub($sql, $userid, 100);
        }
    }

    public function removeTestUsers()
    {
        require_once QA_INCLUDE_DIR . 'db/users.php';

        $ids = qa_db_read_all_values(qa_db_query_sub(
            'SELECT userid FROM ^usermetas WHERE title=$ AND content=$',
            'test', 'yes'
        ));

        foreach ($ids as $userid)
            qa_db_user_delete($userid);
    }

    public function removeTestQuestions()
    {
        require_once QA_INCLUDE_DIR . 'app/posts.php';

        $ids = qa_db_read_all_values(qa_db_query_sub(
            'SELECT postid FROM ^postmetas WHERE title=$ AND content=$',
            'qa_q_extra', 'testPost'
        ));

        foreach ($ids as $postid)
        {
            $answers = qa_post_get_question_answers($postid);
            foreach ($answers as $answer)
            {
                $commentsfollows = qa_post_get_answer_commentsfollows($answer['postid']);
                foreach ($commentsfollows as $comment)
                    qa_post_delete($comment['postid']);
                qa_post_delete($answer['postid']);
            }
            $commentsfollows = qa_post_get_question_commentsfollows($postid);
            foreach ($commentsfollows as $comment)
                qa_post_delete($comment['postid']);
            qa_post_delete($postid);
        }
    }

    private function createTestUsers($nbOfUsers)
    {
        $malePictures = glob(__DIR__ . '/test_data/photos-m/*');
        $femalePictures = glob(__DIR__ . '/test_data/photos-f/*');

        $maleFirstNames = file(__DIR__ . '/test_data/prenoms-m.txt');
        $femaleFirstNames = file(__DIR__ . '/test_data/prenoms-f.txt');

        $givenNames = file(__DIR__ . '/test_data/noms.txt');

        $males = array();

        for ($i = 0; $i < min($nbOfUsers, count($malePictures)); $i++)
            $males[] = $this->generateOneUser($maleFirstNames, $givenNames, $malePictures[$i]);

        $nbOfUsers -= count($males);

        $females = array();

        for ($i = 0; $i < min($nbOfUsers, count($femalePictures)); $i++)
            $females[] = $this->generateOneUser($femaleFirstNames, $givenNames, $femalePictures[$i]);

        $nbOfUsers -= count($females);

        for ($i = 0; $i < $nbOfUsers; $i++)
            $males[] = $this->generateOneUser($maleFirstNames, $givenNames);

        return $males + $females;
    }

    private function generateOneUser($firstnames, $givennames, $picture = '')
    {
        $user = array();
        $user['firstname'] = trim($firstnames[rand(0, count($firstnames) - 1)]);
        $user['givenname'] = trim($givennames[rand(0, count($givennames) - 1)]);
        $user['username'] = $this->makeusername($user['firstname'], $user['givenname']);
        $user['email'] = $this->makeemail($user['firstname'], $user['givenname']);

        if (!empty($picture))
        {
            $user['picture'] = $picture;
            $user['flags'] = QA_USER_FLAGS_EMAIL_CONFIRMED | QA_USER_FLAGS_SHOW_AVATAR | QA_USER_FLAGS_NO_MESSAGES | QA_USER_FLAGS_NO_MAILINGS;
        }
        else
        {
            $user['picture'] = '';
            $user['flags'] = QA_USER_FLAGS_EMAIL_CONFIRMED | QA_USER_FLAGS_NO_MESSAGES | QA_USER_FLAGS_NO_MAILINGS;
        }

        return $user;
    }

    private function makeusername($firstname, $givenname)
    {
        return $firstname . ' ' . $givenname;
        return $this->removeNonAscii($firstname) . $this->removeNonAscii($givenname);
    }

    private function makeemail($firstname, $givenname)
    {
        return $this->removeNonAscii($firstname) . '.' . $this->removeNonAscii($givenname) . '@neayi.com';
    }

    private function removeNonAscii($str)
    {
        $str = iconv("UTF-8", "ASCII//TRANSLIT", $str);
        $str = strtolower($str);
        $str = preg_replace('/[^a-z]/', '', $str);
        return $str;
    }

    public function addTestPosts($filename = '')
    {
        if (empty($filename))
            $filenames = glob(__DIR__ . '/test_data/questions-*');
        else
            $filenames = array(__DIR__ . '/test_data/' . $filename);

        foreach ($filenames as $afilename)
        {
            $this->parseQuestionFile($afilename);
        }
    }

    private function parseQuestionFile($filename)
    {
        $lines = file($filename);

        require_once __DIR__ . '/qa-neayi-test-post.php';

        $post = new testPost();

        foreach ($lines as $line)
        {
            if ($post->parseLine($line))
            {
                $post->storeInDB();
                $post = new testPost();
            }
        }
    }


}
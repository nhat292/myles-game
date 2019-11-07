<?php

// Constant
define('SERVER_NAME', 'localhost');
define('USER_NAME', 'infinity_siatest');
define('PASSWORD', 'fplwes5vhk');
define('DB_NAME', 'infinity_siatest');

// Action
if ($_GET['action'] == 'login') {
    login();
} else if ($_GET['action'] == 'signup') {
    signUp();
} else if ($_GET['action'] == 'update-score') {
    updateScore();
} else if ($_GET['action'] == 'get-leader-board') {
    getLeaderBoard();
} else if ($_GET['action'] == 'save-file') {
    saveFile();
} else if ($_GET['action'] == 'ranking') {
    getRanking();
} else if ($_GET['action'] == 'get-share-score') {
    getShareScore();
}

function login()
{
    if ($_POST) {
        // Create connection
        $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $email = '';
        $password = '';
        $fb_id = '';
        $first_name = '';
        $last_name = '';
        $score = 0;

        if (isset($_POST['email'])) {
            $email = $_POST['email'];
        }
        if (isset($_POST['password'])) {
            $password = md5($_POST['password']);
        }
        if (isset($_POST['fb_id'])) {
            $fb_id = $_POST['fb_id'];
        }

        if (isset($_POST['first_name'])) {
            $first_name = $_POST['first_name'];
        }

        if (isset($_POST['last_name'])) {
            $last_name = $_POST['last_name'];
        }

        if (isset($_POST['score'])) {
            $score = $_POST['score'];
        }

        if ($fb_id != '') {
            $sql = "SELECT * FROM users WHERE fb_id = '" . $fb_id . "'";
        } else {
            $sql = "SELECT * FROM users WHERE email = '" . $email . "' AND password = '" . $password . "'";
        }
        $results = $conn->query($sql);
        if ($results->num_rows > 0) {
            while ($row = $results->fetch_assoc()) {
                $id = $row['id'];
                $s = $row['score'];
                if ($row['score'] < $score) {
                    $s = $score;
                    $now = date('Y-m-d H:i:s');
                    $sql = "UPDATE users SET updated_at = '" . $now . "', score = '" . $score . "' WHERE id = '" . $id . "'";

                    $conn->query($sql);
                }
                echo $row['id'] . '_@_' . $row['fb_id'] . '_@_' . $row['email'] . '_@_' . $row['first_name'] . ' ' . $row['last_name'] . '_@_' . $s;
                break;
            }
        } else {
            if ($fb_id != '') {
                $sql = "INSERT INTO users (
                    fb_id,email,first_name,last_name,score
                )
                VALUES (
                    '$fb_id',
                    '$email',
                    '$first_name',
                    '$last_name',
                    '$score'
                )";
                if ($conn->query($sql) === TRUE) {
                    $sql = "SELECT * FROM users WHERE fb_id = '" . $fb_id . "'";
                    $results = $conn->query($sql);
                    if ($results->num_rows > 0) {
                        while ($row = $results->fetch_assoc()) {
                            echo $row['id'] . '_@_' . $row['fb_id'] . '_@_' . $row['email'] . '_@_' . $row['first_name'] . ' ' . $row['last_name'] . '_@_' . $row['score'];
                            break;
                        }
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }
        }
        $conn->close();
    }
}

function signUp()
{
    if ($_POST) {
        // Create connection
        $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $email = $_POST['email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $password = md5($_POST['password']);
        $score = $_POST['score'];
        $sql = "SELECT * FROM users WHERE email = '" . $email . "'";
        $results = $conn->query($sql);
        if ($results->num_rows > 0) {
            echo 1;
        } else {
            if ($first_name == '' || $last_name == '') {
                echo 1;
            } else {
                $sql = "INSERT INTO users (
                    email,first_name,last_name,password,score
                )
                VALUES (
                    '$email',
                    '$first_name',
                    '$last_name',
                    '$password',
                    '$score'
                )";
                if ($conn->query($sql) === TRUE) {
                    $sql = "SELECT * FROM users WHERE email = '" . $email . "'";
                    $results = $conn->query($sql);
                    if ($results->num_rows > 0) {
                        while ($row = $results->fetch_assoc()) {
                            echo $row['id'] . '_@_' . $row['fb_id'] . '_@_' . $row['email'] . '_@_' . $row['first_name'] . ' ' . $row['last_name'] . '_@_' . $row['score'];
                            break;
                        }
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            }
        }
        $conn->close();
    }
}

function updateScore()
{
    if ($_POST) {
        // Create connection
        $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $id = $_POST['id'];
        $score = $_POST['score'];

        $now = date('Y-m-d H:i:s');

        $sql = "UPDATE users SET updated_at = '" . $now . "', score = '" . $score . "' WHERE id = '" . $id . "' AND score < '" . $score . "'";

        if ($conn->query($sql) === TRUE) {
            echo 1;
        } else {
            echo 0;
        }
        $conn->close();
    }
}

function getLeaderBoard()
{
    if ($_POST) {
        // Create connection
        $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * 
                FROM users
                ORDER BY score DESC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $s = '';
            while ($row = $result->fetch_assoc()) {
                $s = $s . '_@@_' . $row['first_name'] . ' ' . $row['last_name'] . '_@_' . $row['score'] . '_@_' . $row['id'];
            }
            echo $s;
        } else {
            echo 0;
        }

        $conn->close();
    }
}

function updateLikes()
{
    if ($_POST) {
        // Create connection
        $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $post_id = $_POST['post_id'];
        $like_count = $_POST['like_count'];
        $sql = "UPDATE posts SET like_count='$like_count' WHERE post_id='$post_id'";
        if ($conn->query($sql) === TRUE) {
            echo "Record update successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
}

function getRanking()
{
    if ($_POST) {
        // Create connection
        $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $id = $_POST['id'];
        $query = "SELECT rank FROM (SELECT @rownum := @rownum + 1 AS rank FROM users ORDER BY score DESC) as rank WHERE id=$id";
        $result = $conn->query($query);
        $json = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $json[] = $row;
            }
        }
        echo json_encode($json);

        $conn->close();
    }
}

function saveFile()
{
    if ($_POST) {
        $img = $_POST['img'];
        $fileName = round(microtime(true) * 1000) . '.png';
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $filePath = '../screenshot/' . $fileName;
        file_put_contents($filePath, $data);
        // $letterLogoFile = 'screenshot/logo_weiken_letter.png';
        // $circleLogoFile = 'screenshot/logo_weiken_circle.png';
        // $milliseconds = round(microtime(true) * 1000);
        // $outputFileName = 'share_'.$fileName;
        // $shareFile = 'screenshot/'.$outputFileName;
        // merge($filePath, $letterLogoFile, $circleLogoFile, $shareFile);
        echo $fileName;
    }
}


function getShareScore()
{
    if ($_POST) {
        $score = number_format($_POST['score']);
        $filePath = 'share_background.png';
        $numbers = str_split($score);
        $fileName = round(microtime(true) * 1000) . '.png';
        $outputFileName = 'share_' . $fileName;
        $shareFile = '../screenshot/' . $outputFileName;
        merge($filePath, $numbers, $shareFile);
        echo $outputFileName;
    }
}

function merge($filename_x, $numbers, $filename_result)
{
    list($width_x, $height_x) = getimagesize($filename_x);
    $image = imagecreatetruecolor($width_x, $height_x);
    $image_x = imagecreatefrompng($filename_x);
    imagecopy($image, $image_x, 0, 0, 0, 0, $width_x, $height_x);
    $is_comma_before = false;
    $countOfNumbers = count($numbers);
    $current_x = 600 - ($countOfNumbers * 50);
    foreach ($numbers as $key => $value) {
        $filename = $value . '.png';
        list($width, $height) = getimagesize($filename);
        $image_value = imagecreatefrompng($filename);
        $is_comma = $value === ',';
        if ($key !== 0) {
            $current_x += $is_comma_before ? 60 : 105;
        }
        imagecopy($image, $image_value, $is_comma ? $current_x - 5 : $current_x, $is_comma ? 560 : 500, 0, 0, $width, $height);
        if ($is_comma_before === true) {
            $is_comma_before = false;
        }
        $is_comma_before = $is_comma;
    }
    imagejpeg($image, $filename_result);
    imagedestroy($image);
}

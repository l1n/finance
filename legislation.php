<?php
include_once('session.php');
global $dbh;
if (!isset($_SESSION['HTTP_DISPLAYNAME'])) { exit(0); }
// Param validation

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'display';
}

if ($_REQUEST['action'] === 'submit') {
    if (empty($_REQUEST['number'])) {
        $nextnumber = $dbh->prepare('SELECT MAX(number) FROM legislation WHERE code = ? AND session = ?');
        $nextnumber->execute([$_REQUEST['code'], $_REQUEST['session']]);
        $nextnumber = $nextnumber->fetch();
        if (isset($nextnumber[0])) {
            $_REQUEST['number'] = $nextnumber[0] + 1;
        } else {
            $_REQUEST['number'] = 1;
        }
    }
    try {
        $legislation_id = $_REQUEST['legislation_id'];
        if (empty($legislation_id)) {
            $legislation_id = uniqid();
            $request = $dbh->prepare('UPDATE request SET legislation_id = ? WHERE id = ?');
            $request->execute([$legislation_id, $_REQUEST['request_id']]);
        }
        $id_match = $dbh->prepare('SELECT id FROM legislation WHERE number = ? AND session = ? AND code = ?');
        $id_match->execute([$_REQUEST['number'], $_REQUEST['session'], $_REQUEST['code']]);
        $_REQUEST['id'] = $id_match->fetch()['id'];
        $legislation = $dbh->prepare('INSERT OR REPLACE INTO legislation (id, number, code, session, title, author, sponsor, introduced, body, draft) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $legislation->execute([$legislation_id, $_REQUEST['number'], $_REQUEST['code'], $_REQUEST['session'], $_REQUEST['title'], $_REQUEST['author'], $_SESSION['HTTP_DISPLAYNAME'], $_REQUEST['introduced'], $_REQUEST['body'], $_REQUEST['draft']]);
    } catch(PDOException $e) {echo $e->getMessage();}
}
if (isset($_REQUEST['number']) && isset($_REQUEST['session']) && isset($_REQUEST['code'])) {
    $id_match = $dbh->prepare('SELECT id FROM legislation WHERE number = ? AND session = ? AND code = ?');
    $id_match->execute([$_REQUEST['number'], $_REQUEST['session'], $_REQUEST['code']]);
    $_REQUEST['id'] = $id_match->fetch()['id'];
}
if (isset($_REQUEST['id'])) {
    $legislation = $dbh->prepare('SELECT number, code, session, title, author, sponsor, introduced, body, draft FROM legislation WHERE id = ?');
    $legislation->execute([$_REQUEST['id']]);
    $legislation = $legislation->fetch();
    header("Content-type: application/pdf");
    $fields = array(
        'draftwatermark'=>$legislation['draft'],
        'paragraphnumbers'=>TRUE,
        'linenumbers'=>FALSE,
        'author'=>$legislation['author'],
        'title'=>$legislation['title'],
        'legislationNumber'=>$legislation['number'],
        'legislativeSession'=>$legislation['session'],
        'legislationType'=>$legislation['code'],
        'sponsor'=>$legislation['sponsor'],
        'introduced'=>$legislation['introduced'],
        'body'=>$legislation['body'],
        'submit'=>'Generate PDF',
    );

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL,'https://umbc.in/write-legislation');
    curl_setopt($ch,CURLOPT_POST,count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);

    //execute post
    $result = curl_exec($ch);
    print $result;
}

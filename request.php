<?php
include_once('session.php');
global $dbh;
$searchAutoFill = "";
# CREATE TABLE organizations_canonical (id INTEGER PRIMARY KEY ASC, name TEXT, category TEXT, description TEXT, organization_group TEXT, website TEXT, facebook TEXT, twitter TEXT, email TEXT, mailbox TEXT, cabinet TEXT);
# CREATE TABLE officers (organization_id INTEGER, name TEXT, position TEXT, email TEXT);
if (isset($_REQUEST['id'])) {
    $organization = $dbh->prepare('SELECT id, name, category, description, organization_group, website, facebook, twitter, email, mailbox, cabinet FROM organizations_canonical WHERE id = ?');
    $organization->execute([$_REQUEST['id']]);
    $organization = $organization->fetch();
        if ($organization['id'] == 69 && empty($_SESSION['position'])) {
            $TITLE = 'Allocation Request Form';
            $TITLE_LINK = '';
        } else {
            $TITLE = $organization['name'];
            $TITLE_LINK = '<a href="?id='.$_REQUEST['id'].'">'.$organization['name'].'</a>';
        }
} else {
    $organization = $dbh->prepare('SELECT id, name, category, description, organization_group FROM organizations_canonical ORDER BY name');
    $organization->execute();
    $TITLE = "Student Organizations at UMBC";
}
?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
  <title><?=$TITLE?></title>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/<?=$materializeVersion?>/css/materialize.min.css" />
    <link rel="stylesheet" href="/css/homepage.css" />
    <link rel="stylesheet" href="/css/site.css" />
    <link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/materialize/<?=$materializeVersion?>/js/materialize.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <!-- Some custom styles just for older IE versions -->
    <!--[if lte IE 8]>
    <style>
      #container { border: 1px solid #ddd; border-top: none; }
      #site-footer .footer-field { float: left; }
      #site-menu ul li a { border-left: none; }
      #site-menu > ul > li > a .menu-arrow { display: none; }
      .widget-spotlight .playlist li { float: left; display: block; overflow: hidden; }
    </style>
    <![endif]-->

    <!--[if IE]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
<script type="text/javascript">
// Code adapted from http://stackoverflow.com/a/32664363
function search() {
    var input, filter, found, tables, tr, th, i, j, filterByLabel, exin;
    var qs = {
        'all': 'div.organization-panel',
            'org': 'div.collapsible-header .request-org',
            'name': 'div.collapsible-header .request-name'
    };
    var query = qs.all;
    input = document.getElementById("umbc-nav-search-query");
    filter = input.value.toUpperCase();
    tables = document.querySelectorAll(query);
    Array.from(tables).forEach(function (org) {
        if (org.innerText.toUpperCase().indexOf(filter) > -1) {
            found = true;
        }
        if (found) {
            org.classList.remove('hide');
            found = false;
        } else {
            org.classList.add('hide');
        }
    });
}
function resetInput(event) {
    document.location.replace(document.querySelector('div:not([class*="hide"]).organization-panel a').href);
    search();
    var input = document.getElementById("umbc-nav-search-query");
    filter = input.value.toUpperCase();
    input.value = '';
    return false;
}
window.onload = function () {
    $('.modal').modal();
    $('.datepicker').pickadate({
    selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year,
        today: 'Today',
        clear: 'Clear',
        close: 'Ok',
        closeOnSelect: true // Close upon selecting a date,
    });
    $('select').material_select();
    $('#add-item').click(function () {
        $('#line-item-template').append("<div>"+$('#line-item-template').html()+'<a class="delete-item">Delete Line</a>'+"</div>");
        $('.delete-item').click(function () {
            this.parentElement.remove();
        });
        $('select').material_select();
    });
};
window.onpopstate = window.onload;
</script>
    <style type="text/css">
      body {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
      }

      main {
        flex: 1 0 auto;
      }
    </style>
  </head>
  <body id="home">
<?php include('header.php') ?>
<section class="page-container layout-home">
<section class="page-content">
      <div id="content" class="row container">
        <div class="card">
            <div class="card-content">
                <h1 class="card-title"><?=isset($TITLE_LINK)?$TITLE_LINK:$TITLE?></h1>
<?php if (isset($_REQUEST['id'])) {
    if (isset($_SESSION['HTTP_DISPLAYNAME'])) {
        $officers = $dbh->prepare('SELECT name, position, email FROM officers WHERE organization_id = ? AND (officers.email = ? OR officers.email = ? OR officers.email = ?)');
        $officers->execute([$organization['id'], $_SESSION['HTTP_MAIL'], $_SESSION['HTTP_EPPN'], $_SESSION["HTTP_UMBCUSERNAME"]."@umbc.edu"]);
        $officers = $officers->fetch();
        if (!empty($officers['position'])) {
            $_SESSION['position'] = $officers['position'];
        } else {
            $_SESSION['position'] = '';
        }
    }
$officers = $dbh->prepare('SELECT name, position, email FROM officers WHERE organization_id = ? ORDER BY position');
if (isset($_REQUEST['admin'])) {
    if (!isset($_SESSION['HTTP_DISPLAYNAME'])) { ?>
This functionality requires login. Please <a href="login">log in with your myUMBC account</a>.
<?php
    } else {
    if (!isset($_REQUEST['action'])) {
?>
<form action="?id=<?=$organization['id']?>&amp;admin" method="post">
<h5>Allocation Request Form</h5>
<p>Refer to the Budgetary Policy of SGA at the <a href="http://sga.umbc.edu/about/guiding-documents/">Guiding Documents</a>.</p>
        <div class="row">
        <div class="input-field col s3">
          <input type="text" class="datepicker" name="arf-date" id="arf-date" placeholder="August 9th, 2017" />
          <label for="arf-date">Date for Expenditure</label>
        </div>
        <div class="input-field col s6">
        <select value="<?=$organization['name']?>" name="organization" id="organization" />
<?php
    $os = $dbh->prepare('SELECT id, organizations_canonical.name AS name, category FROM organizations_canonical JOIN officers ON organizations_canonical.id = officers.organization_id WHERE (officers.email = ? OR officers.email = ? OR officers.email = ?)');
    $os->execute([$_SESSION['HTTP_MAIL'], $_SESSION['HTTP_EPPN'], $_SESSION["HTTP_UMBCUSERNAME"]."@umbc.edu"]);
    $prev_category = "";
    $first = TRUE;
    $default = "";
    while ($orow = $os->fetch()) {
if ($orow['id'] === $_REQUEST['id']) {
    $default = ' selected="selected"';
} else {
    $default = "";
}
if ($prev_category !== $orow['category']) {
if ($first) {
    $first = FALSE;
} else {
    echo "</optgroup>";
}
    echo '<optgroup label="'.$orow['category'].'">';
    $prev_category = $orow['category'];
}
?>
    <option data-id="<?=$orow['id']?>" value="<?=$orow['name']?>"<?=$default?>><?=$orow['name']?></option>
<?php } ?>
</optgroup>
</select>
          <label for="organization">Organizations</label>
        </div>
        <div class="input-field col s3">
          <input type="text" name="25live" id="25live" placeholder="2017-ASDFGH" />
          <label for="25live">25Live Confirmation</label>
        </div>
        </div>
        <div class="row">
        <div class="input-field col s6">
        <input value="<?=$_SESSION['HTTP_MAIL']?>" name="contact-email" id="contact-email" type="email" class="validate" />
          <label for="contact-email">Email</label>
        </div>
        <div class="input-field col s6">
        <input value="<?=isset($_SESSION['phoneNumber'])?$_SESSION['phoneNumber']:""?>" name="contact-phone" id="contact-phone" type="tel" class="validate" />
          <label for="contact-phone">Telephone</label>
        </div>
</div>
        <div class="row">
        <div class="input-field col s6">
        <input value="<?=$_SESSION['HTTP_DISPLAYNAME']?>" name="contact-name" id="contact-name" type="text" class="validate" />
          <label for="contact-name">Requestor Name</label>
        </div>
        <div class="input-field col s6">
        <input placeholder="Event Name" name="event-name" id="event-name" type="text" class="validate" />
          <label for="event-name">Event Name</label>
        </div>
</div>
<h5>Line Items</h5>
<div id="line-item-template">
<div class="row">
        <div class="input-field col s6">
        <input name="line-description[]" id="line-description" type="text" class="value"></textarea>
          <label for="line-description">Allocation Description</label>
        </div>
        <div class="input-field col s2">
        <input name="line-cost[]" id="line-cost" type="number" min="0" step="0.01" class="validate" />
          <label for="line-cost">Cost</label>
        </div>
        <div class="input-field col s4">
     <select id="line-item" name="line-item[]">
      <option value="" disabled selected>Select...</option>
<?php
        $first = TRUE;
        if ($organization['id'] == 69 && !empty($_SESSION['position'])) {
            $line_items = explode("\n", file_get_contents('fy17.tsv'));
        } else {
            $line_items = array(
                "\tSGA Funding\t",
                "SOF\t\tStudent Organizations Fund\t250000",
                "\tOrganization Accounts\t",
                "Chartstring\t\tCarryover\t0",
            );
        }
        foreach ($line_items as $line) {
            $line = explode("\t", $line);
            if (!empty($line[1])) {
                if ($first) {
                    $first = FALSE;
?>
        </optgroup>
<?php } ?>
        <optgroup label="<?=$line[1]?>">
<?php
            } else {
                if (isset($line[3]) && !empty(trim($line[3]))) {
?>
    <option value="<?=$line[0]?>"><?=$line[2]?> - $<?=trim($line[3])?></option>
<?php
                    }}}
?>
        </optgroup>
    </select>
          <label for="line-item">Line Item</label>
        </div>
        <div class="input-field col s6">
        <input name="payee-name[]" id="payee-name" type="text" class="validate" />
          <label for="payee-name">Payee Name</label>
        </div>
        <div class="input-field col s6">
        <input name="payee-address[]" id="payee-address" type="text" class="value"></textarea>
          <label for="payee-address">Address</label>
        </div>
        <div class="input-field col s12">
        <input name="payee-contact[]" id="payee-contact" type="text" class="validate" />
          <label for="payee-contact">Contact Information</label>
        </div>
</div>
</div><a id="add-item">Add Line</a>
<div class="card-action">
  <button class="btn waves-effect waves-light" type="submit" name="action" value="submit-arf">Submit
    <i class="material-icons right">send</i>
  </button>
</div>
</form>
<?php                } elseif ($_REQUEST['action'] === "submit-arf") {
ob_start();?>
<h5><?=$_REQUEST['event-name']?></h5>
<div class="row">
<div class="col s4"><strong>Submission Date:</strong> <?=date(DATE_RFC2822)?></div>
<div class="col s4"><strong>Date for Event/Expenditure:</strong> <?=$_REQUEST['arf-date']?></div>
<?php if (!empty($_REQUEST['25live'])) {
$_REQUEST['25live'] = trim($_REQUEST['25live']); ?>
<div class="col s4"><strong>25Live Event:</strong> <a id="25live-trigger" href="//umbc.in/api/25live/<?=$_REQUEST['25live']?>&display=html" class="modal-trigger"><?=$_REQUEST['25live']?></a></div>
<div id="25live" class="modal"><div class="modal-content"></div><script type="text/javascript">fetch("//umbc.in/api/25live/<?=$_REQUEST['25live']?>&display=html").then(function (data) {return data.text();}).then(function (text) {
    document.getElementById('25live-trigger').href = '#25live';
    document.getElementById('25live').firstChild.innerHTML = text;
    $('.modal').modal();
});</script></div><?php } ?>
<div class="col s6"><strong>Organization:</strong> <?=$_REQUEST['organization']?></div>
<div class="col s6"><strong>Submitter:</strong> <a href="tel:<?=$_REQUEST['contact-phone']?>"><?=$_REQUEST['contact-name']?></a> (<a href="mailto:<?=$_REQUEST['contact-email']?>"><?=$_REQUEST['contact-email']?></a>)</div>
</div>
  <ul class="collection">
<?php $total = 0; for ($i = 0; $i < count($_REQUEST['line-description']); $i++) { ?>
    <li class="collection-item avatar">
<i class="material-icons circle red">play_arrow</i>
<span class="title"><?=$_REQUEST['line-description'][$i]?> - $<?=$_REQUEST['line-cost'][$i]?> from <?=$_REQUEST['line-item'][$i]?></span>
<p><strong><?=$_REQUEST['payee-name'][$i]?></strong><?=!empty($_REQUEST['payee-contact'][$i])?" (".$_REQUEST['payee-contact'][$i].")":""?><br>
<?=str_replace("\n", "<br>", $_REQUEST['payee-address'][$i])?>
      </p>
    </li>
<?php $total += $_REQUEST['line-cost'][$i];
} ?>
</ul>
<p>Total: $<?=$total?></p>
<ul class="signatories-{{ogc}}">
<!-- sign line for {{ogc}} --><li><?=$_SESSION['HTTP_DISPLAYNAME']?><?=isset($_SESSION['position'])?", ".$_SESSION['position']:""?>
</ul>
<?php
    $ogc = ob_get_contents();
    ob_end_clean();
    $id = md5($ogc);
    $ogc = str_replace('{{ogc}}', $id, $ogc);
    try{
        $insert_arf = $dbh->prepare('INSERT INTO arf (id, organization_id, text, status) VALUES (?, ?, ?, ?)');
        if (($_SESSION['position'] === 'President' || $_SESSION['position'] === 'Treasurer' || $_SESSION['position'] === 'Station Manager' || $_SESSIOn['position'] === 'Financial Manager') && $total < 150) {
            $status = 'Organization Approval';
        } else {
            $status = 'Submitted';
        }
        $insert_arf->execute([$id, $_REQUEST['id'], $ogc, $status]);
    } catch(PDOException $e) {echo $e->getMessage();}

    print $ogc;
    } elseif ($_REQUEST['action'] === "list-requests" || ($_REQUEST['action'] === "sign") || ($_REQUEST['action'] === "schedule") || ($_REQUEST['action'] === "delete") && !empty($_SESSION['position'])) {
                    if ($_REQUEST['action'] === "sign") {
                        $signatory = "<!-- sign line for ".$_REQUEST['request'].' --><li>'.$_SESSION['HTTP_DISPLAYNAME'].(isset($_SESSION['position'])?", ".$_SESSION['position']:"");
                        $contains_signature = $dbh->prepare('SELECT text AS count FROM arf WHERE id = ? AND text NOT LIKE ?');
                        $contains_signature->execute([$_REQUEST['request'], '%'.$signatory.'%']);
                        $contains_signature = $contains_signature->fetch()['count'];
                        if ($contains_signature) {
                            $replacement = str_replace($_REQUEST['request'].'">', $_REQUEST['request'].'">'."\n".$signatory, $contains_signature);
                            $ogc = $dbh->prepare('UPDATE arf SET text = ? WHERE id = ?');
                            $ogc->execute([$replacement, $_REQUEST['request']]);
                        }
                    } elseif ($_REQUEST['action'] === "schedule") {
                        try {
                            $insert_request = $dbh->prepare('INSERT INTO table1 ( column1, column2, someInt, someVarChar )
                                SELECT  arf.id, arf.organization_id, text, status
                                FROM    arf
                                WHERE   arf.id = ?;(id, organization_id, text, status) VALUES (?, ?, ?, ?)');
                            if (($_SESSION['position'] === 'President' || $_SESSION['position'] === 'Treasurer' || $_SESSION['position'] === 'Station Manager' || $_SESSIOn['position'] === 'Financial Manager') && $total < 150) {
                                $status = 'Organization Approval';
                            } else {
                                $status = 'Submitted';
                            }
                            $insert_arf->execute([$id, $_REQUEST['id'], $ogc, $status]);
                        } catch(PDOException $e) {echo $e->getMessage();}
                    } elseif ($_REQUEST['action'] === "delete") {
                        $ogc = $dbh->prepare('DELETE FROM arf WHERE id = ?');
                        $ogc->execute([$_REQUEST['request']]);
                    } elseif ($_REQUEST['action'] === "set" && isset($_REQUEST['request'])) {
                        $ogc = $dbh->prepare('SELECT id, organization_id, text, status FROM arf WHERE id = ?');
                        $ogc->execute([$_REQUEST['request']]);
                    }
                    $ogc = $dbh->prepare('SELECT id, organization_id, text, status FROM arf WHERE organization_id = ?');
                    $ogc->execute([$organization['id']]);
                    // array_pop($ogc);
?>
<h4>Allocation Requests</h4>
<?php
                    while ($line = $ogc->fetch()) {
                        $signatory = $line['id'].' --><li>'.$_SESSION['HTTP_DISPLAYNAME'];
?>
<hr />
<div class="wizard">
<?php foreach (array("Submitted", "Organization Approval", "Scheduled", "Resolved") as $step) {
if ($step === $line['status']) {
    $active = ' class="active"';
} else {
    $active = '';
}
?>
<a href="#<?=$step?>"<?=$active?>><?=$step?></a>
<?php } ?>
</div>
<?=$line['text']?>
<?php if (!empty($_SESSION['position'])) {
    if (strpos($line['text'], $signatory) === FALSE) { ?>
<a href="?id=<?=$line['organization_id']?>&amp;request=<?=$line['id']?>&amp;admin&amp;action=sign">Sign Request</a>&nbsp;
<?php } ?><a href="?id=<?=$line['organization_id']?>&amp;request=<?=$line['id']?>&amp;admin&amp;action=delete">Delete Request</a>
<?php 
        if (isset($_SESSION['admin'])) {
 ?>
     <a href="?request=<?=$line['id']?>&amp;action=set&amp;schedule">Schedule</a>
<?php 
        }
} ?>
<?php
                    }
                }}
} else { ?>
                <h5>Description</h5>
                <p><?=$organization['description']?></p>
                <h5><a href="http://osl.umbc.edu/orgs/pdf/<?=$organization['id']?>" target="_blank">Constitution</a></h5>
                <h5>Current Officers</h5>
                <ul>
<?php
    $officers->execute([$organization['id']]);
while ($officer = $officers->fetch()) { ?>
                <li><strong class="officer-position"><?=$officer['position']?></strong>: <span class="officer-contact"><a href="mailto:<?=$officer['email']?>"><?=$officer['name']?></a></span>
<?php } ?>
</ul>
<?php if ($organization['organization_group']) {
$myumbc = simplexml_load_string(file_get_contents($organization['organization_group'].'/posts.xml?page_size=5'));
$href= ($myumbc->NewsItem?"#myumbc":$organization['organization_group']);
?>
    <h5><a href="<?=$href?>" class="modal-trigger">myUMBC Group</a></h5>
<?php } ?>
<?php if (!empty($_SESSION['position'])) { ?>
    <h5><a href="?id=<?=$_REQUEST['id']?>&admin">Allocation Request Form</a></h5>
<?php } ?>
    <h5><a href="?id=<?=$_REQUEST['id']?>&admin&action=list-requests">List Requests</a></h5>
<?php if ($organization['organization_group']) { ?>
<div class="modal" id="myumbc">
<div class="modal-content">
<?php
$myumbc = simplexml_load_string(file_get_contents($organization['organization_group'].'/posts.xml?page_size=5'));
foreach ($myumbc->NewsItem as $ni) {
?>
        <div class="card-panel">
        <div class="card-title"><a href="<?=(string)$ni->attributes()->url?>" target="_blank"><?=(string)$ni->Title?></a></div>
        <div><?=(string)$ni->Summary?></div>
        </div>
    <?php } ?>
        <div class="card-panel">
        <div class="card-title"><a href="<?=$organization['organization_group']?>" target="_blank">Show More Posts on myUMBC</a></div>
        </div>
    </div>
<?php } ?>
<?php }} else {
    while ($o = $organization->fetch()) {
        $searchAutoFill = $searchAutoFill . "<option>".$o['name']."</option>";
?>
        <div class="card-panel organization-panel">
        <div class="card-title"><a href="?id=<?=$o['id']?>"><?=$o['name']?></a></div>
<p><?=$o['description']?></p>
        </div>
<?php }} ?>
            </div>
        </div>
      </div>
    </section>
</section>
<datalist id="searchAutoFill" onclick="return resetInput(event)">
<?=$searchAutoFill?>
</datalist>
<?php include('footer.php') ?>
  </body>
</html>

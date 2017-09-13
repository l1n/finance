<?php
include_once('session.php');
global $dbh;
$approved = $dbh->prepare('SELECT SUM(amount) FROM request JOIN ledger ON request_id = id WHERE (legislation_id NOT NULL) AND ledger.approved = 1 AND favored > opposed');
$approved->execute();
$approved = $approved->fetch()[0];
$requested = $dbh->prepare('SELECT SUM(amount) FROM request JOIN ledger ON request_id = id WHERE (legislation_id NOT NULL) AND ledger.approved = 0 AND favored > opposed');
$requested->execute();
$requested = $requested->fetch()[0];
$failed = $dbh->prepare('SELECT SUM(amount) FROM request JOIN ledger ON request_id = id WHERE (legislation_id NOT NULL) AND ledger.approved = 0 AND favored <= opposed');
$failed->execute();
$failed = $failed->fetch()[0];
$reduced = $dbh->prepare('SELECT SUM((CASE WHEN ledger.approved = 0 THEN amount ELSE 0 END)) - SUM((CASE WHEN ledger.approved = 1 THEN amount ELSE 0 END)) FROM request JOIN ledger ON request_id = id WHERE (legislation_id NOT NULL) AND favored > opposed');
$reduced->execute();
$reduced = $reduced->fetch()[0];
$pending = $dbh->prepare('SELECT SUM(amount) FROM request JOIN ledger ON request_id = id WHERE legislation_id IS NULL');
$pending->execute();
$pending = $pending->fetch()[0];
?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
    <title>Finance Board Browser</title>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/<?=$materializeVersion?>/css/materialize.min.css" />
    <link rel="stylesheet" href="/css/homepage.css" />
    <link rel="stylesheet" href="/css/site.css" />
    <link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
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
    <!--script type="text/javascript" src="//kripken.github.io/sql.js/js/sql.js"></script-->
<script type="text/javascript">
// Code adapted from http://stackoverflow.com/a/32664363
function search() {
    var input, filter, found, tables, tr, th, i, j, filterByLabel, exin;
    var qs = {
        'all': 'div.collapsible-header',
            'org': 'div.collapsible-header .request-org',
            'name': 'div.collapsible-header .request-name'
    };
    var query = qs.all;
    input = document.getElementById('umbc-nav-search-query');
    filter = input.value.toUpperCase();
    tables = document.querySelectorAll("ul.collapsible");
    [].forEach.call(tables, function (table) {
        tr = table.getElementsByTagName("li");
        var hidden = 0;
        for (i = 0; i < tr.length; i++) {
            th = tr[i].querySelector(query);
            if (th.innerText.toUpperCase().indexOf(filter) > -1) {
                found = true;
            }
            if (found) {
                $(tr[i]).removeClass('hide');
                found = false;
            } else {
                $(tr[i]).addClass('hide');
                hidden++;
            }
        }
        if (hidden) {
            document.getElementById(table.id+'-hidden').innerHTML = hidden+' requests hidden';
        } else {
            document.getElementById(table.id+'-hidden').innerHTML = '';
        }
        table.dataset['hidden'] = hidden;
    });
}
function resetInput(event) {
    search();
    var input = document.getElementById('umbc-nav-search-query');
    input.value = '';
    return false;
}
function makeContentEditable() {
    if (this.parentElement.contentEditable === "false") {
        this.parentElement.contentEditable = ""+(this.parentElement.contentEditable === "false");
        this.innerHTML = 'Save<i class="material-icons right">save</i>';
    } else {
        this.parentElement.contentEditable = ""+(this.parentElement.contentEditable === "false");
        this.innerHTML = 'Edit<i class="material-icons right">edit</i>';
    }
}
</script>
<script type="text/javascript">
window.onload = function () {
    $('#toggle').click(function () {
        if ($('.collapsible-header.active').length) {
            $('.collapsible-header.active').click();
        } else {
            $('li:not(.hide) > .collapsible-header:not(.active)').click();
        }
    });
    $('.legislationSubmit').click(function () {
        var form = this.parentElement;
        var legislation = this.parentElement.parentElement.parentElement;
        var body = '\\whereas{'+legislation.querySelector('.org-name').innerHTML+' has applied for allocation of Student Government Association funds in support of '+legislation.querySelector('.request-name').innerHTML+',}';
        body = body + '\\whereas{the Finance Board has considered the application in light of its funding guidelines, the total amount of funds available for allocation, its concern for equity among the student organizations, and other factors consistent with the SGA Budgetary Policy,}'
        var lines = legislation.querySelector('table tbody');
        var total = 0;
        Array.from(lines.children).forEach(function (tr) {
            if (tr.children[1].innerHTML !== 'Total') {
                body = body + '\\resolved{funds are allocated for '+tr.children[0].innerHTML+' in the amount of \\'+tr.children[1].innerHTML+',}';
                total = total + parseFloat(tr.children[1].innerHTML.replace(/[^0-9\.-]+/g,""));
            }
        });
        body = body + '\\resolved{total funds allocated shall not exceed in total \\$'+total+'.}';
        form.querySelector('[name="code"]').value = 'financeBoard';
        form.querySelector('[name="session"]').value = '1718';
        form.querySelector('[name="title"]').value = legislation.querySelector('.request-name').innerHTML;
        form.querySelector('[name="author"]').value = legislation.querySelector('.author-name').innerHTML;
        var today = new Date;
        form.querySelector('[name="introduced"]').value = (today.getMonth()+1)+'/'+today.getDate()+'/'+today.getFullYear();
        form.querySelector('[name="body"]').value = body;
        form.querySelector('[name="draft"]').value = "true";
        form.submit();
    });
    $('.editToggle').click(makeContentEditable);
    $('.modal').modal();
    $('select').material_select();
}
</script>

    <style type="text/css">
    /* Sticky Footer */
      body {
    display: flex;
    min-height: 100vh;
    flex-direction: column;
      }

      main {
    flex: 1 0 auto;
      }
    /* Tabs H-Scroll Fix */
.tabs {
overflow-x: hidden;
}
.right {
flex: 1;
text-align: right;
}
    </style>
  </head>
  <body id="home">
    <script src="//cdnjs.cloudflare.com/ajax/libs/materialize/<?=$materializeVersion?>/js/materialize.min.js"></script>
<?php include('header.php') ?>
    <section class="page-container layout-home">
<section class="page-content">
      <div id="content" class="row container">
    <div id="table-column" class="col s12">
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red">
                <i class="material-icons large">explore</i>
            </a>
            <ul>
                <li><a id="toggle" class="btn-floating yellow darken-1" title="Toggle Open"><i class="material-icons large">flip</i></a></li>
            </ul>
        </div>
        <div class="card">
        <div class="card-content">
        <ul class="tabs">
    <li class="tab col s4"><a href="#approved">Approved $<?=$approved?>/<?=$requested?><span id="approved-hidden" style="padding-left: 10px;"></span></a></li>
    <li class="tab col s4"><a href="#failed"  >Failed $<?=$failed?></a></li>
    <li class="tab col s4"><a href="#pending" class="active" >Pending $<?=$pending?><span id="pending-hidden" style="padding-left: 10px;"></span></a></li>
        </ul>
        <ul id="approved" class="collapsible popout" data-collapsible="expandable">
<?php
$line = $dbh->prepare('SELECT * FROM ledger WHERE request_id = ? AND amount != "0.00" ORDER BY approved, (category == "Total"), amount');
$ledger = $dbh->prepare('SELECT CAST(request.id AS INTEGER) AS id, request.name AS name, request.description AS description, ledger.approved, MIN(CAST(amount AS INTEGER)) || "/" || MAX(CAST(amount AS INTEGER)) AS amount, organization, organizations_canonical.id AS organization_id FROM request JOIN ledger ON request_id = request.id JOIN organizations_canonical ON organizations_canonical.name = request.organization WHERE ledger.category = "Total" AND (legislation_id NOT NULL) AND favored > opposed GROUP BY request_id ORDER BY request.id, ledger.approved');
if (!$ledger) {
    print_r($dbh->errorInfo());
} else {
    $ledger->execute();
    while ($row = $ledger->fetch()) {?>
            <li>
            <div class="collapsible-header" style="background: rgba(<?=eval("return (int) (1-".$row["amount"]."* 100);")?>, <?=eval("return (int) (".$row["amount"]."* 100);")?>, 0, 0.5);"><a href="/organizations/?id=<?=$row["organization_id"]?>" target="_blank"><span class="request-org chip"><?=$row["organization"]?></span></a>&nbsp;<span class="request-name"><?=$row["name"]?></span><span class="request-total right">$<?=$row["amount"]?></span></div>
                <div class="collapsible-body">
                    <p><?=$row["description"]?></p>
<table>
    <thead>
        <tr>
            <th data-field="category">Category</th>
            <th data-field="status">Status</th>
            <th data-field="price">Price</th>
        </tr>
    </thead>
    <tbody>
<?php
        $line->execute(array($row["id"]));
    while ($item = $line->fetch()) {
        $approved = "Requested ";
        if ($item["approved"] == 1) {
            $approved = "Approved";
        }
?>
        <tr>
            <td<?php if ($item["category"] == "Total") {?> style="font-weight: bold;"<?php } ?>><?=$item["category"]?></td>
            <td><?=$approved?></td>
            <td>$<?=$item["amount"]?></td>
        </tr>
<?php
    }
?>
    </tbody>
</table>
                </div>
            </li>
<?php }
}
?>
        </ul>
        <ul id="failed" class="collapsible popout" data-collapsible="expandable">
<?php
$ledger = $dbh->prepare('SELECT * FROM request JOIN ledger ON request_id = id WHERE (legislation_id NOT NULL) AND ledger.approved = 0 AND favored <= opposed ORDER BY id DESC');
if (!$ledger) {
    print_r($dbh->errorInfo());
} else {
    $ledger->execute();
    while ($row = $ledger->fetch()) { if (strlen($row[1]) < 1000) { ?>
            <li>
                <div class="collapsible-header"><span class="request-org chip"><?=$row[2]?></span>&nbsp;<span class="request-name"><?=$row[1]?></span><span class="request-total right">$<?=$row["amount"]?></span></div>
                <div class="collapsible-body">
                <!--pre><?=var_dump($row)?></pre-->
                    <p><?=$row["description"]?></p>
<table>
    <thead>
        <tr>
            <th data-field="category">Category</th>
            <th data-field="status">Status</th>
            <th data-field="price">Price</th>
        </tr>
    </thead>
    <tbody>
<?php
        $line->execute(array($row["id"]));
    while ($item = $line->fetch()) {
        $approved = "Requested ";
        if ($item["approved"] == 1) {
            $approved = "Approved ";
        }
?>
        <tr>
            <td<?php if ($item["category"] == "Total") {?> style="font-weight: bold;"<?php } ?>><?=$item["category"]?></td>
            <td><?=$approved?></td>
            <td>$<?=$item["amount"]?></td>
        </tr>
<?php
    }
?>
    </tbody>
</table>
                </div>
            </li>
<?php }}
}
?><span id="failed-hidden" style="padding-left: 10px;"></span>
        </ul>
        <ul id="pending" class="collapsible popout" data-collapsible="expandable">
<?php
$ledger = $dbh->prepare('SELECT * FROM request JOIN ledger ON ledger.request_id = request.id LEFT JOIN legislation ON legislation_id = legislation.id WHERE category = "Total" AND (legislation_id IS NULL OR legislation_id = "" OR draft = "true")');
if (!$ledger) {
    print_r($dbh->errorInfo());
} else {
    $ledger->execute();
    while ($row = $ledger->fetch()) {?>
            <li>
                <div class="collapsible-header"><span class="org-name chip"><?=$row["organization"]?></span>&nbsp;<span class="request-name"><?=$row["name"]?></span><span class="request-total right">$<?=$row["amount"]?></span></div>
                <div class="collapsible-body" contentEditable="false">
                <p>Submitter: <span class="author-name"><?=$row["officer_name"]?></span></p>
                    <p><?=$row["description"]?></p>
                    <p>Date: <?=$row["date_from"]?> - <?=$row["date_to"]?></p>
                    <p>Attendees: UMBC <?=$row["attendees_umbc"]?> / Other <?=$row["attendees_other"]?></p>
<table>
    <thead>
        <tr>
            <th data-field="category">Category</th>
            <th data-field="price">Price</th>
        </tr>
    </thead>
    <tbody>
<?php
        $line->execute(array($row["request_id"]));
    while ($item = $line->fetch()) {
?>
        <tr>
            <td<?php if ($item["category"] == "Total") {?> style="font-weight: bold;"<?php } ?>><?=$item["category"]?></td>
            <td>$<?=$item["amount"]?></td>
        </tr>
<?php
    }
?>
    </tbody>
</table>
<a class="btn waves-effect waves-light editToggle">Edit<i class="material-icons right">edit</i></a>&nbsp;<form action="legislation" method="POST">
<input name="legislation_id" type="hidden" value="<?=$row["legislation_id"]?>">
<input name="number" type="hidden" value="<?=$row["number"]?>">
<input name="request_id" type="hidden" value="<?=$row["request_id"]?>">
<input name="code" type="hidden">
<input name="session" type="hidden">
<input name="title" type="hidden">
<input name="author" type="hidden">
<input name="introduced" type="hidden">
<input name="body" type="hidden">
<input name="draft" type="hidden">
<input name="action" type="hidden" value="submit">
<a class="btn waves-effect waves-light legislationSubmit">Legislation<i class="material-icons right">send</i></a>
</form>
                </div>
            </li>
<?php }
}
?>
        </ul>
        </div>
        </div>
    </div>
      </div>
    </section>
    </section>
<?php include('footer.php') ?>
  </body>
</html>

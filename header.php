<div id="container-header-background"> 
<div id="container-header">
<header id="umbc-header"><!-- umbc header -->

<div id="umbc-logo"><a href="http://umbc.edu" title="UMBC: An Honors University in Maryland"><span>UMBC: An Honors University in Maryland</span></a></div>
</header>
<nav role="navigation" id="umbc-nav" >
<ul id="umbc-nav-items">
<li id="umbc-nav-azindex"><a href="/finance-tools">A-Z&nbsp;Index</a></li>
<li id="umbc-nav-myumbc"><a href="http://my.umbc.edu">myUMBC</a></li>
<li id="umbc-nav-events"><a href="http://my.umbc.edu/events">Events</a></li>
<!-- <li id="umbc-nav-computing"><a href="http://umbc.edu/doit">Computing</a></li> -->
<li id="umbc-nav-directory"><a href="http://umbc.edu/search/directory">Directory</a></li>
<li id="umbc-nav-maps"><a href="http://about.umbc.edu/visitors-guide/campus-map/">Maps</a></li>
</ul>

<form role="search" id="umbc-nav-search" onsubmit="return resetInput(event)" method="get">
<div id="umbc-nav-search-input">
<label for="umbc-nav-search-query">Search UMBC</label>
<input type="hidden" name="cx" value="008589878477221853669:fglxypz-c8m" />
<input type="hidden" name="ie" value="UTF-8" />
<input id="umbc-nav-search-query" type="text" placeholder="Search" autocomplete="off" name="q" size="250" maxlength="250" class="browser-default" onkeyup="search()" autofocus="autofocus" list="searchAutoFill">
<input id="umbc-nav-search-submit" type="submit" value="">
</div>
</form>
</nav>
</div>

<div id="site-menu-background"> 
<nav role="navigation" id="site-menu" >
<ul id="menu-main" class="menu">
            <li><a href="/finance-tools">Home</a>
            <li><a href="http://sga.umbc.edu">Student Government</a></li>
            <li><a href="http://osl.umbc.edu">Student Life</a></li>
            <li><a href="browse">Browse Requests</a></li>
            <?=isset($_SESSION['HTTP_DISPLAYNAME'])?'<li><a href="/finance-tools/?id=69&admin" title="Allocation Request Form">Allocation Request</a>'.'<li><a href="logout">Logged in as '.$_SESSION['HTTP_DISPLAYNAME'].'</a>':'<li><a href="login/?redirect_uri='.urlencode($_SERVER['REQUEST_URI']).'">Log In</a>'?>
          </ul>
      </nav>
</div>

<!DOCtype html>
<html lang="en">
<? require 'header-styles.php' ?>

<body>
<div class="container-fluid">
 <!-- Begin Navigation Container -->
        <nav class="navbar-xs navbar-greyscale navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <div onclick="navX(this)">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-bar-mobile-menu">
                            <span class="sr-only"></span>
                            <span class="icon-bar bar1"></span>
                            <span class="icon-bar bar3"></span>
                        </button>
                    </div>

                    <!--Logo-->
                    <div class="nav-logo">
                        <a class="navbar-left" href="index.php">
                        <img src="img/logo.png" alt="logo" width="20px" >
                            &nbsp;&nbsp;Health<b>Wise</b></a>
                    </div>
                    
                </div>

                <div class="collapse navbar-collapse" id="nav-bar-mobile-menu">
                    <ul class="nav navbar-nav navbar-right navbar-header">
                        <li>
                            <form class="navbar-form" role="search">
                            	<div class="form-group">
                            		<div class="row">
                            			<div class="col-xs-12">

                                			<input type="text" class="form-control" id="ipt"
											placeholder="Search for a patient..." name="q">
                                                

                            			</div>
                            		</div>
                            	</div>

                            </form>
                        </li>
                        <li>
                            <span id="header-search" class="visible-lg"><i class="fa fa-search" aria-hidden="true"></i></span>
                        </li>
                        <!--<li class="avatar">
                            <div class="circle">

                                <img class="img-cirlce" src="img/avatar.png" alt="user-avatar" height="20px">-->
                                <li>                      
                                <a href="#" class="dropdown-toggle" id="menu1" data-toggle="dropdown">
                                    <span id="dp-style">
                                    Dr. Davis
                                        <span class="caret"></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="menu1" id="signout-on-small">
                                  <li role="presentation"><a role="menuitem" tabindex="-1" href="#">
                                  <span style="color:#e7604a;">Sign Out
                                  </span></a></li>
                                </ul>
                                </li>
                    </ul>
                </div>

            </div>
        </nav><!-- END NAVIGATION MENU -->
    </div> <!-- End Nav Main Container -->
        <!--Nav 2-->
<div class="visible-lg">
<nav class="navbar navbar-green navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand logo"></div>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav ">
                <li class="hvr-overline-reveal-blue"><a href="#">Menu-Item1</a></li>
                <li class="divider-vertical"></li>
                <li class="hvr-overline-reveal-blue"><a href="#about">Menu-Item2</a></li>
                <li class="divider-vertical"></li>
                <li class="hvr-overline-reveal-blue"><a href="#about">Menu-Item3</a></li>
                <li class="divider-vertical"></li>
                <li class="hvr-overline-reveal-blue"><a href="#about">Menu-Item4</a></li>
                <li class="divider-vertical"></li>
            </ul>
        </div>
    </div>
</nav> <!-- End Nav 2 -->
</div>
    
<script type="text/javascript">
    document.getElementById('ipt').style.height="25px";
</script>





<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/admin/bootstrap.min.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/admin/datepicker3.css" rel="stylesheet">
    <link href="/css/admin/styles.css" rel="stylesheet">

    <!--Custom Font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="/js/admin/html5shiv.js"></script>
    <script src="/js/admin/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse"><span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span></button>
            <a class="nav-brand" href="/"><img src="{{ asset('img/core-img/logo2.png')}}" alt=""></a>
        </div>
    </div><!-- /.container-fluid -->
</nav>

<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar" style="color: white">
    <div class="profile-sidebar">
        <div class="profile-userpic">
            <img src="/img/admin.png" class="img-responsive" alt="">
        </div>
        <div class="profile-usertitle">
            <div class="profile-usertitle-name">Admin</div>
            <div class="profile-usertitle-status"><span class="indicator label-success"></span>Online</div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="divider"></div>
    @if(!empty($users) or !empty($parties)or !empty($bans) or !empty($kicks)or !empty($votes))
    <form role="search">
        <div class="form-group">
            @if(!empty($users))
            <input type="text" class="form-control" placeholder="Search by email" name="email">
                <input type="submit" hidden>
            @endif
                @if(!empty($parties))
                <input type="text" class="form-control" placeholder="Search by name" name="name">
                <input type="submit" hidden>
                @endif
                @if(!empty($bans))
                    <input type="text" class="form-control" placeholder="Search by email" name="email">
                    <input type="submit" hidden>
                @endif
                @if(!empty($kicks))
                    <input type="text" class="form-control" placeholder="Search by email" name="email">
                    <input type="submit" hidden>
                @endif
                @if(!empty($votes))
                    <input type="text" class="form-control" placeholder="Search by email" name="email">
                    <input type="submit" hidden>
                @endif
        </div>
    </form>
    @endif
    <ul class="nav menu" >
        <li class="{{Request::getPathInfo() === '/admin' ? 'active' : 'parent'}}"><a href="/admin"  style="color: white"><em class="fa fa-dashboard">&nbsp;</em> Dashboard</a></li>
        <li class="{{(Request::getPathInfo() ==='/admin/user/new' or Request::getPathInfo() === '/admin/users') ? 'active parent': 'parent'}}"><a data-toggle="collapse" href="#sub-item-1" style="color: white">
                <em class="fa fa-navicon">&nbsp;</em> Users<span data-toggle="collapse" href="#sub-item-1" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-1">
                <li><a class="" href="/admin/user/new">
                        <span class="fa fa-arrow-right">&nbsp;</span> Add a new user
                    </a></li>
                <li><a class="" href="/admin/users">
                        <span class="fa fa-arrow-right">&nbsp;</span> Show all users
                    </a></li>
            </ul>
        </li>
        <li class="{{(Request::getPathInfo() ==='/admin/party/new' or Request::getPathInfo() === '/admin/parties') ? 'active parent': 'parent'}}"><a data-toggle="collapse" href="#sub-item-2" style="color: white">
                <em class="fa fa-navicon">&nbsp;</em> Parties<span data-toggle="collapse" href="#sub-item-2" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-2">
                <li><a class="" href="/admin/party/new">
                        <span class="fa fa-arrow-right">&nbsp;</span> Create a new party
                    </a></li>
                <li><a class="" href="/admin/parties">
                        <span class="fa fa-arrow-right">&nbsp;</span> Show all parties
                    </a></li>
            </ul>
        </li>
        <li class="{{(Request::getPathInfo() ==='/admin/vote/new' or Request::getPathInfo() === '/admin/votes') ? 'active parent': 'parent'}}"><a data-toggle="collapse" href="#sub-item-3" style="color: white">
                <em class="fa fa-navicon">&nbsp;</em> Votes <span data-toggle="collapse" href="#sub-item-3" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-3">
                <li><a class="" href="/admin/vote/new">
                        <span class="fa fa-arrow-right">&nbsp;</span> Create a new vote
                    </a></li>
                <li><a class="" href="/admin/votes">
                        <span class="fa fa-arrow-right">&nbsp;</span> Show all votes
                    </a></li>
            </ul>
        </li>
        <li class="{{(Request::getPathInfo() ==='/admin/kick/new' or Request::getPathInfo() === '/admin/kicks') ? 'active parent': 'parent'}}"><a data-toggle="collapse" href="#sub-item-4" style="color: white">
                <em class="fa fa-navicon">&nbsp;</em> Kicks <span data-toggle="collapse" href="#sub-item-4" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-4">
                <li><a class="" href="/admin/kick/new">
                        <span class="fa fa-arrow-right">&nbsp;</span> Create a new kick
                    </a></li>
                <li><a class="" href="/admin/kicks">
                        <span class="fa fa-arrow-right">&nbsp;</span> Show all kicks
                    </a></li>
            </ul>
        </li>
        <li class="{{(Request::getPathInfo() ==='/admin/ban/new' or Request::getPathInfo() === '/admin/bans') ? 'active parent': 'parent'}}"><a data-toggle="collapse" href="#sub-item-5" style="color: white">
                <em class="fa fa-navicon">&nbsp;</em> Bans <span data-toggle="collapse" href="#sub-item-5" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-5">
                <li><a class="" href="/admin/ban/new">
                        <span class="fa fa-arrow-right">&nbsp;</span> Create a new ban
                    </a></li>
                <li><a class="" href="/admin/bans">
                        <span class="fa fa-arrow-right">&nbsp;</span> Show all bans
                    </a></li>
            </ul>
        </li>
        <li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
            <a href="{{ route('logout') }}"
               style="color: white"
               onclick="event.preventDefault();
            document.getElementById('logout-form').submit();"><em class="fa fa-power-off">&nbsp;</em> Logout</a></li>
    </ul>
</div>
<!-- ***** Header Area End ***** -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">

    </div>

@yield('section')

    <script src="/js/admin/jquery-1.11.1.min.js"></script>
    <script src="/js/admin/bootstrap.min.js"></script>
</div>
</body>
</html>

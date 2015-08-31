@if(!empty($mainBtnColor))
    .btn-primary {
        background-color: {{$mainBtnColor}};
        color: #fff;
    }
    .grid-box .boxed.question-choice:hover {
        background: {{$mainBtnColor}};
        color: #fff;
        border-color: transparent;
    }
    .items-row .item-category {
        background-color: {{$mainBtnColor}};
        color: #fff;
    }
    .list-editor .add-item-btn {
        background-color: {{$mainBtnColor}};
        color: #fff;
    }
    .list-items-container .item-number {
        background-color: {{$mainBtnColor}};
        color: #fff;
    }
    .loading-primary {
        background-color: {{$mainBtnColor}};
    }
    .profile-header .user-name {
        border-color: {{$mainBtnColor}};
    }
@endif

@if(!empty($linkColor))
    a{
        color: {{$linkColor}};
    }
    a:hover, a:focus {
        color: #222;
        text-decoration: none;
    }
    #loginDialog .heading {
        background-color: {{$navbarColor}} !important;
        color: {{$navbarLinkColor}};
    }
@endif

.navbar-default {
    background-color: {{$navbarColor}} !important;
}
.navbar-default .navbar-nav > li > a {
    color: {{@$navbarLinkColor}} !important;
}
.navbar-search-form .search-btn {
    color: {{@$navbarLinkColor}} !important;
}
.navbar-default .navbar-brand {
    color: {{@$navbarLinkColor}} !important;
}
.navbar-toggle:before {
    color: {{@$navbarLinkColor}} !important;
}
.navbar-default .navbar-nav > .dropdown > a .caret {
    border-top-color: {{@$navbarLinkColor}} !important;
    border-bottom-color: {{@$navbarLinkColor}} !important;
}

.navbar-default .navbar-nav > li > a.navbar-create-btn {
    border-radius: 0px !important;
    background: {{@$navbarCreateButtonColor}};
    color: {{@$navbarCreateButtonLinkColor}} !important;
}
#topmenu .dropdown > li{
    border-right: 1px solid rgba(255, 255, 255, 0.24);
}

#topmenu .dropdown > li a{
    color: #ffffff;
}

#topmenu #headerUserLoginLink > span {
    background: #fff;
    color: #222;
}

#loginDialog .dialog__content {
    border-top: solid 3px {{$navbarColor}};
}
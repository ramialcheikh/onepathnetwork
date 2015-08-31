<style>
    body{
        direction: rtl;
        text-align: right;
    }

    .rtl-language .pull-right {
        float: left !important;
    }

    .rtl-language .pull-left {
        float: right !important;
    }

    .rtl-language .text-left {
        text-align: right !important;
    }
    .rtl-language .text-right {
        text-align: left !important;
    }

    /*Certain Animations(those using x/y transforms) disabled in RTL languages as it has some display glitch on Chrome*/
    .fadeInLeft, .fadeInRight, .bounceInLeft, .bounceInRight {
        /*Replacing them with a simple working one*/
        -webkit-animation-name: fadeIn;
        animation-name: fadeIn;
    }
    /*Float navbar menus to the right*/
    li.menu-level-0 {
        float: right;
    }
    .progress-breadcrumb li:first-child {
        margin-left: 0px;
    }
    .progress-breadcrumb li {
        padding: 0px !important;
    }
    .progress-breadcrumb li a {
        margin-right: 0px;
        padding: 0px 10px;
        border-radius: 3px !important;
    }
    .progress-breadcrumb li a:before{
         display: none;
     }
    .progress-breadcrumb li a:after{
        display: none;
    }
    .progress-breadcrumb li:first-child a {

    }
    .progress-breadcrumb li:last-child a {

    }

    .share-result-btn {
        text-align: right !important;
    }
    .share-result-btn > span{
        padding: 0px 25px;
    }

    *[class*="btn-social-"] i {
        padding-right: 0px;
        margin-right: 0px;
        border-right: none;
    }

    #topmenu #headerUserMenu #userProfilePicture {
        margin-right: 0px;
        margin-left: 10px;
    }
</style>
@section('adminlte_css')

@endsection
<style>
    .bg-gradient-medical,
    .content-wrapper,
    .content,
    .breadcrumb {
        background-color: #f4f6f9 !important;
    }

    a,
    a:focus,
    a:hover {
        color: #fff;
    }

    .btn-secondary,
    .btn-secondary:hover,
    .btn-secondary:focus {
        color: #333;
        text-shadow: none;
        background-color: #fff;
        border: .05rem solid #fff;
    }


    html,
    body {
        height: 100%;
        background-color: #333;
    }

    body {
        color: #fff;
        text-align: center;
        text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);
    }

    .site-wrapper {
        display: table;
        width: 100%;
        height: 100%;
        /* For at least Firefox */
        min-height: 100%;
        box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);
        background: url(../img/bg.jpg);
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    .site-wrapper-inner {
        display: table-cell;
        vertical-align: top;
    }

    .cover-container {
        margin-right: auto;
        margin-left: auto;
    }

    .inner {
        padding: 2rem;
    }

    .masthead {
        margin-bottom: 2rem;
    }

    .masthead-brand {
        margin-bottom: 0;
    }

    .nav-masthead .nav-link {
        padding: .25rem 0;
        font-weight: 700;
        color: rgba(255, 255, 255, .5);
        background-color: transparent;
        border-bottom: .25rem solid transparent;
    }

    .nav-masthead .nav-link:hover,
    .nav-masthead .nav-link:focus {
        border-bottom-color: rgba(255, 255, 255, .25);
    }

    .nav-masthead .nav-link+.nav-link {
        margin-left: 1rem;
    }

    .nav-masthead .active {
        color: #fff;
        border-bottom-color: #fff;
    }

    @media (min-width: 48em) {
        .masthead-brand {
            float: left;
        }

        .nav-masthead {
            float: right;
        }
    }


    /*
 * Cover
 */

    .cover {
        padding: 0 1.5rem;
    }

    .cover .btn-lg {
        padding: .75rem 1.25rem;
        font-weight: 700;
    }

    .mastfoot {
        color: rgba(255, 255, 255, .5);
    }

    @media (min-width: 40em) {
        .masthead {
            position: fixed;
            top: 0;
        }

        .mastfoot {
            position: fixed;
            bottom: 0;
        }


        .site-wrapper-inner {
            vertical-align: middle;
        }

        /* Handle the widths */
        .masthead,
        .mastfoot,
        .cover-container {
            width: 100%;
        }
    }

    @media (min-width: 62em) {

        .masthead,
        .mastfoot,
        .cover-container {
            width: 42rem;
        }
    }

    .chatbubble {
        position: fixed;
        bottom: 0;
        right: 30px;
        transform: translateY(300px);
        transition: transform .3s ease-in-out;
    }

    .chatbubble.opened {
        transform: translateY(0)
    }

    .chatbubble .unexpanded {
        display: block;
        background-color: #e23e3e;
        padding: 10px 15px 10px;
        position: relative;
        cursor: pointer;
        width: 350px;
        border-radius: 10px 10px 0 0;
    }

    .chatbubble .expanded {
        height: 300px;
        width: 350px;
        background-color: #fff;
        text-align: left;
        padding: 10px;
        color: #333;
        text-shadow: none;
        font-size: 14px;
    }

    .chatbubble .chat-window {
        overflow: auto;
    }

    .chatbubble .loader-wrapper {
        margin-top: 50px;
        text-align: center;
    }

    .chatbubble .messages {
        display: none;
        list-style: none;
        margin: 0 0 50px;
        padding: 0;
    }

    .chatbubble .messages li {
        width: 85%;
        float: left;
        padding: 10px;
        border-radius: 5px 5px 5px 0;
        font-size: 14px;
        background: #c9f1e6;
        margin-bottom: 10px;
    }

    .chatbubble .messages li .sender {
        font-weight: 600;
    }

    .chatbubble .messages li.support {
        float: right;
        text-align: right;
        color: #fff;
        background-color: #e33d3d;
        border-radius: 5px 5px 0 5px;
    }

    .chatbubble .chats .input {
        position: absolute;
        bottom: 0;
        padding: 10px;
        left: 0;
        width: 100%;
        background: #f0f0f0;
        display: none;
    }

    .chatbubble .chats .input .form-group {
        width: 80%;
    }

    .chatbubble .chats .input input {
        width: 100%;
    }

    .chatbubble .chats .input button {
        width: 20%;
    }

    .chatbubble .chats {
        display: none;
    }

    .chatbubble .login-screen {
        margin-top: 20px;
        display: none;
    }

    .chatbubble .chats.active,
    .chatbubble .login-screen.active {
        display: block;
    }

    /* Loader Credit: https://codepen.io/ashmind/pen/zqaqpB */
    .chatbubble .loader {
        color: #e23e3e;
        font-family: Consolas, Menlo, Monaco, monospace;
        font-weight: bold;
        font-size: 10vh;
        opacity: 0.8;
    }

    .chatbubble .loader span {
        display: inline-block;
        -webkit-animation: pulse 0.4s alternate infinite ease-in-out;
        animation: pulse 0.4s alternate infinite ease-in-out;
    }

    .chatbubble .loader span:nth-child(odd) {
        -webkit-animation-delay: 0.4s;
        animation-delay: 0.4s;
    }

    @-webkit-keyframes pulse {
        to {
            -webkit-transform: scale(0.8);
            transform: scale(0.8);
            opacity: 0.5;
        }
    }

    @keyframes pulse {
        to {
            -webkit-transform: scale(0.8);
            transform: scale(0.8);
            opacity: 0.5;
        }
    }

</style>

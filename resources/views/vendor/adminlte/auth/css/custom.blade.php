<style>
@import url('https://fonts.googleapis.com/css?family=Numans');
html,
body {
    background-image : url("{{ asset('vendor/adminlte/dist/img/nuvetoback.jpg') }}") !important;
    background-size  : cover !important;
    background-repeat: no-repeat !important;
    height           : 100% !important;
    font-family      : 'Numans', sans-serif !important;
}
.card {
    height          : 370px !important;
    margin-top      : auto !important;
    margin-bottom   : auto !important;
    width           : 400px !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
}

.card-header:first-child {
    border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
}
.card-footer:last-child {
    border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0 !important;
}

.card-header h3 {
    color: white;
    font-weight: bold;
}

.input-group-prepend span {
    width           : 50px;
    background-color: #FFC312;
    color           : black;
    border          : 0 !important;
}

input:focus {
    outline   : 0 0 0 0 !important;
    box-shadow: 0 0 0 0 !important;

}

.remember {
    color: white !important;
}

.remember input {
    width       : 20px !important;
    height      : 20px !important;
    margin-left : 15px !important;
    margin-right: 5px !important;
}

.login_btn, .register_btn, .send_mail_btn {
    color           : #fff !important;
    background-color: #D02C2F !important;
    border-color : #D02C2F !important;
}

.login_btn:hover, .register_btn:hover {
    color: #212529 !important;
    text-decoration: none !important;
}

.input-group-text span{
    color : #D02C2F !important;
}

p.my-0 {
    color: #fff;
}

p.my-0 a:hover {
    color: #D02C2F;
}

</style>

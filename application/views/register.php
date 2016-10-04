<?php
if (!$this->sessiondata['userid']) {
    ?>

<div class="col-md-8">
    <!--START LIGHTBOXES-->
    <div class="lightbox-info-popup">
        <div id="UserEmailLightbox" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="modal-title color-light-blue noto-sans">User Email Requirements</h3>
                    </div>
                    <div class="modal-body">
                        <p class="font-size-fourtheen-px noto-sans">Your account email must fullfil the following requirements:</p>
                        <div class="display-block">
                            <ol class="segoe font-size-fourtheen-px">
                                <li>It must unique</li>
                                <li>It must be a valid email account</li>
                                <li>Some other reason</li>
                                <li>...</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close padding-right-five-px"></i>Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="UserEmailLightbox" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="modal-title color-light-blue noto-sans">User Email Requirements</h3>
                    </div>
                    <div class="modal-body">
                        <p class="font-size-fourtheen-px noto-sans">Your account email must fullfil the following requirements:</p>
                        <div class="display-block">
                            <ol class="segoe font-size-fourtheen-px">
                                <li>It must unique</li>
                                <li>It must be a valid email account</li>
                                <li>Some other reason</li>
                                <li>...</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close padding-right-five-px"></i>Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="Usernamebox" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="modal-title color-light-blue noto-sans">About the username</h3>
                    </div>
                    <div class="modal-body">
                        <p class="font-size-fourtheen-px noto-sans">Your username has to be:</p>
                        <div class="display-block">
                            <ol class="segoe font-size-fourtheen-px">
                                <li>Unique in our database</li>
                                <li>Usernames must be between 1 and 12 characters long.</li>
                                <li>Usernames may only contain alphanumeric characters, the space, and hyphens (-). Any other characters are replaced with a space.</li>
                                <li>Usernames cannot start with or end with a space, but can have any number of spaces between.</li>
                                <li>Usernames cannot contain one of several phrases including "Java", "Mod", "Jagex", etc.</li>
                                <li>Usernames cannot contain anything offensive such as inappropriate words or curse words</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close padding-right-five-px"></i>Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="CVVLightbox" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="modal-title color-light-blue noto-sans">Password Requirements</h3>
                    </div>
                    <div class="modal-body">
                        <p class="font-size-fourtheen-px noto-sans">Help make your ProPay account more secure by following there requirements:</p>
                        <div class="display-block">
                            <ol class="segoe font-size-fourtheen-px">
                                <li>At least 1 upper-case letter (A-Z)</li>
                                <li>At least 1 lower-case letter (a-z)</li>
                                <li>At least 1 number (0-9)</li>
                                <li>Must be between 8-19 characters</li>
                                <li>Optional: Spaces and keyword symbols (! @ # $ % ^ & * _)</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close padding-right-five-px"></i>Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--END LIGHTBOXES-->

    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <div id="SignUpForm" class="modal-dialog" style="width: 100%; margin-top: 0;" >
        <div class="modal-content" >
            <div class="modal-header">
                <h3 class="modal-title" id="myModalLabel"><i class="fa fa-user"></i> Setup Your New Account on RsFlipLog</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="well">
                            <div class="padding-top-and-bottom-fiftheen-px align-text-center hidden-sm hidden-xs">
                                <i class="fa fa-user" style="width: 65px; height: 65px; font-size: 45px; margin: 0 auto 8px; padding-top: 7px; padding-left: 13px; display: block; border: solid 1px #CDCDCD; color: #CECECE; -moz-border-radius: 50%; -webkit-border-radius: 50%; border-radius: 50%;"></i>
                            </div>
                            <form id="NewAccountForm" method="POST" action="submit">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" ><i class="fa fa-user" title="Username"></i></span>
                                        <input type="text" class="form-control" id="username" min="3" name="username" pattern=".{3,12}" value="" required title="Please enter you username (between 4-12 characters)" placeholder="username" />
                                        <span class="input-group-addon" id="PasswordLockAltHelp"><a href="#Usernamebox" data-toggle="modal"><i class="fa fa-question-circle"></i></a></span>
                                    </div>
                                    <span class="help-block"></span>
                                    <div class="input-group">
                                        <span class="input-group-addon" id="UserEmail"><i class="fa fa-user" title="Enter Your Email"></i></span>
                                        <input type="email" class="form-control" id="username" name="username" value="" required title="Please enter your email" placeholder="example@gmail.com" />
                                        <span class="input-group-addon" id="PasswordLockAltHelp"><a href="#UserEmailLightbox" data-toggle="modal"><i class="fa fa-question-circle"></i></a></span>
                                    </div>
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="UserPassword"><i class="fa fa-unlock-alt" title="Choose password"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" value="" required title="Choose password" placeholder="Choose a password" />
                                        <span class="input-group-addon" id="PasswordLockAltHelp"><a href="#CVVLightbox" data-toggle="modal"><i class="fa fa-question-circle"></i></a></span>
                                    </div>
                                    <span class="help-block"></span>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="UserPasswordMatch"><i class="fa fa-lock" title="Choose password"></i></span>
                                        <input type="password" class="form-control" id="passwordmatch" name="passwordmatch" value="" required title="Re-enter password" placeholder="Re-enter password" />
                                        <span class="input-group-addon" id="PasswordLockAltHelp"><a href="#CVVLightbox" data-toggle="modal"><i class="fa fa-question-circle"></i></a></span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">Submit</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <p class="lead">New Account Requirements</p>
                        <ul class="list-unstyled small" style="line-height: 2">
                            <li><span class="fa fa-check text-success"></span> Provide a <strong>valid</strong> email account (for using export feature etc.)</li>
                            <li><span class="fa fa-check text-success"></span> Password must be at least 6 characters long</li>
                            <li><span class="fa fa-check text-success"></span> Your registered username cannot be changed</li>
                            <li><span class="fa fa-check text-success"></span> Be at least 1 number (0-9)</li>
                            <li><span class="fa fa-check text-success"></span> It must be between 8-19 characters</li>
                            <li><span class="fa fa-check text-success"></span> Spaces & symbols (! @ # $ % ^ & * _)</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--

                <div id="SignInForm" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="myModalLabel"><i class="fa fa-lock"></i> Secure Account Login</h3>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p>Enter must enter a registered Email & Password combination to access your account:</p>
                                    <div class="well">
                                        <form id="loginForm" method="POST" action="/login/" novalidate="novalidate">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="UserEmail"><i class="fa fa-user" title="Enter Your Email"></i></span>
                                                    <input type="email" class="form-control" id="username" name="username" value="" required title="Please enter you username" placeholder="example@gmail.com" />
                                                </div>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="UserPasswordMatch"><i class="fa fa-lock" title="Choose password"></i></span>
                                                    <input type="password" class="form-control" id="passwordmatch" name="passwordmatch" value="" required title="Enter your password" placeholder="Enter password" />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-block">Login</button>
                                        </form>
                                    </div>
                                    <p style="text-align: center;"><a href="#" title="Forgot password?">Forgot password?</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div id="SignInForm" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="myModalLabel"><i class="fa fa-lock"></i> Reset Account Password</h3>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p>To reset your password you must enter a valid email address below:</p>
                                    <div class="well">
                                        <form id="loginForm" method="POST" action="/login/" novalidate="novalidate">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="UserEmail"><i class="fa fa-user" title="Enter Your Email"></i></span>
                                                    <input type="email" class="form-control" id="username" name="username" value="" required title="Please enter you username" placeholder="example@gmail.com" />
                                                </div>
                                                <span class="help-block"></span>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-block">Reset</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            -->

</div>
<?php
}else{
    redirect(base_url());
}
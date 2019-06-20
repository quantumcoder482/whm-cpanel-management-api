<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<?php

/**
 *  Previous page validation 
 */

$prev_page_url = "http://localhost:10085/index.php";
$next_page_url = "http://localhost:10085/index.php";


/**
 * Private Data UserID, Token
 * You should be input servername as like IP address or domain name and port number. (default port number is 2087)
 * packageName is your plan for account
 */

$user = "root";
$token = "";
$serveraddr = "45.79.27.235:2087";
$cpaneladdr = "45.79.27.235:2083";
$packageName = "newone";


/**
 *  Confirm Validation Request for Create a New Account
 */
/*
  if ($_SERVER['HTTP_REFERER'] == $prev_page_url && !empty($_POST)) {
    header("Location: $next_page_url");
  }
*/

/**
 * Validation Checking functions
 */

function is_valid_domain_name($domain_name)
{
    if (
        preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name)
        && preg_match("/^.{1,253}$/", $domain_name)
        && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)
    ) {
        return true;
    } else {
        return false;
    }
}

function is_valid_user_name($username)
{
    if (preg_match('/^[A-Za-z][A-Za-z0-9]{2,15}$/', $username)) {
        return true;
    } else {
        return false;
    }
}

function is_valid_password($password)
{
    $error_msg = '';
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        $error_msg .= 'Password should be include at least a uppercase, lowercase, number and exceed 8 characters. ';
    }
    return $error_msg;
}


/**
 *  Getting Form Data  (UserName, Email, Password)
 */


$post_status = 0;
$post_success = 0;

if (!empty($_POST)) {
    $post_status = 1;
}



$msg = array();

$domain = $_POST['domain'];
if ($domain == '') {
    $msg['domain'] = "Domain is required";
} elseif (!is_valid_domain_name($domain)) {
    $msg['domain'] = "Domain format invalid";
}


$username = $_POST['user_name'];
if ($username == '') {
    $msg['user_name'] = "User Name is required";
} elseif (!is_valid_user_name($username)) {
    $msg['user_name'] = "User Name format invalid";
}

$alternate_email = $_POST['email'];
if ($alternate_email == '') {
    $msg['email'] = "Email is required";
}

$password = $_POST['pwd'];
$confirm_password = $_POST['confirm_pwd'];

if ($password == '') {
    $msg['password'] = "Password is required";
}
if ($confirm_password == '') {
    $msg['confirm_pwd'] = "Password Confirm is required";
}
if ($password !== $confirm_password) {
    $msg['pwd_match_error'] = "Password doesn't matched. Confirm Password again";
} elseif ($pwd_error_message = is_valid_password($password)) {
    $msg['pwd_format_error'] = $pwd_error_message;
}


if (count($msg) == 0) {

    $query = "https://" . $serveraddr . "/json-api/listaccts?api.version=1";
    $query1 = "https://" . $serveraddr . "/json-api/createacct?api.version=1&username=$username&domain=$domain&bwlimit=unlimited&cgi=1&contactemail=$alternate_email&cpmod=paper_lantern&customip=192.0.2.0&dkim=1&featurelist=feature_list&forcedns=0&hasshell=1&hasuseregns=1&homedir=/home/user&ip=n&language=en&owner=root&mailbox_format=mdbox&max_defer_fail_percentage=unlimited&max_email_per_hour=unlimited&max_emailacct_quota=1024&maxaddon=unlimited&maxftp=unlimited&maxlst=unlimited&maxpark=unlimited&maxpop=unlimited&maxsql=unlimited&maxsub=unlimited&mxcheck=auto&owner=root&password=$password&pkgname=my_new_package&plan=default&quota=500&reseller=0&savepkg=1&spamassassin=1&spf=1&spambox=y&useregns=0";
    $query2 = "https://" . $serveraddr . "/json-api/createacct?api.version=1&username=$username&domain=$domain&contactemail=$alternate_email&password=$password&plan=$packageName";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $header[0] = "Authorization: whm $user:$token";

    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_URL, $query2);

    $result = curl_exec($curl);

    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($http_status != 200) {
        echo "[!] Error: " . $http_status . " returned\n";
    } else {
        $json = json_decode($result);

        if ($json->{'metadata'}->{'result'} == '0') {
            echo "\t" . "<div class='col-md-offset-4 col-md-8'><span class='text-danger' style='font-size:16px'>ERROR :" . $json->{'metadata'}->{'reason'} . "</span><br/></div>";
            // echo "\t" . "<div class='col-md-offset-4 col-md-8'><span class='text-danger' style='font-size:16px'>Some Errors Occured. Please Check fields again</span><br/></div>";
        } else {
            // echo "\t" . "<div class='col-md-offset-4 col-md-8'><span class='text-success' style='font-size:16px'>Your Account created successfully! You can use the account</span></div>";
            // echo "<script>alert('Your account created successfully!'); location.href='$next_page_url';</script>";
            // header("Location: $next_page_url");
            $post_success = 1;
        }
    }

    curl_close($curl);
}

?>

<?php if (!$post_success) { ?>
    <!-- UI -->
    <div class="container">
        <div class="col-md-6">
            <form class="form-horizontal" id="form_main" action="<?php echo $_SERVER[' PHP_SELF']; ?>" method="POST">
                <div class="form-group">
                    <label class="control-label col-md-4">Package Name:</label>
                    <div>
                        <span style="font-weight:700;font-size:16px;position:absolute;padding-right:15px; padding-left:15px;padding-top:7px"><?php echo $packageName; ?></span>
                    </div>

                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="domain">Domain:</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="domain" name="domain" autocomplete="off" value="<?php echo $_POST['domain']; ?>">
                        <?php
                        if ($post_status && $msg['domain'] != '') {
                            echo "<span class='error text-danger'>{$msg['domain']}</span>";
                        }
                        ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="user_name">User Name:</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="user_name" name="user_name" autocomplete="off" value="<?php echo $_POST['user_name']; ?>">
                        <?php
                        if ($post_status && $msg['user_name'] != '') {
                            echo "<span class='error text-danger'>{$msg['user_name']}</span>";
                        }
                        ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="email">Email:</label>
                    <div class="col-md-8">
                        <input type="email" class="form-control" id="email" name="email" autocomplete="off" value="<?php echo $_POST['email']; ?>">
                        <?php
                        if ($post_status && $msg['email'] != '') {
                            echo "<span class='error text-danger'>{$msg['email']}</span>";
                        }
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="pwd">Password:</label>
                    <div class="col-md-8">
                        <input type="password" class="form-control" id="pwd" name="pwd" autocomplete="off" value="<?php echo $_POST['pwd']; ?>">
                        <?php
                        if ($post_status && $msg['password'] != '') {
                            echo "<span class='error text-danger'>{$msg['password']}</span>";
                        }
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="confirm_pwd">Password Confirm:</label>
                    <div class="col-md-8">
                        <input type="password" class="form-control" id="confirm_pwd" name="confirm_pwd" autocomplete="off" value="<?php echo $_POST['confirm_pwd']; ?>">
                        <?php
                        if ($post_status && $msg['confirm_pwd'] != '') {
                            echo "<span class='error text-danger'>{$msg['confirm_pwd']}</span>";
                        } elseif ($post_status && $msg['pwd_match_error'] != '') {
                            echo "<span class='error text-danger'>{$msg['pwd_match_error']}</span>";
                        } elseif ($post_status && $msg['pwd_format_error'] != '') {
                            echo "<span class='error text-danger'>{$msg['pwd_format_error']}</span>";
                        }

                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-4 col-md-8">
                        <button type="submit" class="btn btn-primary" id="btn_submit">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
<?php
} else {
    ?>
    <div class="container">
        <div class="col-md-6">
            <form class="form-horizontal">
                <div class="form-group">
                    <p style="font-size:18px; font-weight:600; margin-left:30px; margin-top:30px">Your cPanel Account created successfully.</p>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">Package Name:</label>
                    <div class="col-md-4">
                        <span style="font-weight:700;font-size:16px;position:absolute;padding-right:15px; padding-left:15px;padding-top:7px"><?php echo $packageName; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">User Name:</label>
                    <div class="col-md-8">
                        <span style="font-weight:700;font-size:16px;position:absolute;padding-right:15px; padding-left:15px;padding-top:7px"><?php echo $username; ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4">cPanel Address:</label>
                    <div class="col-md-8">
                        <span style="font-weight:700;font-size:16px;position:absolute;padding-right:15px; padding-left:15px;padding-top:7px">
                            <a href="<?php echo "https://" . $cpaneladdr; ?>" target="_blank"><?php echo "https://" . $cpaneladdr; ?></a>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } ?>
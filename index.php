<?php error_reporting(E_ALL & ~E_NOTICE); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create New cPanel Account</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width; initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>

<body>
    <form>
        <button class="btn btn-primary" id="btn_gotopage">Goto Post Page</button>
    </form>
</body>
<script>
    $(document).ready(function() {
        $('#btn_gotopage').click(function(e) {
            e.preventDefault();
            location.href = "http://localhost:10085/post.php";
        });
    });
</script>

</html>
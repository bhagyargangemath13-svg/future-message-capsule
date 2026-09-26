<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="refresh" content="1">

    <style>
        body {
            background-color: maroon;
        }

        p {
            color: yellow;
            font-size: 90px;
            text-align:center;
           
            padding:300px;
            
        }
    </style>
</head>

<body>

    <p>
        <?php
        date_default_timezone_set("Asia/Kolkata");
        echo date("h:i:s A");
        ?>
    </p>

</body>

</html>
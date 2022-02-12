<?php
session_start();
?>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/peerjs@1.3.1/dist/peerjs.min.js"></script>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>RedRadon</title>


    <script>
        var peer = new Peer();
    </script>
</head>
<body>
    <div class="container" id="rootpage">
        <div class="jumbotron" id="jumbo">
            <center>
                <h1>
                    Two Of These People Are Lying
                </h1>
            </center>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Host A Session</div>
            <div class="panel-body">
                <form action="game.php" method="post" class="form-inline">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input name="name" type="name" class="form-control" id="name" placeholder="Your Name" value="<?php echo $_SESSION["name"];?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input name="ID" id="ID" type="text" class="form-control" name="ID" readonly="true" hidden="false">
                    </div>
                    <button type="submit" name="action" value="host" class="btn btn-primary">Host Session</button>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Join A Session</div>
            <div class="panel-body">
                <form action="game.php" method="post" class="form-inline">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input name="name" type="name" class="form-control" id="name" placeholder="Your Name" value="<?php echo $_SESSION["name"];?>">
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input name="host" id="host" type="text" class="form-control" name="host" placeholder="Host ID">
                    </div>
                    <input name="ID" id="ID2" type="text" class="form-control" name="ID" readonly="true" hidden="true">
                    <button type="submit" name="action" value="join" class="btn btn-primary">Join Session</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        peer.on('open', function(id) {
            console.log('My peer ID is: ' + id);
            document.getElementById("ID").value = id;
            document.getElementById("ID2").value = id;
        });
    </script>
</body>
</html>
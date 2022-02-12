<?php
session_start();

if ($_POST['action'] == 'host') {
    //action for host here
    $_SESSION["name"] = $_POST["name"];
    $_SESSION["role"] = "host";
    $_SESSION["ID"] = $_POST["ID"];
} else if ($_POST['action'] == 'join') {
    //action for join
    $_SESSION["name"] = $_POST["name"];
    $_SESSION["host"] = $_POST["host"];
    $_SESSION["role"] = "join";
    $_SESSION["ID"] = $_POST["ID"];
} else {
    //invalid action!
}

?>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/peerjs@1.3.1/dist/peerjs.min.js"></script>


    <style>
        iframe {
            width:100%;
            height:500px;
        }
    </style>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>RedRadon</title>

    <script>
        var peer = new Peer("<?php echo $_SESSION["ID"]; ?>");
        var connList = [];
        var status;
        var articles = [];
        var role = "<?php echo $_SESSION["role"];?>";
        var host = "<?php echo $_SESSION["host"];?>";
        var name = "<?php echo $_SESSION["name"];?>";

        var myID;

    </script>
</head>
<body>
<div class="jumbotron" id="jumbo">
    <center>
        <h1 id="gamebar">
            Ready to Play
        </h1>
    </center>
</div>    
<div class="container" id="rootpage">
    <div class="panel panel-default">
        <div class="panel-heading">Session Info: <span id="status">Loading...</span></div>
        <div class="panel-body">
            My ID: <span id="myID">Loading...</span><br>
            Host ID: <b><span id="hostID">Loading...</span></b>
            <br>
            <table class="table table-striped" id="playerTableParent" hidden="true">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>ID</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody id="playerTable">
                </tbody>
            </table>
        </div>
    </div>
    <br>

    <div class="panel panel-default" id="hostControls" hidden=true>
        <div class="panel-heading">Host Controls</div>
        <div class="panel-body">
            <button type="button" class="btn btn-primary" id="requestArticle" onclick="requestArticle()">Request Articles</button>
            <button type="button" class="btn btn-primary" id="selectArticle" onclick="selectArticle()" disabled=true>Select Article</button>
            <button type="button" class="btn btn-danger" onclick="reset()">Reset</button>
        </div>
    </div>
    <br>

    <div class="panel panel-default" id="articleSelection" hidden=true>
        <div class="panel-heading">Article Selection</div>
        <div class="panel-body">
        <center>
        
        <button type="button" class="btn btn-primary" style="width:33%;" id="randomArticle" onclick="randomArticle()" className='mb-2 mr-2' disabled=true>Random Article</button>
        <button type="button" class="btn btn-danger" style="width:33%;" id="submitArticle" onclick="submitArticle()" disabled=true>Submit Article</button>
        <center>
        <br>
        <center><h2 id="selectedArticle"></h2></center>
        <center><p id="selectedArticleURL"></p></center>
        <br>
        <iframe id="articlePreview"> </iframe>
        </div>
    </div>
</div>

<script>

    function handleData(conn, data) {
        console.log(conn.peer);
        console.log(data);

        if (data.hasOwnProperty("method")) {
            if (data["method"] == "getName") {
                conn.send(name);
            }
            if (data["method"] == "getStatus") {
                conn.send({"method": "replyStatus", "status": status, "name": name});
            }

            if (data["method"] == "replyStatus") {
                addPlayerTable(conn, data);
            }
            if (data["method"] == "update") {
                updatePlayerTable();
            }
            if (data["method"] == "gamebar") {
                document.getElementById("gamebar").innerHTML = data["value"];
            }
            if (data["method"] == "reset") {
                document.getElementById("articleSelection").hidden = true;
                document.getElementById("selectedArticleURL").innerHTML = "";
                document.getElementById("selectedArticle").innerHTML = "";
                document.getElementById("articlePreview").src = "";
                status = "Idle";
                send({"method": "update"});
            }
            if (data["method"] == "requestArticle") {
                document.getElementById("articleSelection").hidden = false;
                document.getElementById("randomArticle").disabled = false;

                status = "Selecting Article";
                send({"method": "update"});
            }
            if (data["method"] == "submitArticle") {
                articles.push(data["title"]);
                updatePlayerTable();
            }
        }
    }

    function newConn(conn) {
        console.log("connection");
        console.log(conn);
        connList.push(conn);
        conn.on('data', function(data) {
            console.log('Received', data);
            handleData(conn, data);
        });
    }

    function addPlayerTable(conn, data) {
        strng = "<tr><th>";
        strng += data["name"] + "</th><th>";
        strng += conn.peer + "</th><th>";
        strng += data["status"] + "</th></tr> ";
        if (!document.getElementById("playerTable").innerHTML.includes(strng)) {
            document.getElementById("playerTable").innerHTML += strng;
        }
    }

    function updateGameBar(value) {
        document.getElementById("gamebar").innerHTML = value;
        send({"method": "gamebar", "value": value});
    }

    function requestArticle() {
        articles = [];
        updateGameBar("Article Selection in Progress");
        document.getElementById("requestArticle").disabled = true;
        document.getElementById("selectArticle").disabled = false;
        send({"method": "requestArticle"});
    }

    function selectArticle() {
        updateGameBar("Article Selected");
        document.getElementById("requestArticle").disabled = true;
        document.getElementById("selectArticle").disabled = true;
        send({"method": "reset"});
        var item = articles[Math.floor(Math.random()*articles.length)];
        updateGameBar(item);
    }

    function submitArticle() {
        title = document.getElementById("selectedArticle").innerHTML;
        //title = "Banana";
        status = "Submitted";
        document.getElementById("submitArticle").disabled = true;
        document.getElementById("randomArticle").disabled = true;
        send({"method": "submitArticle", "title": title});

    }

    function randomArticle() {
        fetch("random.php").then(function(response) {
            return response.json();
        }).then(function(data) {
            console.log(data);
            document.getElementById("selectedArticle").innerHTML = data["title"];
            document.getElementById("selectedArticleURL").innerHTML = data["url"];
            document.getElementById("articlePreview").src = data["url"];
            document.getElementById("submitArticle").disabled = false;


        }).catch(function() {
            console.log("HTTP Error");
        });
    }

    function reset() {
        send({"method": "reset"});
        updateGameBar("Ready to Play");
        document.getElementById("requestArticle").disabled = false;
        document.getElementById("selectArticle").disabled = true;
        articles = [];
    }

    function updatePlayerTable() {
        document.getElementById("playerTable").innerHTML = "";
        for (let i = 0, len = connList.length; i < len; i++) {
            connList[i].send({"method": "getStatus"});
        }
    }

    function send(message) {
        for (let i = 0, len = connList.length; i < len; i++) {
            connList[i].send(message);
        }
    }

    function proxy(sender, message) {
        for (let i = 0, len = connList.length; i < len; i++) {
            if (connList[i].peer != sender) {
                connList[i].send(message);
            }
        }
    }

    peer.on('open', function(id) {
        console.log('My peer ID is: ' + id);
        myID = id;

        document.getElementById("myID").innerHTML = myID;
        document.getElementById("status").innerHTML = "Connecting...";
        if (role == "host") {
            document.getElementById("hostID").innerHTML = myID;
            document.getElementById("status").innerHTML = "Hosting";
            document.getElementById("playerTableParent").hidden = false;
            document.getElementById("articleSelection").hidden = true;
            document.getElementById("hostControls").hidden = false;
            document.getElementById("selectArticle").disabled = true;
            status = "Host";

        } else {
            document.getElementById("playerTableParent").hidden = false;
            document.getElementById("submitArticle").disabled = true;
            document.getElementById("randomArticle").disabled = true;
            document.getElementById("hostID").innerHTML = host;
            connList.push(peer.connect(host));
            document.getElementById("status").innerHTML = "Connected";
            status = "Idle"
            connList[0].on('data', function(data) {
                    console.log('Received', data);
                    handleData(connList[0], data);
            });
            setTimeout(function() {
                send({"method": "update"});
                updatePlayerTable();
            }, 1000);
            setTimeout(function() {
                send({"method": "update"});
                updatePlayerTable();
            }, 5000);
        }
    });

    peer.on('connection', function(conn) {
                newConn(conn);
    });

    const interval = setInterval(function() {
                // method to be executed;
                updatePlayerTable();
            }, 30000);

</script>    
</body>
</html>
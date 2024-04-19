<html>
    <head>
        <title>main</title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>  
        <script>
            function funcBefore () {
                $("#information").text ("Ожидание данных...");
            }

            function funcSuccess (data) {
                $("#information").html (data);
            }

            $(document).ready (function () {
                $("#load").bind("click", function () {
                    var admin = "Admin";
                    $.ajax ({
                        url: "processing.php",
                        type: "POST",
                        data: ({name: admin, number: 5}),
                        dataType: "html",
                        beforeSend: funcBefore,
                        success: funcSuccess
                    });
                });
            });
        </script>
    </head>
    <body>
        <p id="load" style="cursor:pointer">Загрузить данные</p>
        <p id="information"></p>
    </body>
</html>

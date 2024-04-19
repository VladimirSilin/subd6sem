<title>choice2</title>
<?php

$conn = mysqli_connect("localhost", "root", "", "DataBase");

if ($conn == false)
{
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else
{
    $sql = 'SELECT Saldo, Month, Year FROM Accounts WHERE ID_Account = ' . $_POST["id_account"] . ' AND Year = ' . $_POST["year"];

    $result = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);

    if ($result == false) 
    {
        print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
    }
    else
    {
        if ($result->num_rows > 0)
        {
            $row_a = $result->fetch_assoc();

            $saldo = $row_a["Saldo"];

            $sql = 'SELECT Charge FROM Charges WHERE ID_Account = ' . $_POST["id_account"] . " AND Year = " . $row_a["Year"];

            $result = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);

            if ($result == false) 
            {
                print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
            }
            else
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $saldo += $row["Charge"];
                    }
                }
            }

            $sql = 'SELECT Payment FROM Payments WHERE ID_Account = ' . $_POST["id_account"] . " AND Year = " . $row_a["Year"];

            $result = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);

            if ($result == false) 
            {
                print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
            }
            else
            {
                if ($result->num_rows > 0)
                {
                    while ($row = $result->fetch_assoc())
                    {
                        $saldo -= $row["Payment"];
                    }
                }
            }

            echo "<table border='1'><tr><th>Входное сальдо</th><th></th>";
            for ($month = $row_a["Month"]; $month < 13; $month++)
            {
                echo "<th>".$month."</th>";
            }
            echo "<th>Итоговое сальдо</th>";

            echo "<tr><td rowspan='2'>".$row_a["Saldo"]."</td><td>Начисление</td>";

            for ($month = $row_a["Month"]; $month < 13; $month++)
            {
                $sql = 'SELECT Charge FROM Charges WHERE ID_Account = ' . $_POST["id_account"] . " AND Year = " . $row_a["Year"] . " AND Month = " . $month;

                $result = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);

                if ($result == false) 
                {
                    print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
                }
                else
                {
                    if ($result->num_rows > 0)
                    {
                        $row = $result->fetch_assoc();
                        echo "<td>".$row["Charge"]."</td>";
                    }
                    else
                    {
                        echo "<td>0.00</td>";
                    }
                }

            }

            echo "<td rowspan='2'>".$saldo."</td></tr>";
            echo "<tr><td>Оплата</td>";

            for ($month = $row_a["Month"]; $month < 13; $month++)
            {
                $sql = 'SELECT Payment FROM Payments WHERE ID_Account = ' . $_POST["id_account"] . " AND Year = " . $row_a["Year"] . " AND Month = " . $month;

                $result = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);

                if ($result == false) 
                {
                    print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
                }
                else
                {
                    if ($result->num_rows > 0)
                    {
                        $payment = 0;
                        while ($row = $result->fetch_assoc())
                        {
                            $payment += $row["Payment"];
                        }
                        
                        echo "<td>".$payment."</td>";
                    }
                    else
                    {
                        echo "<td>0.00</td>";
                    }
                }

            }

            echo "</table>";
        }
        else
        {
            echo "<p>Лицевой счёт не существует</p>";
        }
    }
    
}

$conn->close();

?>
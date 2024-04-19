<?php

$conn = mysqli_connect("localhost", "root", "", "DataBase");

if ($conn == false)
{
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else
{
    $sql = 'SELECT * FROM Accounts WHERE Year = ' . $_POST["year"];

    $result = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);

    if ($result == false) 
    {
        print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
    }
    else
    {
        echo "<table border='1'>";

        // Шапка таблицы
        echo "<tr>";
        echo "<th>Квартира</th>";
        echo "<th>Входное сальдо</th>";
        
        echo "<th>Январь</th>";
        echo "<th>Февраль</th>";
        echo "<th>Март</th>";
        echo "<th>Апрель</th>";
        echo "<th>Май</th>";
        echo "<th>Июнь</th>";
        echo "<th>Июль</th>";
        echo "<th>Август</th>";
        echo "<th>Сентябрь</th>";
        echo "<th>Октябрь</th>";
        echo "<th>Ноябрь</th>";
        echo "<th>Декабрь</th>";

        echo "<th>Исходное сальдо</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) // проходим по всем квартирам
        {
            $array_charges = [];
            $array_payments_sum = [];
            $array_saldo = [];

            for ($month = $row["Month"]; $month < 13; $month++)
            {
                $sql_c = 'SELECT Charge FROM Charges WHERE ID_Account = ' . $row["ID_Account"] . " AND Year = " . $_POST["year"] . " AND Month = " . $month;

                $result_c = mysqli_query($conn, $sql_c, MYSQLI_STORE_RESULT);

                if ($result_c == false) 
                {
                    print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
                }
                else
                {
                    if ($result_c->num_rows > 0)
                    {
                        $row_c = $result_c->fetch_assoc();
                        $array_charges[$month] = $row_c["Charge"];
                    }
                    else
                    {
                        $array_charges[$month] = 0;
                    }
                } 
            }

            for ($month = $row["Month"]; $month < 13; $month++)
            {
                $sql_p = 'SELECT Payment FROM Payments WHERE ID_Account = ' . $row["ID_Account"] . " AND Year = " . $_POST["year"] . " AND Month = " . $month;

                $result_p = mysqli_query($conn, $sql_p, MYSQLI_STORE_RESULT);

                if ($result_p == false) 
                {
                    print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
                }
                else
                {
                    if ($result_p->num_rows > 0)
                    {
                        $payment = 0;
                        while ($row_p = $result_p->fetch_assoc())
                        {
                            $payment += $row_p["Payment"];
                        }
                        
                        $array_payments_sum[$month] = $payment;
                    }
                    else
                    {
                        $array_payments[$month] = 0;
                    }
                }
            }

            for ($month = $row["Month"]; $month < 13; $month++)
            {
                if ($array_charges[$month] == 0)
                {
                    $array_saldo[$month] = 0;
                }
                else 
                {
                    $saldo;
                    if ($month == $row["Month"])
                    {
                        $saldo = $row["Saldo"];
                    }
                    else
                    {
                        $saldo = $array_saldo[$month-1];
                    }
                    
                    $saldo += $array_charges[$month];
                    $saldo -= $array_payments_sum[$month];

                    $array_saldo[$month] = $saldo;
                }
            }

            echo "<tr>"; // открываем строку

            echo "<td rowspan='3'>".$row["ID_Account"]."</td>";
            echo "<td rowspan='3'>".$row["Saldo"]."</td>";

            for ($month = 1; $month < $row["Month"]; $month++)
            {
                echo "<td rowspan='3'>-</td>";
            }

            for ($month = $row["Month"]; $month < 13; $month++)
            {

                if ($array_charges[$month] == 0)
                {
                    echo "<td>0.00</td>";
                }
                else
                {
                    echo "<td>".$array_charges[$month]."</td>";
                }
            }

            if ($array_saldo[12] == 0)
            {
                echo "<td rowspan='3'>0.00</td>";
            }
            else
            {
                echo "<td rowspan='3'>".$array_saldo[12]."</td>";
            }
            echo "</tr>";

            for ($month = $row["Month"]; $month < 13; $month++)
            {
                $sql_p = 'SELECT Payment, Day FROM Payments WHERE ID_Account = ' . $row["ID_Account"] . " AND Year = " . $_POST["year"] . " AND Month = " . $month;

                $result_p = mysqli_query($conn, $sql_p, MYSQLI_STORE_RESULT);

                if ($result_p == false) 
                {
                    print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
                }
                else
                {
                    if ($result_p->num_rows > 0)
                    {
                        echo "<td>";
                        while ($row_p = $result_p->fetch_assoc())
                        {
                            echo $row_p["Day"]." day - ".$row_p["Payment"];
                            echo "<br>";
                        }
                        echo "</td>";
                        
                    }
                    else
                    {
                        echo "<td>0.00</td>";
                    }
                }
            }
            echo "</tr>";

            for ($month = $row["Month"]; $month < 13; $month++)
            {
                if ($array_saldo[$month] == 0)
                {
                    echo "<td>0.00</td>";
                }
                else
                {
                    echo "<td>".$array_saldo[$month]."</td>";
                }
            }
            echo "</tr>"; // закрываем строку
        }


        echo "</table>";
        
    }
}

$conn->close();

?>
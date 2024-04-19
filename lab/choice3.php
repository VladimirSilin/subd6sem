<?php
$conn = mysqli_connect("localhost", "root", "", "DataBase");

if ($conn == false)
{
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else
{
    echo "<p>Состояние на 01.";
    if ($_POST["month"] < 10)
    {
        echo "0".$_POST["month"];
    }
    else
    {
        echo $_POST["month"];
    }
    echo ".".$_POST["year"]."</p>";

    echo "<table border='1'>";
    ?>

    <!-- Шапка таблицы -->
    <tr>
        <th rowspan='2'>Квартира</th>
        <th rowspan='2'>Начислено за последний месяц</th>
        <th rowspan='2'>Сальдо</th>
        <th colspan='4'>Задолженность</th>
    </tr>
    
    <tr>
        <td>1 месяц</td>
        <td>2 месяца</td>
        <td>3 месяца</td>
        <td>Свыше 3 месяцев</td>
    </tr>

    <?php
    echo "</table";
    
    $sql = 'SELECT * FROM Accounts';

    $result = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);

    if ($result == false) 
    {
        print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
    }
    else
    {
        while ($row = $result->fetch_assoc()) // проходим по всем квартирам
        {
            if ($row["Month"] >= $_POST["month"])
            {
                continue;
            }

            $last_charge = 0;
            $last_month = 0;
            $array_charges = [];
            $last_saldo = $row["Saldo"];
            

            $sql_c = 'SELECT Charge, Month FROM Charges WHERE ID_Account = '.$row["ID_Account"].' AND Year = '.$_POST["year"].' AND Month < '.$_POST["month"];

            $result_c = mysqli_query($conn, $sql_c, MYSQLI_STORE_RESULT);

            if ($result == false) 
            {
                print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
            }
            else
            {
                while ($row_c = $result_c->fetch_assoc())
                {
                    if ($row_c["Month"] > $last_month)
                    {
                        $last_month = $row_c["Month"];
                        $last_charge = $row_c["Charge"];
                    }
                    $last_saldo += $row_c["Charge"];
                }
            }

            $sql_p = 'SELECT Payment FROM Payments WHERE ID_Account = '.$row["ID_Account"].' AND Year = '.$_POST["year"].' AND Month < '.$_POST["month"];

            $result_p = mysqli_query($conn, $sql_p, MYSQLI_STORE_RESULT);

            if ($result_p == false) 
            {
                print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
            }
            else
            {
                while ($row_p = $result_p->fetch_assoc())
                {
                    $last_saldo -= $row_p["Payment"];
                }
            }
            
            $debt = $last_saldo/$last_charge;

            if ($debt < 1)
            {
                continue;
            }
            else
            {
                echo "<tr>";
                echo "<td>".$row["ID_Account"]."</td>";
                echo "<td>".$last_charge."</td>";
                echo "<td>".$last_saldo."</td>";
                if ($debt < 2)
                {
                    echo "<td>".$last_saldo."</td><td></td><td></td><td></td>";
                }
                else if ($debt < 3)
                {
                    echo "<td></td><td>".$last_saldo."</td><td></td><td></td>";
                }
                else if ($debt < 4)
                {
                    echo "<td></td><td></td><td>".$last_saldo."</td><td></td>";
                }
                else
                {
                    echo "<td></td><td></td><td></td><td>".$last_saldo."</td>";
                }

                echo "</tr>";
            }
            
        }
    }
}

$conn->close();

?>
<?php

// if (isset($_POST["year"]) && isset($_POST["month"]) && isset($_POST["min"])&& isset($_POST["max"])) { 

// 	// Формируем массив для JSON ответа
//     $result = array(
//     	'year' => $_POST["year"],
//     	'month' => $_POST["month"],
//         'min' => $_POST["min"],
//         'max' => $_POST["max"]
//     ); 

//     // // Переводим массив в JSON
//     // echo json_encode($result); 
// }

$conn = mysqli_connect("localhost", "root", "", "DataBase");

if ($conn == false)
{
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else
{

    $sql = 'SELECT * FROM Charges WHERE ID_Account >= ' . $_POST["min"] . ' AND ID_Account <= ' . $_POST["max"] . ' AND Year = ' . $_POST["year"] . ' AND Month = ' . $_POST["month"];

    $result = mysqli_query($conn, $sql, MYSQLI_STORE_RESULT);

    if ($result == false) 
    {
        print("Произошла ошибка при выполнении запроса") . mysqli_error($conn);
    }
    else
    {
        echo "<table border='1'>";
        ?>

        <!-- Шапка таблицы -->
        <tr>
            <th>Дата</th>
            <th>Квартира</th>
            <th>Сумма</th>
        </tr>

        <?php
        
        while ($row = $result->fetch_assoc())
        {
            

            echo "<tr>";
                echo "<td>".$row["Month"].".".$row["Year"]."</td>";
                echo "<td>".$row["ID_Account"]."</td>";
                echo "<td>".$row["Charge"]."</td>";
            echo "</tr>";

            
        }
        echo "</table";
    }
}

$conn->close();


?>
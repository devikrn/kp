<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$counts = array(); 
$excelData = array();
if (isset($_POST['submit'])) {
    // Check ada file yang diupload atau gk
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Define nama kolom yang mau diitung
        $columnToCount = $_POST['processingOption'];
        
        // Ambil path file yang diupload
        $uploadedFilePath = $_FILES['file']['tmp_name'];

        try {
            // Load file Excel yang diupload
            $spreadsheet = IOFactory::load($uploadedFilePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            $result = array_slice($data, 2);
            $header = $result[0];
            array_shift($result);

            $all_data = array_slice($data, 1);
            $all_header = $all_data[0];
            array_shift($all_data);
            
            // Perhitungan jumlah data yang sama pake count if
            $counts = array_count_values(array_column($result, array_search($columnToCount, $header)));

            // Store Excel data
            $excelData = $all_data;
        } catch (Exception $e) {
            echo "Error reading the Excel file: " . $e->getMessage();
        }
    } else {
        echo "No file uploaded or an error occurred during upload.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Countif Web App</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style.css" />
    <style>
        .visually-hidden {
            display: none;
        }
    </style>
</head>
<body>
<div id="wrapper">
        <div class="hero">
            <div class="inner-wrapper">
                <img class="toplogo" src="images/toplogo.svg">
                <div class="header-middle">
                    <div class="animasi">
                        <img src="./images/animasi.gif" alt="animasi">
                        <h1 class="header-text">SORTING WORK ORDER</h1>
                        <h2 class="cek">Cek WO</h2>
                    </div>
                    <div class="unggah">
                        <form action="process.php" method="post" enctype="multipart/form-data">
                            <label for="file">Choose an Excel file to upload:</label>
                            <input type="file" id="file" name="file" accept=".xlsx, .xls, .csv">

                            <input type="radio" id="optionA" name="processingOption" value="Complete MH">
                            <label for="optionA">Complete MH</label>
                            <input type="radio" id="optionB" name="processingOption" value="Complete Log">
                            <label for="optionB">Complete Log</label><br>
                            <button type="submit" name="submit">Upload dan Process</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (!empty($counts)) {
        echo "<h2>Count Results:</h2>";
        echo '<table border="1" style=" color: white;">';
        echo "<tr>";
        foreach ($counts as $status => $count) {
            echo "<th>{$status}</th>";
        }
        echo "</tr>";
        echo "<tr>";
        foreach ($counts as $status => $count) {
            echo "<td>{$count}</td>";
        }
        echo "</tr>";
        echo "</table>";
    }
    ?>
    <section id="listorders" class="" style="padding-bottom: 15vh; background-color: grey; color: white;">
    <?php
    $counter = 0;
    if (!empty($excelData)) {
        echo "<h2>Excel Data:</h2>";
        echo '<input class="" type="text" id="myInput" onkeyup="debouncedSearch()" placeholder="Search...">';
        echo "<table border='1'>";
        foreach ($excelData as $row) {
            echo '<tr id="kartu-'.$counter.'" class="card">';
            foreach ($row as $cell) {
                echo "<td>{$cell}</td>";
            }
            echo "</tr>";
            $counter++;
        }
        echo "</table>";
    }
    ?>
    </section>
    <script>
        let debounceTimer;

        function debounce(func, delay) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(func, delay);
        }
        function search() {
            // mendapatkan inputan pencarian
            var input, filter, listorders, kartu, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            listorders = document.getElementById("listorders");
            kartu = listorders.getElementsByClassName("card");
            
            // melakukan iterasi pada setiap kartu
            for (i = 0; i < kartu.length; i++) {
                txtValue = kartu[i].textContent || kartu[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    // jika sesuai dengan pencarian, hapus class visually-hidden
                    kartu[i].classList.remove("visually-hidden");
                } else {
                    // jika tidak sesuai dengan pencarian, tambahkan class visually-hidden
                    kartu[i].classList.add("visually-hidden");
                }
            }
        }
        const debouncedSearch = () => debounce(search, 1000);
    </script>
</body>
</html>

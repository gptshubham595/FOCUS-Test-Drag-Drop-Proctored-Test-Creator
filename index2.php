<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', false);
/*
require_once __DIR__.'\SimpleXLSX.php';

if ( $xlsx = SimpleXLSX::parse('test.xlsx') ) {
	print_r( $xlsx->rows() );
    
} else {
	echo SimpleXLSX::parseError();
}*/

set_include_path(__DIR__ . '/phpexcel/Classes/');
include __DIR__ . '/phpexcel/Classes/PHPExcel/IOFactory.php';
$file = __DIR__ . '/test.xlsx';

$inputFileType = PHPExcel_IOFactory::identify($file);
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load($file);
$objWorksheet = $objPHPExcel->getActiveSheet();
$CurrentWorkSheetIndex = 0;
// header('Content-Type: text/csv');
// header('Content-Disposition: attachment; filename="sample.csv"');
function store_Result($file_name, $line)
{   //echo '<script>alert(1)</script>';
    $fp = fopen($file_name, 'a'); //opens file in append mode  
    fwrite($fp, $line);
    fclose($fp);
    //echo '<script>alert(2)</script>';
}

if (isset($_POST['name'])) {
    $name = $_POST['name'];
    $std = $_POST['std'];
    $phy_A = $_POST['phy_attempted'];
    $phy_C = $_POST['phy_correct'];
    
    $chem_A = $_POST['chem_attempted'];
    $chem_C = $_POST['chem_correct'];

    $math_A = $_POST['math_attempted'];
    $math_C = $_POST['math_correct'];

    $total_A=$math_A+$phy_A+$chem_A;
    $total_C=$math_C+$phy_C+$chem_C;
    $total_I=$total_A-$total_C;

    //PA,PC,PW,CA,CC,CW,MA,MC,MW,TOTAL
    $total_marks = $_POST['total_marks'];
    $line = $name . "," . $std . "," .$phy_A. "," .$phy_C. "," .$chem_A. "," .$chem_C. "," .$math_A. "," .$math_C. "," .$total_A. "," .$total_C. "," .$total_I. "," . $total_marks . "\n";
    store_Result("Results FOCUS " . date("Y.m.d") . " .csv", $line);
    echo '<script>alert("You GOT TOTAL MARKS='.$total_marks.'")</script>';
}

function isInAP($a, $d, $x)
{
    if ($d == 0) {
        return ($x == $a) ? 1 : 0;
    }
    return (($x - $a) % $d == 0 && ($x - $a) / $d >= 0) ? 1 : 0;
}
//  isInAP(1,3,7);
$marks = array();
$phy_single = array();
$phy_multi = array();
$phy_integer = array();
$phy_match = array();

$chem_single = array();
$chem_multi = array();
$chem_integer = array();
$chem_match = array();

$math_single = array();
$math_multi = array();
$math_integer = array();
$math_match = array();
$partial = 0;


foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

    // echo ("<br/><br/>");
    // echo 'WorkSheet' . $CurrentWorkSheetIndex++ . "\n";
    // echo 'Worksheet:', $objPHPExcel->getIndex($worksheet), PHP_EOL;
    $highestRow = $worksheet->getHighestDataRow();
    $highestColumn = $worksheet->getHighestDataColumn();
    $headings = $worksheet->rangeToArray('A1:' . $highestColumn . 1, '', true, false);
    $positive = $headings[0][1];
    $negative = $headings[0][2];

    if ($objPHPExcel->getIndex($worksheet) != 1) {
        $questions_each = $headings[0][3];
    } else {
        $partial = $headings[0][3];
        $questions_each = $headings[0][4];
    }

    // echo ("TOTAL=" . $questions_each);
    if ($questions_each < 1) {
        array_push($marks, 0, 0);
        continue;
    } else {
        array_push($marks, $positive, $negative);
    }
    // echo ("<br/>");
    switch ($objPHPExcel->getIndex($worksheet)) {
        case 0:
            //SINGLE
            for ($i = 0; $i < 3; $i++) {
                for ($row = 3 + $i * ($questions_each + 1); $row <= 3 + $i * ($questions_each + 1) + $questions_each - 1; $row++) {
                    $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, '  ', true, false);
                    $v = $rowData[0][0];
                    switch ($i) {
                        case 0:
                            array_push($phy_single, $v);
                            break;
                        case 1:
                            array_push($chem_single, $v);
                            break;
                        case 2:
                            array_push($math_single, $v);
                            break;
                    }
                }
            }
            break;
        case 1:
            //MULTI
            for ($i = 0; $i < 3; $i++) {
                for ($row = 3 + $i * ($questions_each + 1); $row <= 3 + $i * ($questions_each + 1) + $questions_each - 1; $row++) {
                    $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, '  ', true, false);
                    $v = $rowData[0][0];
                    switch ($i) {
                        case 0:
                            array_push($phy_multi, $v);
                            break;
                        case 1:
                            array_push($chem_multi, $v);
                            break;
                        case 2:
                            array_push($math_multi, $v);
                            break;
                    }
                }
            }
            break;
        case 2:
            //INTEGER
            for ($i = 0; $i < 3; $i++) {
                for ($row = 3 + $i * ($questions_each + 1); $row <= 3 + $i * ($questions_each + 1) + $questions_each - 1; $row++) {
                    $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, '  ', true, false);
                    $v = $rowData[0][0];
                    switch ($i) {
                        case 0:
                            array_push($phy_integer, $v);
                            break;
                        case 1:
                            array_push($chem_integer, $v);
                            break;
                        case 2:
                            array_push($math_integer, $v);
                            break;
                    }
                }
            }
            break;
        case 3:
            //MATCH
            for ($i = 0; $i < 3; $i++) {
                for ($row = 3 + $i * ($questions_each + 1); $row <= 3 + $i * ($questions_each + 1) + $questions_each - 1; $row++) {
                    $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, '  ', true, false);
                    $v = $rowData[0][0];
                    switch ($i) {
                        case 0:
                            array_push($phy_match, $v);
                            break;
                        case 1:
                            array_push($chem_match, $v);
                            break;
                        case 2:
                            array_push($math_match, $v);
                            break;
                    }
                }
            }
            break;
    }
}
$marks = json_encode($marks);
$phy_single = json_encode($phy_single);
$phy_multi = json_encode($phy_multi);
$phy_integer = json_encode($phy_integer);
$phy_match = json_encode($phy_match);


$math_single = json_encode($math_single);
$math_multi = json_encode($math_multi);
$math_integer = json_encode($math_integer);
$math_match = json_encode($math_match);

$chem_single = json_encode($chem_single);
$chem_multi = json_encode($chem_multi);
$chem_integer = json_encode($chem_integer);
$chem_match = json_encode($chem_match);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
    <title>TEST</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous" />
    
    <style>
        body {
            overflow: hidden;
        }

        .box {
            width: 50px;
            margin: 5px;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 20%;
            /* Could be more or less, depending on screen size */
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body >
    <button class="btn-danger" id="submit_btn" style="margin-left:80vw; margin-top:2vh; width:150px;">SUBMIT</button>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <!-- <embed src="test.pdf" style="
              width: 60vw;
              height: 85vh;
              margin-top: 3vw;
              margin-left: 5vw;
              margin-right: 0;
            " /> <iframe id="fraDisabled" src="test.pdf" frameborder="0" style="
              width: 60vw;
              height: 85vh;
              margin-top: 3vw;
              margin-left: 5vw;
              margin-right: 0;
            ">
                </iframe>-->
                <iframe id="fraDisabled" src="test.pdf" style="
              width: 60vw;
              height: 85vh;
              margin-top: 3vw;
              margin-left: 5vw;
              margin-right: 0;
            "></iframe>
            </div>
            <div class="col" id="test_box" style="
            margin-top: 3vw;
            margin-right: 5vw;
            height: 85vh;
            background-color: #00ff99;
            overflow-y: scroll;
            width: 60vw;
          ">
                <div class="col">
                    <label id="q1">Q1)</label><input class="box" /> <label>Q2)</label><input class="box" />
                </div>
                <div class="col">
                    <label>Q3)</label><input class="box" /> <label>Q4)</label><input class="box" />
                </div>
            </div>
        </div>

    </div>
    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" style="margin-left:90%;">&times;</span>
            <br />
            <form method="POST" action="/index2.php">
                <input id="name" name="name" placeholder="Enter Name.." />
                <br />
                <input id="std" name="std" list="datalist" placeholder="Enter CLASS/STD.." />
                <datalist id="datalist">
                    <option value="11">
                    <option value="12">
                    <option value="13">
                </datalist>
                <br />
                <input id="phy_attempted" name="phy_attempted" style="display:none;" />
                <input id="chem_attempted" name="chem_attempted" style="display:none;" />
                <input id="math_attempted" name="math_attempted" style="display:none;" />
                <input id="phy_correct" name="phy_correct" style="display:none;" />
                <input id="chem_correct" name="chem_correct" style="display:none;" />
                <input id="math_correct" name="math_correct" style="display:none;" />

                <input id="total_marks" name="total_marks" style="display:none;" />
                <br />
                <button class="btn-danger" id="submit" style="width:150px;">CONFIRM SEND!</button>
            </form>

            <br />

        </div>

    </div>

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        function isSubsequence(str1, str2) {
            let i = 0;
            let j = 0;
            while (i < str1.length) {
                if (j === str2.length) {
                    return false;
                }
                if (str1[i] === str2[j]) {
                    i++;
                }
                j++;
            };
            return true;
        };
        var sub = ["PHYSICS", "CHEMISTRY", "MATHS"];
        var k = "<Center>";
        var marks = <?php echo $marks ?>;

        //=======================CORRECT ANS==========================
        var phy_single_correct = <?php echo ($phy_single) ?>;
        var phy_multi_correct = <?php echo ($phy_multi) ?>;
        var phy_integer_correct = <?php echo ($phy_integer) ?>;
        var phy_match_correct = <?php echo ($phy_match) ?>;


        var math_single_correct = <?php echo ($math_single) ?>;
        var math_multi_correct = <?php echo ($math_multi) ?>;
        var math_integer_correct = <?php echo ($math_integer) ?>;
        var math_match_correct = <?php echo ($math_match) ?>;

        var chem_single_correct = <?php echo ($chem_single) ?>;
        var chem_multi_correct = <?php echo ($chem_multi) ?>;
        var chem_integer_correct = <?php echo ($chem_integer) ?>;
        var chem_match_correct = <?php echo ($chem_match) ?>;

        var phy_attempted = 0;
        var phy_correct = 0;
        var math_attempted = 0;
        var math_correct = 0;
        var chem_attempted = 0;
        var chem_correct = 0;

        var analysis=[phy_attempted,phy_correct,phy_attempted-phy_correct,chem_attempted,chem_correct,chem_attempted-chem_correct,math_attempted,math_correct,math_attempted-math_correct];
        //analysis = PA,PC,PW,CA,CC,CW,MA,MC,MW,TOTAL
        //analysis = 0 ,1 ,2 ,3 ,4 ,5 ,6 ,7 ,8 ,9

        var single_correct = [phy_single_correct, chem_single_correct, math_single_correct];
        var multi_correct = [phy_multi_correct, chem_multi_correct, math_multi_correct];
        var integer_correct = [phy_integer_correct, chem_integer_correct, math_integer_correct];
        var match_correct = [phy_match_correct, chem_match_correct, math_match_correct];
        //============================================================        

        var partial = <?php echo $partial ?>;

        var single = <?php echo $phy_single ?>;
        single = single.length;
        var multiple = <?php echo $phy_multi ?>;
        multiple = multiple.length;
        var integer = <?php echo $phy_integer ?>;
        integer = integer.length;
        var match = <?php echo $phy_match ?>;
        match = match.length;
        for (var t = 0; t < 3; t++) {
            k += "<br/>===========================<br/>";
            k += "<div class='" + sub[t].toLocaleLowerCase() + "'><h2>" + sub[t] + "</h2>";
            //===============================SINGLE===================================
            if (single > 0) {
                k += "<br/><h3> SINGLE CORRECT</h3>";
                k +=
                    "<div class='col' id='" + sub[t].toLocaleLowerCase() + "_single'style='background-color:#caebd8;border-radius:5px;'>";
                for (var i = 0; i < single - 1; i += 2) {
                    k += "<div class='col'>";
                    for (var j = i + 1; j < i + 3; j++) {
                        k +=
                            "<label class='box'> Q" +
                            j +
                            ") </label><input onkeypress='return /[A-E]/i.test(event.key)'   style='text-transform:uppercase'  maxlength='1' class='box' />";
                    }
                    k += "</div>";
                }
                if (single & 1) {
                    k += "<div class='col'>";
                    k +=
                        "<label class='box'> Q" +
                        single +
                        ") </label><input  onkeypress='return /[A-E]/i.test(event.key)' style='text-transform:uppercase'  maxlength='1' class='box' />";
                    k += "</div>";
                }
                k += "</div>";
            }
            //==============================================================================
            //=============================MULTIPLE=========================================
            if (multiple > 0) {
                k += "<br/><h3> MULTIPLE CORRECT</h3>";
                k +=
                    "<div class='col'  id='" + sub[t].toLocaleLowerCase() + "_multi' style='background-color:#caebd8; border-radius:5px;'>";

                for (var i = 0; i < multiple - 1; i += 2) {
                    k += "<div class='col'>";
                    for (var j = i + 1; j < i + 3; j++) {
                        k +=
                            "<label class='box'> Q" +
                            j +
                            ") </label><input onkeypress='return /[A-E]/i.test(event.key)'  style='text-transform:uppercase'  maxlength='5' class='box' />";
                    }
                    k += "</div>";
                }
                if (multiple & 1) {
                    k += "<div class='col'>";
                    k +=
                        "<label class='box'> Q" + multiple + ") </label><input class='box' />";
                    k += "</div>";
                }
                k += "</div>";
            }
            //==============================================================================
            //====================================INTEGER===================================
            if (integer > 0) {
                k += "<br/><h3>INTEGER TYPE</h3>";
                k +=
                    "<div class='col'  id='" + sub[t].toLocaleLowerCase() + "_integer' style='background-color:#caebd8; border-radius:5px;'>";

                for (
                    var i = 0; i < integer - 1; i += 2
                ) {
                    k += "<div class='col'>";
                    for (var j = i + 1; j < i + 3; j++) {
                        k +=
                            "<label class='box'> Q" +
                            j +
                            ") </label><input onkeypress='return /[0-9.]/i.test(event.key)'   maxlength='6' class='box' />";
                    }
                    k += "</div>";
                }
                if (integer & 1) {
                    var to = integer;
                    k += "<div class='col'>";
                    k +=
                        "<label class='box'> Q" +
                        to +
                        ") </label><input onkeypress='return /[0-9.]/i.test(event.key)'   maxlength='6' class='box' />";
                    k += "</div>";
                }
                k += "</div>";
            }
            //==============================================================================
            //====================================MATCH===================================
            if (match > 0) {
                k += "<br/><h3>MATCH TYPE</h3>";
                k +=
                    "<div class='col'  id='" + sub[t].toLocaleLowerCase() + "_match'style='background-color:#caebd8; border-radius:5px; '>";

                for (
                    var i = 0; i < match - 1; i += 2
                ) {
                    k += "<div class='col'>";
                    for (var j = i + 1; j < i + 3; j++) {
                        k +=
                            "<label class='box'> Q" +
                            j +
                            ") </label><input onkeypress='return /[A-H]/i.test(event.key)'   maxlength='6' class='box' />";
                    }
                    k += "</div>";
                }
                if (match & 1) {
                    var to = match;
                    k += "<div class='col'>";
                    k +=
                        "<label class='box'> Q" +
                        to +
                        ") </label><input onkeypress='return /[A-H]/i.test(event.key)'   maxlength='6' class='box' />";
                    k += "</div>";
                }
                k += "</div>";

            }
            //==============================================================================
            k += "</div>";
        }
        k += "<br/>"
        k += "</center>";
        $("#test_box").html(k);
        var total_marks = 0;
        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];

        span.onclick = function() {
            modal.style.display = "none";
        }
        $("#submit_btn").click(function() {
            modal.style.display = "block";
            //marks
            total_marks = 0;
            for (var t = 0; t < 3; t++) {
                if ($("." + sub[t].toLocaleLowerCase()).find("div#" + sub[t].toLocaleLowerCase() + "_single").length) {

                    //=========================PHY SINGLE=============================
                    var q = $("." + sub[t].toLocaleLowerCase()).find("div#" + sub[t].toLocaleLowerCase() + "_single").find("input");
                    for (var i = 0; i < q.length; i++) {
                        if (q[i].value == "") continue;
                        if (q[i].value == single_correct[t][i]) {
                            total_marks += marks[0];
                            //analysis = PA,PC,PW,CA,CC,CW,MA,MC,MW,TOTAL  0,3,6  
                            //analysis = 0 ,1 ,2 ,3 ,4 ,5 ,6 ,7 ,8 ,9
                            analysis[t*3]++;
                            analysis[t*3+1]++;
                        } else {
                            total_marks += marks[1];
                            analysis[t*3]++;
                            analysis[t*3+2]++;
                        }
                        // console.log(total_marks);
                    }
                    //==============================================================

                }
                if ($("." + sub[t].toLocaleLowerCase()).find("div#" + sub[t].toLocaleLowerCase() + "_multi").length) {
                    //=========================PHY MULTI=============================
                    var q = $("." + sub[t].toLocaleLowerCase()).find("div#" + sub[t].toLocaleLowerCase() + "_multi").find("input");
                    for (var i = 0; i < q.length; i++) {
                        if (q[i].value == "") continue;

                        if (q[i].value == multi_correct[t][i]) {
                            total_marks += marks[2];
                            analysis[t*3]++;
                            analysis[t*3+1]++;
                        } else if (isSubsequence(q[i].value, phy_multi_correct[i])) {
                            total_marks += q[i].value.length;
                            // console.log(q[i].value.length);
                            analysis[t*3]++;
                            analysis[t*3+1]++;
                        } else {
                            total_marks += marks[3];
                            analysis[t*3]++;
                            analysis[t*3+2]++;
                        }
                        // console.log(total_marks);
                    }
                    //==============================================================

                }
                if ($("." + sub[t].toLocaleLowerCase()).find("div#" + sub[t].toLocaleLowerCase() + "_integer").length) {
                    //=========================PHY INTEGER=============================
                    var q = $("." + sub[t].toLocaleLowerCase()).find("div#" + sub[t].toLocaleLowerCase() + "_multi").find("input");
                    for (var i = 0; i < q.length; i++) {
                        if (q[i].value == "") continue;
                        if (q[i].value == integer_correct[t][i]) {
                            total_marks += marks[4];
                            analysis[t*3]++;
                            analysis[t*3+1]++;
                        } else {
                            total_marks += marks[5];
                            analysis[t*3]++;
                            analysis[t*3+2]++;
                        }
                        // console.log(total_marks);
                    }
                    //==============================================================

                }
                if ($("." + sub[t].toLocaleLowerCase()).find("div#" + sub[t].toLocaleLowerCase() + "_match").length) {
                    //=========================PHY MATCH=============================
                    var q = $("." + sub[t].toLocaleLowerCase()).find("div#" + sub[t].toLocaleLowerCase() + "_match").find("input");
                    for (var i = 0; i < q.length; i++) {
                        if (q[i].value == "") continue;
                        if (q[i].value == match_correct[t][i]) {
                            total_marks += marks[6];
                            analysis[t*3]++;
                            analysis[t*3+1]++;
                        } else {
                            total_marks += marks[7];
                            analysis[t*3]++;
                            analysis[t*3+2]++;
                        }
                        console.log(total_marks);
                    }
                    //==============================================================

                }

            }
            //analysis = PA,PC,PW,CA,CC,CW,MA,MC,MW,TOTAL  0,3,6  
                            //analysis = 0 ,1 ,2 ,3 ,4 ,5 ,6 ,7 ,8 ,9
            $("#total_marks").val(total_marks);
            $("#phy_attempted").val(analysis[0]);
            $("#chem_attempted").val(analysis[3]);
            $("#math_attempted").val(analysis[6]);

            $("#phy_correct").val(analysis[1]);
            $("#chem_correct").val(analysis[4]);
            $("#math_correct").val(analysis[7]);
        });

        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = mm + '/' + dd + '/' + yyyy;

        $("#submit").click(function() {
            Email(total_marks);
            var line = $("#name").val() + "," + today + "\n";
            var filename = String(today) + ".csv";
        });

        function Email() {
            var emailTo = "gptshubham595@gmail.com";

            var emailSubject = "Marks of " + $("#name").val() + " on " + today;

            var emailBody = String("MARKS: " + total_marks);

            location.href = "mailto:" + emailTo + "?" +
                (emailSubject ? "&subject=" + emailSubject : "") +
                (emailBody ? "&body=" + emailBody : "");
        }
        // 0,1,2,3,4,5,6,7
        // SS,MM,II,MM
    </script>
    <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
<script type="text/javascript">
    window.onload = maxWindow;

    function maxWindow() {
        window.moveTo(0, 0);

        if (document.all) {
            top.window.resizeTo(screen.availWidth, screen.availHeight);
        }

        else if (document.layers || document.getElementById) {
            if (top.window.outerHeight < screen.availHeight || top.window.outerWidth < screen.availWidth) {
                top.window.outerHeight = screen.availHeight;
                top.window.outerWidth = screen.availWidth;
            }
        }
    }
</script> 
    <script>
        document.onkeyup = function(e) {
            if (e.which == 28) {
                e.preventDefault();
            } else if (e.ctrlKey && e.shiftKey && e.which == 105 || e.ctrlKey && e.shiftKey && e.which == 73) {
                e.preventDefault();
            } else if (e.ctrlKey && e.altKey && e.which == 89) {
                alert("Ctrl + Alt + Y shortcut combination was pressed");
            } else if (e.ctrlKey && e.which == 85) {
                e.preventDefault();
            }
        };
    </script>
    <script>
        ! function(e, t) {
            "object" == typeof exports && "undefined" != typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define(t) : (e = e || self).hotkeys = t()
        }(this, (function() {
            "use strict";

            function e(t) {
                return (e = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
                    return typeof e
                } : function(e) {
                    return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
                })(t)
            }
            var t = "undefined" != typeof navigator && navigator.userAgent.toLowerCase().indexOf("firefox") > 0;

            function n(e, t, n) {
                e.addEventListener ? e.addEventListener(t, n, !1) : e.attachEvent && e.attachEvent("on".concat(t), (function() {
                    n(window.event)
                }))
            }

            function o(e, t) {
                for (var n = t.slice(0, t.length - 1), o = 0; o < n.length; o++) n[o] = e[n[o].toLowerCase()];
                return n
            }

            function r(e) {
                "string" != typeof e && (e = "");
                for (var t = (e = e.replace(/\s/g, "")).split(","), n = t.lastIndexOf(""); n >= 0;) t[n - 1] += ",", t.splice(n, 1), n = t.lastIndexOf("");
                return t
            }
            for (var i = {
                    backspace: 8,
                    tab: 9,
                    clear: 12,
                    enter: 13,
                    return: 13,
                    esc: 27,
                    escape: 27,
                    space: 32,
                    left: 37,
                    up: 38,
                    right: 39,
                    down: 40,
                    del: 46,
                    delete: 46,
                    ins: 45,
                    insert: 45,
                    home: 36,
                    end: 35,
                    pageup: 33,
                    pagedown: 34,
                    capslock: 20,
                    "â‡ª": 20,
                    ",": 188,
                    ".": 190,
                    "/": 191,
                    "`": 192,
                    "-": t ? 173 : 189,
                    "=": t ? 61 : 187,
                    ";": t ? 59 : 186,
                    "'": 222,
                    "[": 219,
                    "]": 221,
                    "\\": 220
                }, f = {
                    "â‡§": 16,
                    shift: 16,
                    "âŒ¥": 18,
                    alt: 18,
                    option: 18,
                    "âŒƒ": 17,
                    ctrl: 17,
                    control: 17,
                    "âŒ˜": 91,
                    cmd: 91,
                    command: 91
                }, c = {
                    16: "shiftKey",
                    18: "altKey",
                    17: "ctrlKey",
                    91: "metaKey",
                    shiftKey: 16,
                    ctrlKey: 17,
                    altKey: 18,
                    metaKey: 91
                }, a = {
                    16: !1,
                    18: !1,
                    17: !1,
                    91: !1
                }, l = {}, s = 1; s < 20; s++) i["f".concat(s)] = 111 + s;
            var p = [],
                y = "all",
                u = [],
                d = function(e) {
                    return i[e.toLowerCase()] || f[e.toLowerCase()] || e.toUpperCase().charCodeAt(0)
                };

            function h(e) {
                y = e || "all"
            }

            function v() {
                return y || "all"
            }
            var g = function(e) {
                var t = e.key,
                    n = e.scope,
                    i = e.method,
                    c = e.splitKey,
                    a = void 0 === c ? "+" : c;
                r(t).forEach((function(e) {
                    var t = e.split(a),
                        r = t.length,
                        c = t[r - 1],
                        s = "*" === c ? "*" : d(c);
                    if (l[s]) {
                        n || (n = v());
                        var p = r > 1 ? o(f, t) : [];
                        l[s] = l[s].map((function(e) {
                            return (!i || e.method === i) && e.scope === n && function(e, t) {
                                for (var n = e.length >= t.length ? e : t, o = e.length >= t.length ? t : e, r = !0, i = 0; i < n.length; i++) - 1 === o.indexOf(n[i]) && (r = !1);
                                return r
                            }(e.mods, p) ? {} : e
                        }))
                    }
                }))
            };

            function w(e, t, n) {
                var o;
                if (t.scope === n || "all" === t.scope) {
                    for (var r in o = t.mods.length > 0, a) Object.prototype.hasOwnProperty.call(a, r) && (!a[r] && t.mods.indexOf(+r) > -1 || a[r] && -1 === t.mods.indexOf(+r)) && (o = !1);
                    (0 !== t.mods.length || a[16] || a[18] || a[17] || a[91]) && !o && "*" !== t.shortcut || !1 === t.method(e, t) && (e.preventDefault ? e.preventDefault() : e.returnValue = !1, e.stopPropagation && e.stopPropagation(), e.cancelBubble && (e.cancelBubble = !0))
                }
            }

            function k(e) {
                var t = l["*"],
                    n = e.keyCode || e.which || e.charCode;
                if (m.filter.call(this, e)) {
                    if (93 !== n && 224 !== n || (n = 91), -1 === p.indexOf(n) && 229 !== n && p.push(n), ["ctrlKey", "altKey", "shiftKey", "metaKey"].forEach((function(t) {
                            var n = c[t];
                            e[t] && -1 === p.indexOf(n) ? p.push(n) : !e[t] && p.indexOf(n) > -1 && p.splice(p.indexOf(n), 1)
                        })), n in a) {
                        for (var o in a[n] = !0, f) f[o] === n && (m[o] = !0);
                        if (!t) return
                    }
                    for (var r in a) Object.prototype.hasOwnProperty.call(a, r) && (a[r] = e[c[r]]);
                    var i = v();
                    if (t)
                        for (var s = 0; s < t.length; s++) t[s].scope === i && ("keydown" === e.type && t[s].keydown || "keyup" === e.type && t[s].keyup) && w(e, t[s], i);
                    if (n in l)
                        for (var y = 0; y < l[n].length; y++)
                            if (("keydown" === e.type && l[n][y].keydown || "keyup" === e.type && l[n][y].keyup) && l[n][y].key) {
                                for (var u = l[n][y], h = u.splitKey, g = u.key.split(h), k = [], b = 0; b < g.length; b++) k.push(d(g[b]));
                                k.sort().join("") === p.sort().join("") && w(e, u, i)
                            }
                }
            }

            function m(e, t, i) {
                p = [];
                var c = r(e),
                    s = [],
                    y = "all",
                    h = document,
                    v = 0,
                    g = !1,
                    w = !0,
                    b = "+";
                for (void 0 === i && "function" == typeof t && (i = t), "[object Object]" === Object.prototype.toString.call(t) && (t.scope && (y = t.scope), t.element && (h = t.element), t.keyup && (g = t.keyup), void 0 !== t.keydown && (w = t.keydown), "string" == typeof t.splitKey && (b = t.splitKey)), "string" == typeof t && (y = t); v < c.length; v++) s = [], (e = c[v].split(b)).length > 1 && (s = o(f, e)), (e = "*" === (e = e[e.length - 1]) ? "*" : d(e)) in l || (l[e] = []), l[e].push({
                    keyup: g,
                    keydown: w,
                    scope: y,
                    mods: s,
                    shortcut: c[v],
                    method: i,
                    key: c[v],
                    splitKey: b
                });
                void 0 !== h && ! function(e) {
                    return u.indexOf(e) > -1
                }(h) && window && (u.push(h), n(h, "keydown", (function(e) {
                    k(e)
                })), n(window, "focus", (function() {
                    p = []
                })), n(h, "keyup", (function(e) {
                    k(e),
                        function(e) {
                            var t = e.keyCode || e.which || e.charCode,
                                n = p.indexOf(t);
                            if (n >= 0 && p.splice(n, 1), e.key && "meta" === e.key.toLowerCase() && p.splice(0, p.length), 93 !== t && 224 !== t || (t = 91), t in a)
                                for (var o in a[t] = !1, f) f[o] === t && (m[o] = !1)
                        }(e)
                })))
            }
            var b = {
                setScope: h,
                getScope: v,
                deleteScope: function(e, t) {
                    var n, o;
                    for (var r in e || (e = v()), l)
                        if (Object.prototype.hasOwnProperty.call(l, r))
                            for (n = l[r], o = 0; o < n.length;) n[o].scope === e ? n.splice(o, 1) : o++;
                    v() === e && h(t || "all")
                },
                getPressedKeyCodes: function() {
                    return p.slice(0)
                },
                isPressed: function(e) {
                    return "string" == typeof e && (e = d(e)), -1 !== p.indexOf(e)
                },
                filter: function(e) {
                    var t = e.target || e.srcElement,
                        n = t.tagName,
                        o = !0;
                    return !t.isContentEditable && ("INPUT" !== n && "TEXTAREA" !== n || t.readOnly) || (o = !1), o
                },
                unbind: function(t) {
                    if (t) {
                        if (Array.isArray(t)) t.forEach((function(e) {
                            e.key && g(e)
                        }));
                        else if ("object" === e(t)) t.key && g(t);
                        else if ("string" == typeof t) {
                            for (var n = arguments.length, o = new Array(n > 1 ? n - 1 : 0), r = 1; r < n; r++) o[r - 1] = arguments[r];
                            var i = o[0],
                                f = o[1];
                            "function" == typeof i && (f = i, i = ""), g({
                                key: t,
                                scope: i,
                                method: f,
                                splitKey: "+"
                            })
                        }
                    } else Object.keys(l).forEach((function(e) {
                        return delete l[e]
                    }))
                }
            };
            for (var O in b) Object.prototype.hasOwnProperty.call(b, O) && (m[O] = b[O]);
            if ("undefined" != typeof window) {
                var K = window.hotkeys;
                m.noConflict = function(e) {
                    return e && window.hotkeys === m && (window.hotkeys = K), m
                }, window.hotkeys = m
            }
            return m
        }));
    </script>
    <script>
        var mdpUnGrabber = {
            "selectAll": "on",
            "copy": "on",
            "cut": "on",
            "paste": "on",
            "save": "on",
            "viewSource": "on",
            "printPage": "on",
            "developerTool": "on",
            "readerMode": "on",
            "rightClick": "on",
            "textSelection": "on",
            "imageDragging": "on"
        };
    </script>
    <script>
        "use strict";
        const UnGrabber = function() {
            function _ungrabber() {
                function init() {
                    "on" === mdpUnGrabber.selectAll && disable_select_all(), "on" === mdpUnGrabber.copy && disable_copy(), "true" === mdpUnGrabber.cut && disable_cut(), "on" === mdpUnGrabber.paste && disable_paste(), "on" === mdpUnGrabber.save && disable_save(), "on" === mdpUnGrabber.viewSource && disable_view_source(), "on" === mdpUnGrabber.printPage && disable_print_page(), "on" === mdpUnGrabber.developerTool && disable_developer_tool(), "on" === mdpUnGrabber.readerMode && disable_reader_mode(), "on" === mdpUnGrabber.rightClick && disable_right_click(), "on" === mdpUnGrabber.textSelection && disable_text_selection(), "on" === mdpUnGrabber.imageDragging && disable_image_dragging()
                }

                function disable_select_all() {
                    disable_key(65)
                }

                function disable_copy() {
                    disable_key(67)
                }

                function disable_cut() {
                    disable_key(88)
                }

                function disable_paste() {
                    disable_key(86)
                }

                function disable_save() {
                    disable_key(83)
                }

                function disable_view_source() {
                    disable_key(85)
                }

                function disable_print_page() {
                    disable_key(80)
                }

                function disable_reader_mode() {
                    navigator.userAgent.toLowerCase().includes("safari") && !navigator.userAgent.toLowerCase().includes("chrome") && window.addEventListener("keydown", (function(e) {
                        (e.ctrlKey || e.metaKey) && e.shiftKey && 82 === e.keyCode && e.preventDefault()
                    }))
                }

                function disable_developer_tool() {
                    let checkStatus;
                    hotkeys("command+option+j,command+option+i,command+shift+c,command+option+c,command+option+k,command+option+z,command+option+e,f12,ctrl+shift+i,ctrl+shift+j,ctrl+shift+c,ctrl+shift+k,ctrl+shift+e,shift+f7,shift+f5,shift+f9,shift+f12", (function(e, t) {
                        e.preventDefault()
                    }));
                    let element = new Image;
                    Object.defineProperty(element, "id", {
                        get: function() {
                            throw checkStatus = "on", new Error("Dev tools checker")
                        }
                    }), requestAnimationFrame((function check() {
                        checkStatus = "off", console.dir(element), "on" === checkStatus ? (document.body.parentNode.removeChild(document.body), document.head.parentNode.removeChild(document.head), setTimeout((function() {
                            for (;;) eval("debugger")
                        }), 100)) : requestAnimationFrame(check)
                    }))
                }

                function disable_right_click() {
                    document.oncontextmenu = function(e) {
                        var t = e || window.event;
                        if ("A" !== (t.target || t.srcElement).nodeName) return !1
                    }, document.body.oncontextmenu = function() {
                        return !1
                    }, document.onmousedown = function(e) {
                        if (2 === e.button) return !1
                    };
                    let e = setInterval((function() {
                        null === document.oncontextmenu && (document.body.parentNode.removeChild(document.body), document.head.parentNode.removeChild(document.head), clearInterval(e))
                    }), 500)
                }

                function disable_text_selection() {
                    void 0 !== document.body.onselectstart ? document.body.onselectstart = function() {
                        return !1
                    } : void 0 !== document.body.style.MozUserSelect ? document.body.style.MozUserSelect = "none" : void 0 !== document.body.style.webkitUserSelect ? document.body.style.webkitUserSelect = "none" : document.body.onmousedown = function() {
                        return !1
                    }, document.body.style.cursor = "default", document.documentElement.style.webkitTouchCallout = "none", document.documentElement.style.webkitUserSelect = "none";
                    let e = document.createElement("style");
                    document.head.appendChild(e), e.type = "text/css", e.innerText = "* {-webkit-user-select: none !important; -moz-user-select: none !important; -ms-user-select: none !important; user-select: none !important; }"
                }

                function disable_image_dragging() {
                    document.ondragstart = function() {
                        return !1
                    }
                }

                function disable_key(e) {
                    window.addEventListener("keydown", (function(t) {
                        t.ctrlKey && t.which === e && t.preventDefault(), t.metaKey && t.which === e && t.preventDefault()
                    })), document.keypress = function(t) {
                        return (!t.ctrlKey || t.which !== e) && ((!t.metaKey || t.which !== e) && void 0)
                    }
                }
                return {
                    init: init
                }
            }
            return _ungrabber
        }();
        document.addEventListener("DOMContentLoaded", (function() {
            if ("undefined" != typeof mdpUngrabberDestroyer) return;
            (new UnGrabber).init()
        }));
    </script><noscript>
        <div id='mdp-deblocker-js-disabled'>
            <div>
                <h3>Please Enable JavaScript in your Browser.</h3>
            </div>
        </div>
        <style>
            #mdp-deblocker-js-disabled {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                width: 100%;
                z-index: 999999;
                text-align: center;
                background-color: #FFFFFF;
                color: #000000;
                font-size: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        </style>
    </noscript>
    
    <script>
        document.addEventListener("DOMContentLoaded", (function() {
            if (void 0 !== window.mdpDeBlockerDestroyer) return;

            function disableTextSelection(t) {
                void 0 !== t.onselectstart ? t.onselectstart = function() {
                    return !1
                } : void 0 !== t.style.MozUserSelect ? t.style.MozUserSelect = "none" : void 0 !== t.style.webkitUserSelect ? t.style.webkitUserSelect = "none" : t.onmousedown = function() {
                    return !1
                }, t.style.cursor = "default"
            }

            function enableSelection(t) {
                void 0 !== t.onselectstart ? t.onselectstart = function() {
                    return !0
                } : void 0 !== t.style.MozUserSelect ? t.style.MozUserSelect = "text" : void 0 !== t.style.webkitUserSelect ? t.style.webkitUserSelect = "text" : t.onmousedown = function() {
                    return !0
                }, t.style.cursor = "auto"
            }

            function disableContextMenu() {
                document.oncontextmenu = function(t) {
                    var e = t || window.event;
                    if ("A" != (e.target || e.srcElement).nodeName) return !1
                }, document.body.oncontextmenu = function() {
                    return !1
                }, document.ondragstart = function() {
                    return !1
                }
            }

            function enableContextMenu() {
                document.oncontextmenu = null, document.body.oncontextmenu = null, document.ondragstart = null
            }
            let h_win_disableHotKeys, h_mac_disableHotKeys;

            function disableHotKeys() {
                h_win_disableHotKeys = function(t) {
                    !t.ctrlKey || 65 != t.which && 66 != t.which && 67 != t.which && 70 != t.which && 73 != t.which && 80 != t.which && 83 != t.which && 85 != t.which && 86 != t.which || t.preventDefault()
                }, window.addEventListener("keydown", h_win_disableHotKeys), document.keypress = function(t) {
                    if (t.ctrlKey && (65 == t.which || 66 == t.which || 70 == t.which || 67 == t.which || 73 == t.which || 80 == t.which || 83 == t.which || 85 == t.which || 86 == t.which)) return !1
                }, h_mac_disableHotKeys = function(t) {
                    !t.metaKey || 65 != t.which && 66 != t.which && 67 != t.which && 70 != t.which && 73 != t.which && 80 != t.which && 83 != t.which && 85 != t.which && 86 != t.which || t.preventDefault()
                }, window.addEventListener("keydown", h_mac_disableHotKeys), document.keypress = function(t) {
                    if (t.metaKey && (65 == t.which || 66 == t.which || 70 == t.which || 67 == t.which || 73 == t.which || 80 == t.which || 83 == t.which || 85 == t.which || 86 == t.which)) return !1
                }, document.onkeydown = function(t) {
                    (123 == t.keyCode || (t.ctrlKey || t.metaKey) && t.shiftKey && 73 == t.keyCode) && t.preventDefault()
                }
            }

            function disableDeveloperTools() {
                let checkStatus;
                window.addEventListener("keydown", (function(t) {
                    (123 === t.keyCode || (t.ctrlKey || t.metaKey) && t.shiftKey && 73 === t.keyCode) && t.preventDefault()
                }));
                let element = new Image;
                Object.defineProperty(element, "id", {
                    get: function() {
                        throw checkStatus = "on", new Error("Dev tools checker")
                    }
                }), requestAnimationFrame((function check() {
                    checkStatus = "off", console.dir(element), "on" === checkStatus ? (document.body.parentNode.removeChild(document.body), document.head.parentNode.removeChild(document.head), setTimeout((function() {
                        for (;;) eval("debugger")
                    }), 100)) : requestAnimationFrame(check)
                }))
            }

            function enableHotKeys() {
                window.removeEventListener("keydown", h_win_disableHotKeys), document.keypress = function(t) {
                    if (t.ctrlKey && (65 == t.which || 66 == t.which || 70 == t.which || 67 == t.which || 73 == t.which || 80 == t.which || 83 == t.which || 85 == t.which || 86 == t.which)) return !0
                }, window.removeEventListener("keydown", h_mac_disableHotKeys), document.keypress = function(t) {
                    if (t.metaKey && (65 == t.which || 66 == t.which || 70 == t.which || 67 == t.which || 73 == t.which || 80 == t.which || 83 == t.which || 85 == t.which || 86 == t.which)) return !0
                }, document.onkeydown = function(t) {
                    if (123 == (t = t || window.event).keyCode || 18 == t.keyCode || t.ctrlKey && t.shiftKey && 73 == t.keyCode) return !0
                }
            }

            function addStyles() {
                let t = mdpDeBlocker.prefix,
                    e = document.createElement("style");
                e.innerHTML = `\n            .${t}-style-compact .${t}-blackout,\n            .${t}-style-compact-right-top .${t}-blackout,\n            .${t}-style-compact-left-top .${t}-blackout,\n            .${t}-style-compact-right-bottom .${t}-blackout,\n            .${t}-style-compact-left-bottom .${t}-blackout,\n            .${t}-style-compact .${t}-blackout {\n                position: fixed;\n                z-index: 9997;\n                left: 0;\n                top: 0;\n                width: 100%;\n                height: 100%;\n                display: none;\n            }\n\n            .${t}-style-compact .${t}-blackout.active,\n            .${t}-style-compact-right-top .${t}-blackout.active,\n            .${t}-style-compact-left-top .${t}-blackout.active,\n            .${t}-style-compact-right-bottom .${t}-blackout.active,\n            .${t}-style-compact-left-bottom .${t}-blackout.active,\n            .${t}-style-compact .${t}-blackout.active {\n                display: block;\n                -webkit-animation: deblocker-appear;\n                animation: deblocker-appear;\n                -webkit-animation-duration: .2s;\n                animation-duration: .2s;\n                -webkit-animation-fill-mode: both;\n                animation-fill-mode: both;\n            }\n\n            .${t}-style-compact .${t}-wrapper,\n            .${t}-style-compact-right-top .${t}-wrapper,\n            .${t}-style-compact-left-top .${t}-wrapper,\n            .${t}-style-compact-right-bottom .${t}-wrapper,\n            .${t}-style-compact-left-bottom .${t}-wrapper,\n            .${t}-style-compact .${t}-wrapper {\n                display: flex;\n                justify-content: center;\n                align-items: center;\n                position: fixed;\n                top: 0;\n                left: 0;\n                width: 100%;\n                height: 100%;\n                z-index: 9998;\n            }\n\n            .${t}-style-compact .${t}-modal,\n            .${t}-style-compact-right-top .${t}-modal,\n            .${t}-style-compact-left-top .${t}-modal,\n            .${t}-style-compact-right-bottom .${t}-modal,\n            .${t}-style-compact-left-bottom .${t}-modal,\n            .${t}-style-compact .${t}-modal {\n                height: auto;\n                width: auto;\n                position: relative;\n                max-width: 40%;\n                padding: 4rem;\n                opacity: 0;\n                z-index: 9999;\n                transition: all 0.5s ease-in-out;\n                border-radius: 1rem;\n                margin: 1rem;\n            }\n\n            .${t}-style-compact .${t}-modal.active,\n            .${t}-style-compact-right-top .${t}-modal.active,\n            .${t}-style-compact-left-top .${t}-modal.active,\n            .${t}-style-compact-right-bottom .${t}-modal.active,\n            .${t}-style-compact-left-bottom .${t}-modal.active,\n            .${t}-style-compact .${t}-modal.active {\n                opacity: 1;\n                -webkit-animation: deblocker-appear;\n                animation: deblocker-appear;\n                -webkit-animation-delay: .1s;\n                animation-delay: .1s;\n                -webkit-animation-duration: .5s;\n                animation-duration: .5s;\n                -webkit-animation-fill-mode: both;\n                animation-fill-mode: both;\n            }\n\n            .${t}-style-compact .${t}-modal h4,\n            .${t}-style-compact-right-top .${t}-modal h4,\n            .${t}-style-compact-left-top .${t}-modal h4,\n            .${t}-style-compact-right-bottom .${t}-modal h4,\n            .${t}-style-compact-left-bottom .${t}-modal h4,\n            .${t}-style-compact .${t}-modal h4 {\n                margin: 0 0 1rem 0;\n                padding-right: .8rem;\n            }\n\n            .${t}-style-compact .${t}-modal p,\n            .${t}-style-compact-right-top .${t}-modal p,\n            .${t}-style-compact-left-top .${t}-modal p,\n            .${t}-style-compact-right-bottom .${t}-modal p,\n            .${t}-style-compact-left-bottom .${t}-modal p,\n            .${t}-style-compact .${t}-modal p {\n                margin: 0;\n            }\n\n            @media only screen and (max-width: 1140px) {\n                .${t}-style-compact .${t}-modal,\n                .${t}-style-compact-right-top .${t}-modal,\n                .${t}-style-compact-left-top .${t}-modal,\n                .${t}-style-compact-right-bottom .${t}-modal,\n                .${t}-style-compact-left-bottom .${t}-modal,\n                .${t}-style-compact .${t}-modal {\n                    min-width: 60%;\n                }\n            }\n\n            @media only screen and (max-width: 768px) {\n                .${t}-style-compact .${t}-modal,\n                .${t}-style-compact-right-top .${t}-modal,\n                .${t}-style-compact-left-top .${t}-modal,\n                .${t}-style-compact-right-bottom .${t}-modal,\n                .${t}-style-compact-left-bottom .${t}-modal,\n                .${t}-style-compact .${t}-modal {\n                    min-width: 80%;\n                }\n            }\n\n            @media only screen and (max-width: 420px) {\n                .${t}-style-compact .${t}-modal,\n                .${t}-style-compact-right-top .${t}-modal,\n                .${t}-style-compact-left-top .${t}-modal,\n                .${t}-style-compact-right-bottom .${t}-modal,\n                .${t}-style-compact-left-bottom .${t}-modal,\n                .${t}-style-compact .${t}-modal {\n                    min-width: 90%;\n                }\n            }\n\n            .${t}-style-compact .${t}-close,\n            .${t}-style-compact-right-top .${t}-close,\n            .${t}-style-compact-left-top .${t}-close,\n            .${t}-style-compact-right-bottom .${t}-close,\n            .${t}-style-compact-left-bottom .${t}-close,\n            .${t}-style-compact .${t}-close {\n                position: absolute;\n                right: 1rem;\n                top: 1rem;\n                display: inline-block;\n                cursor: pointer;\n                opacity: .3;\n                width: 32px;\n                height: 32px;\n                -webkit-animation: deblocker-close-appear;\n                animation: deblocker-close-appear;\n                -webkit-animation-delay: 1s;\n                animation-delay: 1s;\n                -webkit-animation-duration: .4s;\n                animation-duration: .4s;\n                -webkit-animation-fill-mode: both;\n                animation-fill-mode: both;\n            }\n\n            .${t}-style-compact .${t}-close:hover,\n            .${t}-style-compact-right-top .${t}-close:hover,\n            .${t}-style-compact-left-top .${t}-close:hover,\n            .${t}-style-compact-right-bottom .${t}-close:hover,\n            .${t}-style-compact-left-bottom .${t}-close:hover,\n            .${t}-style-compact .${t}-close:hover {\n                opacity: 1;\n            }\n\n            .${t}-style-compact .${t}-close:before,\n            .${t}-style-compact .${t}-close:after,\n            .${t}-style-compact-right-top .${t}-close:before,\n            .${t}-style-compact-right-top .${t}-close:after,\n            .${t}-style-compact-left-top .${t}-close:before,\n            .${t}-style-compact-left-top .${t}-close:after,\n            .${t}-style-compact-right-bottom .${t}-close:before,\n            .${t}-style-compact-right-bottom .${t}-close:after,\n            .${t}-style-compact-left-bottom .${t}-close:before,\n            .${t}-style-compact-left-bottom .${t}-close:after,\n            .${t}-style-compact .${t}-close:before,\n            .${t}-style-compact .${t}-close:after {\n                position: absolute;\n                left: 15px;\n                content: ' ';\n                height: 33px;\n                width: 2px;\n            }\n\n            .${t}-style-compact .${t}-close:before,\n            .${t}-style-compact-right-top .${t}-close:before,\n            .${t}-style-compact-left-top .${t}-close:before,\n            .${t}-style-compact-right-bottom .${t}-close:before,\n            .${t}-style-compact-left-bottom .${t}-close:before,\n            .${t}-style-compact .${t}-close:before {\n                transform: rotate(45deg);\n            }\n\n            .${t}-style-compact .${t}-close:after,\n            .${t}-style-compact-right-top .${t}-close:after,\n            .${t}-style-compact-left-top .${t}-close:after,\n            .${t}-style-compact-right-bottom .${t}-close:after,\n            .${t}-style-compact-left-bottom .${t}-close:after,\n            .${t}-style-compact .${t}-close:after {\n                transform: rotate(-45deg);\n            }\n\n            .${t}-style-compact-right-top .${t}-wrapper {\n                justify-content: flex-end;\n                align-items: flex-start;\n            }\n\n            .${t}-style-compact-left-top .${t}-wrapper {\n                justify-content: flex-start;\n                align-items: flex-start;\n            }\n\n            .${t}-style-compact-right-bottom .${t}-wrapper {\n                justify-content: flex-end;\n                align-items: flex-end;\n            }\n\n            .${t}-style-compact-left-bottom .${t}-wrapper {\n                justify-content: flex-start;\n                align-items: flex-end;\n            }\n\n            .${t}-style-full .${t}-blackout {\n                position: fixed;\n                z-index: 9998;\n                left: 0;\n                top: 0;\n                width: 100%;\n                height: 100%;\n                display: none;\n            }\n\n            .${t}-style-full .${t}-blackout.active {\n                display: block;\n                -webkit-animation: deblocker-appear;\n                animation: deblocker-appear;\n                -webkit-animation-delay: .4s;\n                animation-delay: .4s;\n                -webkit-animation-duration: .4s;\n                animation-duration: .4s;\n                -webkit-animation-fill-mode: both;\n                animation-fill-mode: both;\n            }\n\n            .${t}-style-full .${t}-modal {\n                height: 100%;\n                width: 100%;\n                max-width: 100%;\n                max-height: 100%;\n                position: fixed;\n                left: 50%;\n                top: 50%;\n                transform: translate(-50%, -50%);\n                padding: 45px;\n                opacity: 0;\n                z-index: 9999;\n                transition: all 0.5s ease-in-out;\n                display: flex;\n                align-items: center;\n                justify-content: center;\n                flex-direction: column;\n            }\n\n            .${t}-style-full .${t}-modal.active {\n                opacity: 1;\n                -webkit-animation: mdp-deblocker-appear;\n                animation: mdp-deblocker-appear;\n                -webkit-animation-duration: .4s;\n                animation-duration: .4s;\n                -webkit-animation-fill-mode: both;\n                animation-fill-mode: both;\n            }\n\n            .${t}-style-full .${t}-modal h4 {\n                margin: 0 0 1rem 0;\n            }\n\n            .${t}-style-full .${t}-modal p {\n                margin: 0;\n            }\n\n            .${t}-style-full .${t}-close {\n                position: absolute;\n                right: 10px;\n                top: 10px;\n                width: 32px;\n                height: 32px;\n                display: inline-block;\n                cursor: pointer;\n                opacity: .3;\n                -webkit-animation: mdp-deblocker-close-appear;\n                animation: mdp-deblocker-close-appear;\n                -webkit-animation-delay: 1s;\n                animation-delay: 1s;\n                -webkit-animation-duration: .4s;\n                animation-duration: .4s;\n                -webkit-animation-fill-mode: both;\n                animation-fill-mode: both;\n            }\n\n            .${t}-style-full .${t}-close:hover {\n                opacity: 1;\n            }\n\n            .${t}-style-full .${t}-close:before,\n            .${t}-style-full .${t}-close:after {\n                position: absolute;\n                left: 15px;\n                content: ' ';\n                height: 33px;\n                width: 2px;\n            }\n\n            .${t}-style-full .${t}-close:before {\n                transform: rotate(45deg);\n            }\n\n            .${t}-style-full .${t}-close:after {\n                transform: rotate(-45deg);\n            }\n\n            @-webkit-keyframes mdp-deblocker-appear {\n                from {\n                    opacity: 0;\n                }\n                to {\n                    opacity: 1;\n                }\n            }\n\n            @keyframes mdp-deblocker-appear {\n                from {\n                    opacity: 0;\n                }\n                to {\n                    opacity: 1;\n                }\n            }\n\n            @-webkit-keyframes mdp-deblocker-close-appear {\n                from {\n                    opacity: 0;\n                    transform: scale(0.2);\n                }\n                to {\n                    opacity: .3;\n                    transform: scale(1);\n                }\n            }\n\n            @keyframes mdp-deblocker-close-appear {\n                from {\n                    opacity: 0;\n                    transform: scale(0.2);\n                }\n                to {\n                    opacity: .3;\n                    transform: scale(1);\n                }\n            }\n\n            body.${t}-blur { \n                -webkit-backface-visibility: none;\n            }\n\n            body.${t}-blur > *:not(#wpadminbar):not(.${t}-modal):not(.${t}-wrapper):not(.${t}-blackout) {\n                -webkit-filter: blur(5px);\n                filter: blur(5px);\n            }\n        `;
                let n = document.querySelectorAll("script"),
                    o = n[Math.floor(Math.random() * n.length)];
                o.parentNode.insertBefore(e, o)
            }

            function showModal() {
                setTimeout((function() {
                    let t = mdpDeBlocker.prefix;
                    addStyles(), document.body.classList.add(`${t}-style-` + mdpDeBlocker.style), "on" === mdpDeBlocker.blur && document.body.classList.add(`${t}-blur`);
                    let e = document.createElement("div");
                    e.classList.add(`${t}-blackout`), e.style.backgroundColor = mdpDeBlocker.bg_color, e.classList.add("active"), document.body.appendChild(e);
                    let n = document.createElement("div");
                    n.classList.add(`${t}-wrapper`), document.body.appendChild(n);
                    let o = document.createElement("div");
                    if (o.classList.add(`${t}-modal`), o.style.backgroundColor = mdpDeBlocker.modal_color, o.classList.add("active"), n.appendChild(o), "on" === mdpDeBlocker.closeable) {
                        let e = document.createElement("span");
                        e.classList.add(`${t}-close`), e.innerHTML = "&nbsp;", e.setAttribute("href", "#");
                        let n = document.createElement("style");
                        n.type = "text/css", n.innerHTML = `.${t}-close:after,` + `.${t}-close:before {` + "background-color: " + mdpDeBlocker.text_color + ";}", (document.head || document.getElementsByTagName("head")[0]).appendChild(n), e.addEventListener("click", (function(e) {
                            e.preventDefault();
                            let n = document.querySelector(`.${t}-modal`);
                            n.parentNode.removeChild(n), n = document.querySelector(`.${t}-wrapper`), n.parentNode.removeChild(n), n = document.querySelector(`.${t}-blackout`), n.parentNode.removeChild(n), document.body.classList.remove(`${t}-blur`), enableSelection(document.body), enableContextMenu(), enableHotKeys()
                        })), o.appendChild(e)
                    }
                    let c = document.createElement("h4");
                    c.innerHTML = mdpDeBlocker.title, c.style.color = mdpDeBlocker.text_color, o.appendChild(c);
                    let a = document.createElement("div");
                    a.classList.add(`${t}-content`), a.innerHTML = mdpDeBlocker.content, a.style.color = mdpDeBlocker.text_color, o.appendChild(a), disableTextSelection(document.body), disableContextMenu(), disableHotKeys(), disableDeveloperTools()
                }), mdpDeBlocker.timeout)
            }
        }), !1);
    </script>
    <script>
        $(document).bind("contextmenu", function(e) {
            return false;
        });
        (function(a, b, c) {
            Object.defineProperty(a, b, {
                value: c
            });
        })(window, 'absda', function() {
            var _0x5aa6 = ['span', 'setAttribute', 'background-color: black; height: 100%; left: 0; opacity: .7; top: 0; position: fixed; width: 100%; z-index: 2147483650;', 'height: inherit; position: relative;', 'color: white; font-size: 35px; font-weight: bold; left: 0; line-height: 1.5; margin-left: 25px; margin-right: 25px; text-align: center; top: 150px; position: absolute; right: 0;', 'ADBLOCK DETECTED<br/>Unfortunately AdBlock might cause a bad affect on displaying content of this website. Please, deactivate it.', 'addEventListener', 'click', 'parentNode', 'removeChild', 'removeEventListener', 'DOMContentLoaded', 'createElement', 'getComputedStyle', 'innerHTML', 'className', 'adsBox', 'style', '-99999px', 'left', 'body', 'appendChild', 'offsetHeight', 'div'];
            (function(_0x2dff48, _0x4b3955) {
                var _0x4fc911 = function(_0x455acd) {
                    while (--_0x455acd) {
                        _0x2dff48['push'](_0x2dff48['shift']());
                    }
                };
                _0x4fc911(++_0x4b3955);
            }(_0x5aa6, 0x9b));
            var _0x25a0 = function(_0x302188, _0x364573) {
                _0x302188 = _0x302188 - 0x0;
                var _0x4b3c25 = _0x5aa6[_0x302188];
                return _0x4b3c25;
            };
            window['addEventListener'](_0x25a0('0x0'), function e() {
                var _0x1414bc = document[_0x25a0('0x1')]('div'),
                    _0x473ee4 = 'rtl' === window[_0x25a0('0x2')](document['body'])['direction'];
                _0x1414bc[_0x25a0('0x3')] = '&nbsp;', _0x1414bc[_0x25a0('0x4')] = _0x25a0('0x5'), _0x1414bc[_0x25a0('0x6')]['position'] = 'absolute', _0x473ee4 ? _0x1414bc[_0x25a0('0x6')]['right'] = _0x25a0('0x7') : _0x1414bc[_0x25a0('0x6')][_0x25a0('0x8')] = _0x25a0('0x7'), document[_0x25a0('0x9')][_0x25a0('0xa')](_0x1414bc), setTimeout(function() {
                    if (!_0x1414bc[_0x25a0('0xb')]) {
                        var _0x473ee4 = document[_0x25a0('0x1')](_0x25a0('0xc')),
                            _0x3c0b3b = document[_0x25a0('0x1')](_0x25a0('0xc')),
                            _0x1f5f8c = document[_0x25a0('0x1')](_0x25a0('0xd')),
                            _0x5a9ba0 = document['createElement']('p');
                        _0x473ee4[_0x25a0('0xe')]('style', _0x25a0('0xf')), _0x3c0b3b['setAttribute']('style', _0x25a0('0x10')), _0x1f5f8c[_0x25a0('0xe')](_0x25a0('0x6'), 'color: white; cursor: pointer; font-size: 0px; font-weight: bold; position: absolute; right: 30px; top: 20px;'), _0x5a9ba0[_0x25a0('0xe')](_0x25a0('0x6'), _0x25a0('0x11')), _0x5a9ba0[_0x25a0('0x3')] = _0x25a0('0x12'), _0x1f5f8c[_0x25a0('0x3')] = '&#10006;', _0x3c0b3b['appendChild'](_0x5a9ba0), _0x3c0b3b[_0x25a0('0xa')](_0x1f5f8c), _0x1f5f8c[_0x25a0('0x13')](_0x25a0('0x14'), function _0x3c0b3b() {
                            _0x473ee4[_0x25a0('0x15')][_0x25a0('0x16')](_0x473ee4)
                        }), _0x473ee4[_0x25a0('0xa')](_0x3c0b3b), document[_0x25a0('0x9')][_0x25a0('0xa')](_0x473ee4);
                    }
                }, 0xc8), window[_0x25a0('0x17')]('DOMContentLoaded', e);
            });
        });
    </script>
</body>

</html>
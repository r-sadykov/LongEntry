<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Long Entry Generation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="../Styles/default.css"/>
    <script type="text/javascript" src="../Scripts/tableManage.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../Scripts/jquery.validate.min.js"></script>
    <script src="../Scripts/is.mobile.js" type="text/javascript"></script>
    <script src="../Scripts/jquery.maskedinput.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../Scripts/validationScript.js"></script>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: phaen
 * Date: 31.07.2016
 * Time: 20:31
 */
if (isset($_GET['posted'])=='Copy') {
    $agencyCode = $_GET['agencyCode'];
    $restrictedTicketIndicator = $_GET['restrictedTicketIndicator'];
    $airlineAccountCode = $_GET['airlineAccountCode'];
    $ticketNumber = $_POST['ticketNumber'];
    $departureDay = $_POST['departureDay'];
    $departureMonth = $_POST['departureMonth'];
    $departureYear = $_POST['departureYear'];
    $departurePort = $_POST['departurePort'];
    $pnr = $_POST['pnr'];
    $carrierCode = $_POST['carrierCode'];
    $serviceClass = $_POST['serviceClass'];
    $stopOverCode = $_POST['stopOverCode'];
    $destinationCity = $_POST['destinationCity'];
    $result = "";
    $size = 0;
    $checksum = 0;

    $field_names = array(
        "agencyCode" => "agencyCodeString",
        "restrictedTicketIndicator" => "restrictedTicketIndicatorString",
        "airlineAccountCode" => "airlineAccountCodeString",
        "ticketNumber" => "ticketNumberString",
        "departureDay" => "departureDayString",
        "departureMonth" => "departureMonthString",
        "departureYear" => "departureYearString",
        "departurePort" => "departurePortString",
        "pnr" => "pnrString",
    );

    function form_validate($fns)
    {
        foreach ($fns as $key => $value) {
            $field_value = $key;
            global $$field_value;
            switch ($value) {
                case "agencyCodeString":
                    global $agencyCode;
                    if (strlen($$field_value) < 1) {
                        for ($i = 0; $i <= 8; $i++) {
                            $agencyCode .= " ";
                        }
                    }
                    if (strlen($$field_value) > 8) {
                        throw new Exception("<b>" . $key . "</b> must have 8 letters only");
                    }
                    break;
                case "restrictedTicketIndicatorString":
                    if (!mb_ereg("^(0|1){1}", $$field_value)) {
                        throw new Exception("<b>" . $key . "</b> must have <b>0</b> if ticket could be returned and <b>1</b> if ticket is forbidden to return");
                    }
                    break;
                case "airlineAccountCodeString":
                    if ((strlen($$field_value) < 3) or (strlen($$field_value) > 3)) {
                        throw new Exception("<b>" . $key . "</b> must be only 3 letters");
                    }
                    break;
                case "ticketNumberString":
                    if ((strlen($$field_value) < 10) or strlen($$field_value) > 10) {
                        throw new Exception("<b>" . $key . "</b> must be only 10 letters");
                    }
                    break;
                case "departureDayString":
                    if ((strlen($$field_value) < 1) or (strlen($$field_value) > 2)) {
                        throw new Exception("<b>" . $key . "</b> must be not less than 1 and not more than 2 digits.");
                    }
                    if (strlen($$field_value) == 1) {
                        if (!mb_ereg("^([1-9]){1}", $$field_value)) {
                            throw new Exception("<b>" . $key . "</b> is not valid number");
                        } else {
                            $$field_value .= "0" . $$field_value;
                        }
                    }
                    if (strlen($$field_value) == 2) {
                        if (!mb_ereg("([0][1-9])|([1-2][0-9])|([3][0-1])", $$field_value)) {
                            throw new Exception("<b>" . $key . "</b> is not match day value");
                        }
                    }
                    break;
                case "departureMonthString":
                    if ((strlen($$field_value) < 1) or (strlen($$field_value) > 2)) {
                        throw new Exception("<b>" . $key . "</b> must be not less than 1 and not more than 2 digits.");
                    }
                    if (strlen($$field_value) == 1) {
                        if (!mb_ereg("^([1-9]){1}", $$field_value)) {
                            throw new Exception("<b>" . $key . "</b> is not valid number");
                        } else {
                            $$field_value .= "0" . $$field_value;
                        }
                    }
                    if (strlen($$field_value) == 2) {
                        if (!mb_ereg("([0][1-9])|([1][1-2])", $$field_value)) {
                            throw new Exception("<b>" . $key . "</b> is not match month value");
                        }
                    }
                    break;
                case "departureYearString":
                    if ((strlen($$field_value) < 1) or (strlen($$field_value) > 2)) {
                        throw new Exception("<b>" . $key . "</b> must be not less than 1 and not more than 2 digits.");
                    }
                    if (strlen($$field_value) == 1) {
                        if (!mb_ereg("^([1-9]){1}", $$field_value)) {
                            throw new Exception("<b>" . $key . "</b> is not valid number");
                        } else {
                            $$field_value .= "0" . $$field_value;
                        }
                    }
                    if (strlen($$field_value) == 2) {
                        if (!mb_ereg("([0-9][1-9])", $$field_value)) {
                            throw new Exception("<b>" . $key . "</b> is not match year value");
                        }
                    }
                    break;
                case "departurePortString":
                    if ((strlen($$field_value) < 3) or (strlen($$field_value) > 3)) {
                        throw new Exception("<b>" . $key . "</b> departure point IATA code must be 3 letters.");
                    }
                    break;
                case "pnrString":
                    if (strlen($$field_value) < 20) {
                        for ($i = strlen($$field_value); $i < 20; $i++) {
                            $$field_value .= " ";
                        }
                    }
                    if (strlen($$field_value) > 20) {
                        $$field_value = substr($$field_value, 0, 20);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    try {
        form_validate($field_names);
        $result = $_POST['extensionIdentification'] . $agencyCode;
        $rowsLen=count($carrierCode);
        for ($i = 0; $i < $rowsLen; $i++) {
            $result .= $carrierCode[$i] . $serviceClass[$i] . $stopOverCode[$i] . $destinationCity[$i];
        }
        $result .= $restrictedTicketIndicator . $airlineAccountCode . $ticketNumber;
        $checksum = $ticketNumber - (floor($ticketNumber / 7) * 7);
        $result .= $checksum . $departureMonth . $departureDay . $departureYear . $departurePort . $pnr;
        $size = strlen($result);
    } catch (Exception $e) {
        echo $e->getMessage();
        echo "<br/>";
    }
    echo $result."<br/>";
    echo $size."<br/>";
    echo "DONE";
}
/*
if (!is_object($e) and isset($_POST['posted'])){
?>
<form action="../Models/logentry.php" class="register" method="POST" id="LongEntry">
    <input type="hidden" name="posted" value="true"/>
    <h1>Long Entry Generation Script</h1>
    <fieldset class="row1">
        <legend>Ticket Information</legend>
        <p>
            <label style="width: 120px">Extension indicator:</label>
            <input name="extensionIdentification" type="text" required="required" readonly value="01"
                   style="width: 20px;"/>
            <label style="width: 110px">Agency code:</label>
            <input name="agencyCode" type="text" required="required" readonly style="width: 80px;"
                   value="<?php $agencyCode ?>"/>
            <label style="width: 170px">Restricted ticket indicator:</label>
            <input name="restrictedTicketIndicator" type="text" required="required" style="width: 10px;"
                   value=<?php $restrictedTicketIndicator

            ?>/>
        </p>
        <p>
            <label style="width: 120px">Airline account code:</label>
            <input name="airlineAccountCode" required="required" type="text" style="width: 30px;"
                   value="<?php $airlineAccountCode ?>"/>
            <label>Ticket number:</label>
            <input name="ticketNumber" required="required" type="text" style="width: 100px;"
                   value="<?php $ticketNumber ?>"/>
        </p>
        <p>
            <label>Departure date:</label>
        </p>
        <p>
            <label style="width: 120px">Day - </label>
            <input name="departureDay" required="required" type="text" style="width: 20px;"
                   value="<?php $departureDay ?>"/>
            <label style="width: 50px">Month - </label>
            <input name="departureMonth" required="required" type="text" style="width: 20px"
                   value="<?php $departureMonth ?>"/>
            <label style="width: 50px">Year - </label>
            <input name="departureYear" required="required" type="text" style="width: 20px"
                   value="<?php $departureYear ?>"/>
        </p>
        <p>
            <label style="width: 120px">Departure airport:</label>
            <input name="departurePort" required="required" type="text" style="width: 30px;"/>
        </p>
        <p>
            <label style="width: 120px">Passenger:</label>
            <input name="pnr" required="required" type="text" style="width: 200px;" value="<?php $pnr ?>"/>
        </p>
        <div class="clear"></div>
    </fieldset>
    <fieldset class="row2">
        <legend>Legs Details</legend>
        <p>
            <input type="button" value="Add Leg" onClick="addRow('dataTable')"/>
            <input type="button" value="Remove Leg" onClick="deleteRow('dataTable')"/>
        </p>
        <table id="dataTable" class="form" border="1">
            <tbody>
            <?php
            $arrLen = count($carrierCode);
            for ($i = 0; i < $arrLen; $i++) {
                ?>
                <tr>
                    <p>
                    <td><input type="checkbox" required="required" name="chk[]" checked="checked"/></td>
                    <td>
                        <label>Carrier code:</label>
                        <input type="text" required="required" name="carrierCode[]" style="width: 20px"
                               value="<?php $carrierCode[$i] ?>">
                    </td>
                    <td>
                        <label>Service class:</label>
                        <input type="text" required="required" class="small" name="serviceClass[]" style="width: 10px"
                               value="<?php $serviceClass[$i] ?>">
                    </td>
                    <td>
                        <label>Stop-Over code:</label>
                        <input type="text" required="required" name="stopOverCode[]" style="width: 10px"
                               value="<?php $stopOverCode[$i] ?>"/>
                    </td>
                    <td>
                        <label>Destination city:</label>
                        <input type="text" required="required" name="destinationCity[]" style="width: 30px"
                               value="<?php $destinationCity[$i] ?>"/>
                    </td>
                    </p>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <div class="clear"></div>
    </fieldset>
    <fieldset class="row4">
        <legend>Generated long entry row</legend>
        <p>
            <input class="submit" type="submit" value="Confirm &raquo;"/>
        </p>
        <p>
            <?php

            ?>
            <input type="text" required="required" name="result" style="width: 830px" readonly
                   value="<?php $result ?>"/>
        </p>
        <p>
            <label style="width: 140px">The size of long entry is:</label>
            <input type="text" required="required" name="size" style="width: 20px" readonly value="<?php $size ?>"/>
        </p>
        <p>
            <input class="submit" type="submit" value="Copy"/>
        </p>
        <div class="clear"></div>
    </fieldset>
    <div class="clear"></div>
</form>
</body>
</html>
<?php
} else {

}
*/
?>


<?php

    require "../../../config.php";
    require "../../../dbconn.php";
    require "../../../class/settings.php"; 

    function debug($debug) {
        echo "<pre>";
        print_r($debug);
        exit;   
    }    

    function debugFile($debug) {
        $file = "./payment.log";
        file_put_contents($file, file_get_contents($file) . PHP_EOL . print_r($debug, true));
    }    

    function checkTxnid($txnid) {
        global $db;

        $results = $db->prepare('SELECT * FROM `payments` WHERE PA_txnid = :txnid');
        $results->bindParam(":txnid", $txnid);
        $results->execute();

        return !count($results->fetchAll(PDO::FETCH_ASSOC));
    }

    function addPayment($data) {
        global $db;

        if (is_array($data)) {

            $stmt = $db->prepare('INSERT INTO `payments` (
                PA_txnid, PA_payment_amount, PA_payment_status, PA_itemid, PA_createdtime, PA_user
            ) VALUES(
                :txnid, :payment_amount, :payment_status, :itemid, NOW(), :user_id
            )');
            
            $stmt->bindParam(":txnid", $data['txn_id']);
            $stmt->bindParam(":payment_amount", $data['payment_amount']);
            $stmt->bindParam(":payment_status", $data['payment_status']);
            $stmt->bindParam(":itemid", $data['item_number']);
            $stmt->bindParam(":user_id", $data['user_id']);
            $stmt->execute();

            return $db->lastInsertId();;
        }

        return false;
    }

    function verifyTransaction($data) {
        global $paypalUrl;

        $req = 'cmd=_notify-validate';
        foreach ($data as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
            $req .= "&$key=$value";
        }

        $ch = curl_init($paypalUrl);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);

        if (!$res) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }

        $info = curl_getinfo($ch);

        // Check the http response
        $httpCode = $info['http_code'];
        if ($httpCode != 200) {
            throw new Exception("PayPal responded with http code $httpCode");
        }

        curl_close($ch); 

        return $res;
    }

    $settings = new Settings();

    $url = $_SERVER["HTTP_ORIGIN"] . str_replace("/modules/installed/points/payments.php", "", $_SERVER["SCRIPT_NAME"]);


    // PayPal settings. Change these to your account details and the relevant URLs
    // for your site.
    $paypalConfig = array(
        'email' => $settings->loadSetting("paypalEmail"),
        'return_url' => $url . '/?page=points&action=successful',
        'cancel_url' => $url . '/?page=points&action=cancelled',
        'notify_url' => $url . '/modules/installed/points/payments.php'
    );

    $paypalUrl = 'https://www.paypal.com/cgi-bin/webscr';

    // Check if paypal request or response
    if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {

        // Grab the post data so that we can set up the query string for PayPal.
        // Ideally we'd use a whitelist here to check nothing is being injected into
        // our post data.
        $data = array();
        foreach ($_POST as $key => $value) {
            $data[$key] = stripslashes($value);
        }

        // Set the PayPal account.
        $data['business'] = $paypalConfig['email'];

        // Set the PayPal return addresses.
        $data['return'] = stripslashes($paypalConfig['return_url']);
        $data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
        $data['notify_url'] = stripslashes($paypalConfig['notify_url']);


        // Add any custom fields for the query string.
        $data['user_id'] = $_POST["userID"];

        // Build the query string from the data.
        $queryString = http_build_query($data);

        // Redirect to paypal IPN
        header('location:' . $paypalUrl . '?' . $queryString);
        exit();

    } else {
        // Assign posted variables to local data array.
        $data = array(
            'user_id' => explode("-", $_POST['invoice'])[0],
            'item_name' => $_POST['item_name'],
            'item_number' => $_POST['item_number'],
            'payment_status' => $_POST['payment_status'],
            'payment_amount' => $_POST['mc_gross'],
            'payment_currency' => $_POST['mc_currency'],
            'txn_id' => $_POST['txn_id'],
            'receiver_email' => $_POST['receiver_email'],
            'payer_email' => $_POST['payer_email'],
            'custom' => $_POST['custom'],
        );

        if (verifyTransaction($_POST) === 'VERIFIED' && checkTxnid($data['txn_id'])) {
            if (addPayment($data) !== false) {
                $item = $db->prepare("SELECT * FROM store WHERE ST_id =  :id");
                $item->bindParam(":id", $data["item_number"]);
                $item->execute();
                $item = $item->fetch(PDO::FETCH_ASSOC);

                if (
                    $data["payment_currency"] == $settings->loadSetting("currency") && 
                    $data["payment_amount"] == ($item["ST_cost"] / 100)
                ) {
                    $update = $db->prepare("
                        UPDATE userStats SET US_points = US_points + :points WHERE US_id = :id;
                        INSERT INTO notifications (
                            N_uid, 
                            N_time, 
                            N_text
                        ) VALUES (
                            :id, 
                            UNIX_TIMESTAMP(), 
                            'Your payment has been processed, you have received ".number_format($item["ST_points"])." ".$settings->loadSetting("pointsName")."'
                        );
                    ");
                    $update->bindParam(":points", $item["ST_points"]);
                    $update->bindParam(":id", $data["user_id"]);
                    $update->execute();
                } else {
                    $update = $db->prepare("
                        INSERT INTO notifications (N_uid, N_time, N_text) VALUES (:id, UNIX_TIMESTAMP(), 'There was a problem with your purchase please contact the admins!');
                    ");
                    $update->bindParam(":id", $data["user_id"]);
                    $update->execute();
                }
                
            }
        }
    }
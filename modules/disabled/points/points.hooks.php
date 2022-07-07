<?php

    new hook("pointsMenu", function () {
        $pointsName = _setting("pointsName");
        return array(
            "url" => "?page=points", 
            "text" => "Buy " . $pointsName
        );
    });

    new Hook("userAction", function ($action) {
        global $db;

        if ($action["module"] == "register") {
            $user = new User($action["user"]);
            $points = $db->selectAll("
                SELECT *
                FROM users 
                INNER JOIN userStats ON (U_id = US_id)
                WHERE U_email = :email AND US_points > 0
            ", array(
                ":email" => $user->info->U_email
            ));


            $added = 0;
            if ($points) {
                foreach ($points as $point) {
                    $u = new User($point["U_id"]);
                    $u->set("US_points", 0);
                    $user->add("US_points", $point["US_points"]);
                    $added += $point["US_points"];
                }
                $user->newNotification("You have been transfered ".number_format($added)." points from previous character!");
            }
        }
    });



new hook("adminWidget-html", function ($user) {
    
    global $db, $page;

    $name = _setting("pointsName");

    return array(
        "sort" => 1000,
        "size" => 12, 
        "html" => "<hr /><h2>Point Sales</h2>",
        "type" => "html", 
        "title" => false
    );

});



new hook("adminWidget-alerts", function ($user) {
    global $db, $page;
    $currency = _setting("currency");
    $currencySymbol = _setting("currencySymbol");
    $paypalEmail = _setting("paypalEmail");
    if (!$currency || !$currencySymbol || !$paypalEmail) return array( 
        "type" => "error", 
        "text" => "You need to set up your paypal payment information, To update it please go to the <a href='?page=admin&module=points&action=settings'>paypal settings</a>!"
    );
    return false;
});

new hook("adminWidget-chart", function ($user) {
    
    global $db, $page;

    $packages = $db->selectAll("
        SELECT
            ST_id as 'id', 
            ST_desc as 'desc', 
            COUNT(PA_id) as 'sales', 
            SUM(PA_payment_amount) as 'income'
        FROM store LEFT OUTER JOIN payments ON (ST_id = PA_itemid)
        GROUP BY ST_id
        ORDER BY ST_cost ASC
    ");

    $labels = array();
    $sales = array();
    $income = array();

    foreach ($packages as $p) {
        $labels[] = $p["desc"];
        $sales[] = $p["sales"];
        $income[] = $p["income"];
    }

    $data = array(
        "labels" => $labels,
        "datasets" => array(
            array(
                "backgroundColor" => "#6e2caf",
                "label" => "Sales", 
                "data" => $sales
            ),
            array(
                "backgroundColor" => "#77b300",
                "label" => "Income "._setting("currencySymbol"), 
                "data" => $income
            )
        )
    );

    return array(
        "sort" => 1001,
        "size" => 4, 
        "data" => array(
            "height" => "250px",
            "type" => "bar", 
            "data" => $data
        ),
        "title" => "Package Sales"
    );

});

new hook("adminWidget-chart", function ($user) {
    
    global $db, $page;

    $labels = array();
    $income = array();

    $lastYear = strtotime("-11 months");
    $start = strtotime(date("Y-m-01 00:00:00", $lastYear));

    $i = 0;
    while ($i < 12) {

        $label = date("M Y", $start);
        $end = strtotime($label . " +1 month");
        $labels[] = $label;

        $payments = $db->select("
            SELECT SUM(PA_payment_amount) as 'income' FROM payments WHERE PA_createdtime BETWEEN :from AND :to
        ", array(
            ":from" => date("Y-m-d 00:00:00", $start),
            ":to" => date("Y-m-d 00:00:00", $end-1)
        ));

        if ($payments) {
            $income[] = $payments["income"];
        } else {
            $income[] = 0;
        }

        $start = $end;
        $i++;
    }


    $data = array(
        "labels" => $labels,
        "datasets" => array(
            array(
                "backgroundColor" => "#77b300",
                "label" => "Income "._setting("currencySymbol"), 
                "data" => $income
            )
        )
    );

    return array(
        "sort" => 1002,
        "size" => 8, 
        "data" => array(
            "height" => "250px",
            "type" => "bar", 
            "data" => $data
        ),
        "title" => "Income - Last 12 months"
    );

});

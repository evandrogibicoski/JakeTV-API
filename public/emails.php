<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://us15.api.mailchimp.com/3.0/Lists/568ae6f9d2/members",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
      'email_address' => $_GET['email'],
      'status' => 'subscribed',
      'merge_fields' => [
          'FNAME' => $_GET['fname'],
          'LNAME' => $_GET['lname']
        ]
    ]),
  CURLOPT_HTTPHEADER => array(
    "authorization: apikey ac17ce75cc9383eefa2f0e32bf6ad4fb-us15",
    "cache-control: no-cache",
    "content-type: application/json",
    "postman-token: fbc1a89f-7ca1-89da-c955-6b799baed58d"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

header('content-type: application/json');

if ($err) {
  echo json_encode($err);
} else {
  echo $response;

  mysql_connect(getenv('DB_HOST'),getenv('DB_USER'),getenv('DB_PASS')) or die ("Oops! Connection fail.");
  mysql_select_db(getenv("DB_DATABASE")) or die ("Oops! No database found.");

  mysql_query("update tbl_user set subscribed = 1 where email = '{$_GET['email']}'");

}
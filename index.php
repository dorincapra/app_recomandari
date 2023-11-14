<?php
// index.php

// Replace these values with your Shopify App credentials
$api_key = "9aa83f2387758b26c60164f8caed7f4a";
$api_secret = "2785ef325fa19e4b0d60521b563ce217";
$shopify_domain = "simi-first-store.myshopify.com";

// Shopify API URL
$api_url = "https://$shopify_domain/admin/api/2021-10";

// Shopify API endpoint for retrieving products
$products_endpoint = "/products.json";

// Check if the app is installed
if (!isset($_GET['code'])) {
    // If not, redirect to the Shopify authorization URL
    $authorization_url = "https://$shopify_domain/admin/oauth/authorize?client_id=$api_key&scope=read_products&redirect_uri=https://your-app-url/callback";
    header("Location: $authorization_url");
    exit;
}

// Handle the installation process and obtain the access token
$access_token = handleInstallation($_GET['code'], $api_key, $api_secret, $shopify_domain);

// Build the URL for the API request with the access token
$api_request_url = $api_url . $products_endpoint . "?access_token=$access_token";

// Set up cURL to make the API request
$ch = curl_init($api_request_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

// Execute the API request
$response = curl_exec($ch);
curl_close($ch);

// Decode the JSON response
$products = json_decode($response, true);

// Output the product list
if (isset($products['products'])) {
    echo "<h1>Product List</h1>";
    echo "<ul>";
    foreach ($products['products'] as $product) {
        echo "<li>{$product['title']} - {$product['id']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Error retrieving products</p>";
}

// Your function to handle the installation and obtain the access token
function handleInstallation($code, $api_key, $api_secret, $shopify_domain) {
    $url = "https://$shopify_domain/admin/oauth/access_token";
    $data = array(
        'client_id' => $api_key,
        'client_secret' => $api_secret,
        'code' => $code
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    // $result['access_token'] contains the access token
    return $result['access_token'];
}
?>

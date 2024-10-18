# Orange SmartSMS API Client

A PHP client to interact with the Orange SmartSMS API. This package allows you to send SMS messages, check their delivery status, and monitor API usage limits.

## Features

- Send SMS messages to various networks.
- Check the delivery status of previously sent SMS messages.
- Retrieve the usage limit and available requests for the SmartSMS API.

## Installation

1. Install via Composer:

    ```bash
    composer require kwarcek/orange-smartsms-api
    ```

## Usage

### 1. Send an SMS Message

You can send an SMS message by using the `sendSMS` method. The message will be delivered to the recipient's mobile phone.

#### Example:

```php
use Kwarcek\OrangeSmartsmsApi\Requests\MessagingRequest;
use Kwarcek\OrangeSmartsmsApi\DTO\SMSMessage;
use GuzzleHttp\Client;

$isDev = getenv('APP_ENV');

// Create a new Guzzle client and MessagingRequest instance
$client = new Client([
    'base_uri' => $isDev ? 'https://apib2b-test.orange.pl/' : 'https://apib2b.orange.pl/',
]);
$apiKey = 'your-api-key-here';

$messagingRequest = new MessagingRequest($client, $apiKey);

// Define the SMS message
$message = new SMSMessage([
    'sender' => 'YourSenderID', 
    'recipient' => '48510123456', // Recipient's phone number
    'content' => 'Hello from Orange SmartSMS!',
]);

// Send the SMS
$response = $messagingRequest->sendSMS($message, true);

print_r($response);
```

### 2. Check SMS Delivery Status

You can check the delivery status of a sent SMS by passing the unique ID returned in the sendSMS response.
Example:

```php
use Kwarcek\OrangeSmartsmsApi\Requests\MessagingRequest;
use GuzzleHttp\Client;

$isDev = getenv('APP_ENV');

$client = new Client([
    'base_uri' => $isDev ? 'https://apib2b-test.orange.pl/' : 'https://apib2b.orange.pl/',
]);
$apiKey = 'your-api-key-here';

$messagingRequest = new MessagingRequest($client, $apiKey);

$id = '54510a5d0361'; // Example message ID
$response = $messagingRequest->checkDeliveryStatus($id);

print_r($response);
```

### 3. Check API Usage Limit

You can check the current API usage limit for SmartSMS, including used and available requests.
Example:

```php
use Kwarcek\OrangeSmartsmsApi\Requests\MessagingRequest;
use GuzzleHttp\Client;

$isDev = getenv('APP_ENV');

$client = new Client([
    'base_uri' => $isDev ? 'https://apib2b-test.orange.pl/' : 'https://apib2b.orange.pl/',
]);
$apiKey = 'your-api-key-here';

$messagingRequest = new MessagingRequest($client, $apiKey);

$response = $messagingRequest->checkLimit();

print_r($response);
```
<?php

namespace Kwarcek\OrangeSmartsmsApi\Test;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Kwarcek\OrangeSmartsmsApi\DTO\SMSMessage;
use Kwarcek\OrangeSmartsmsApi\Requests\MessagingRequest;
use PHPUnit\Framework\TestCase;

class MessagingMockRequestTest extends TestCase
{
    private ClientInterface $clientMock;
    private MessagingRequest $messagingRequest;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);

        $this->messagingRequest = new MessagingRequest($this->clientMock, 'tTGPMspDeGpdGQ8PLCyfdSD1jz5zQdZb');
    }

    public function testSendSMSSuccessful(): void
    {
        $responseBody = json_encode([
            'result' => 'OK',
            'id' => '123456789',
            'deliveryStatus' => 'DeliveredToNetwork'
        ]);

        $response = new Response(200, [], $responseBody);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', 'Messaging/v1/SmartSMS', $this->arrayHasKey('query'))
            ->willReturn($response);

        $message = new SMSMessage('test', '48510123456', 'Wiadomość testowa');

        $result = $this->messagingRequest->sendSMS($message, true);

        $this->assertEquals('OK', $result['result']);
        $this->assertEquals('123456789', $result['id']);
        $this->assertEquals('DeliveredToNetwork', $result['deliveryStatus']);
    }

    public function testCheckDeliveryStatusSuccessful(): void
    {
        $responseBody = json_encode([
            'result' => 'OK',
            'address' => '123456789',
            'deliveryStatus' => 'DeliveredToTerminal'
        ]);

        $response = new Response(200, [], $responseBody);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', 'Messaging/v1/SMSDeliveryStatus', $this->arrayHasKey('query'))
            ->willReturn($response);

        $result = $this->messagingRequest->checkDeliveryStatus('123456789');

        $this->assertEquals('OK', $result['result']);
        $this->assertEquals('123456789', $result['address']);
        $this->assertEquals('DeliveredToTerminal', $result['deliveryStatus']);
    }

    public function testCheckLimitSuccessful()
    {
        $responseBody = json_encode([
            'SmartSMS -global counter' => [
                'used' => '10',
                'available' => '990',
                'date' => '2024-10-05'
            ]
        ]);

        $response = new Response(200, [], $responseBody);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', 'Messaging/v1/SmartSMS/limit', $this->arrayHasKey('query'))
            ->willReturn($response);

        $result = $this->messagingRequest->checkLimit();

        $this->assertEquals('10', $result['used']);
        $this->assertEquals('990', $result['available']);
        $this->assertEquals('2024-10-05', $result['date']);
    }
}
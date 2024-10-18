<?php

namespace Kwarcek\OrangeSmartsmsApi\Requests;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Kwarcek\OrangeSmartsmsApi\DTO\SMSMessage;
use Kwarcek\OrangeSmartsmsApi\Exceptions\BadRequestException;
use Kwarcek\OrangeSmartsmsApi\Exceptions\Exception;
use Kwarcek\OrangeSmartsmsApi\Exceptions\ForbiddenException;
use Kwarcek\OrangeSmartsmsApi\Exceptions\InternalServerException;
use Kwarcek\OrangeSmartsmsApi\Exceptions\NotFoundException;
use Kwarcek\OrangeSmartsmsApi\Exceptions\UnathorizedException;

class MessagingRequest
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly string          $apiKey
    )
    {

    }

    /**
     * @param SMSMessage $message
     * @param bool $deliveryStatus
     * @return array{ result: string, id: string, deliveryStatus: string }
     * @throws BadRequestException
     * @throws Exception
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnathorizedException
     * @throws GuzzleException
     */
    public function sendSMS(SMSMessage $message, bool $deliveryStatus = false): array
    {
        try {
            $response = $this->client->request('GET', 'Messaging/v1/SmartSMS', [
                'query' => [
                    'from' => $message->sender,
                    'to' => $message->recipient,
                    'msg' => $message->content,
                    'deliverystatus' => $deliveryStatus ? 'true' : 'false',
                    'apikey' => $this->apiKey,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'result' => $data['result'],
                'id' => $data['id'],
                'deliveryStatus' => $data['deliveryStatus'] ?? null
            ];
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * @param string $id
     * @return array{ result: string, address: string, deliveryStatus: string }
     * @throws BadRequestException
     * @throws Exception
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnathorizedException
     * @throws GuzzleException
     */
    public function checkDeliveryStatus(string $id): array
    {
        try {
            $response = $this->client->request('GET', 'Messaging/v1/SMSDeliveryStatus', [
                'query' => [
                    'id' => $id,
                    'apikey' => $this->apiKey,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'result' => $data['result'],
                'address' => $data['address'],
                'deliveryStatus' => $data['deliveryStatus']
            ];
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * @return array{ used: string, available: string, date: string}
     * @throws BadRequestException
     * @throws Exception
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnathorizedException
     * @throws GuzzleException
     */
    public function checkLimit(): array
    {
        try {
            $response = $this->client->request('GET', 'Messaging/v1/SmartSMS/limit', [
                'query' => [
                    'apikey' => $this->apiKey,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'used' => $data['SmartSMS -global counter']['used'],
                'available' => $data['SmartSMS -global counter']['available'],
                'date' => $data['SmartSMS -global counter']['date']
            ];
        } catch (RequestException $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Handle errors in API requests by throwing custom exceptions based on status code.
     *
     * @param RequestException $exception
     * @return array
     * @throws BadRequestException
     * @throws Exception
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnathorizedException
     */
    private function handleError(RequestException $exception): array
    {
        $errorMessage = 'Network or server error';

        if ($exception->hasResponse()) {
            $statusCode = $exception->getResponse()->getStatusCode();
            $errorBody = json_decode($exception->getResponse()->getBody(), true);
            $errorMessage = $errorBody['description'] ?? 'Unknown error';

            switch ($statusCode) {
                case 400:
                    throw new BadRequestException($errorMessage);
                case 401:
                    throw new UnathorizedException($errorMessage);
                case 403:
                    throw new ForbiddenException($errorMessage);
                case 404:
                    throw new NotFoundException($errorMessage);
                case 500:
                    throw new InternalServerException($errorMessage);
                default:
                    throw new Exception($errorMessage);
            }
        }

        throw new Exception($errorMessage);
    }
}

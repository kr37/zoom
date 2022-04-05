<?php
namespace Zoom;

use Zoom\Client;
use Zoom\Config;

class Webinar
{
    private $client;
    public $zoomError;
    public function __construct()
    {
        $this->client = new Client();
    }

    public function getUserId()
    {
        $response = $this->client->doRequest(
            'GET',
            '/users/{userId}',
            [],
            ['userId' => Config::$email]
        );

        if ($this->client->responseCode() == 200) {
            return $response['id'];
        } else {
            print_r($response);
            exit();
        }
    }

    public function list($paramArray = [])
    {
        $response = $this->client->doRequest(
            'GET',
            '/users/{userId}/webinars',
            $paramArray,
            ['userId' => $this->getUserId()],
            json_encode(['action' => 'end'])
        );
        $responseCode = $this->client->responseCode();
        if ($responseCode == 200) {
            return $response;
        } else {
            $this->zoomError = $response;
            return false;
        }
    }

    public function instances($webinarId)
    {
        $response = $this->client->doRequest(
            'GET',
            '/past_webinars/{webinarId}/instances',
            [],
            ['webinarId' => $webinarId],
            json_encode(['action' => 'end'])
        );

        if ($this->client->responseCode() == 200) {
            return $response;
        } else {
            $this->zoomError = $response;
            return false;
        }
    }


    public function listParticipants($webinarId)
    {
        $response = $this->client->doRequest(
            'GET',
            '/past_webinars/{webinarId}/participants',
            [],
            ['webinarId' => $webinarId],
            json_encode(['action' => 'end'])
        );

        if ($this->client->responseCode() == 200) {
            return $response;
        } else {
            $this->zoomError = $response;
            return false;
        }
    }
    /** Add Registrant to meeting that require registration
     * @param $meetingId meeting id
     * @param Array $registrant {email:"required",first_name:"required"}
     * @return Array of response
     */
    public function addRegistrant($meetingId, $registrant)
    {
        $response = $this->client->doRequest(
            'POST',
            '/meetings/{meetingId}/registrants',
            [],
            ['meetingId' => $meetingId],
            json_encode($registrant)
        );

        if ($this->client->responseCode() == 201) {
            return $response;
        } else {
            $this->zoomError = $response;

            return false;
        }
    }
}

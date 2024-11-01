<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Message;
use File;

class WhatsAppController extends Controller
{
    public $messageDirectoryFolder = "json-messages";
    public $voiceMessageDirectoryFolder = "voice-messages";

    public function fetch(Request $request)
    {
        $yourNumber = $request->input('your_number');
        $friendNumber = $request->input('friend_number');

        // Generate QR Code by calling the Node.js API
        $qrCode = $this->generateQRCode();

        // Display the QR code and wait for the user to scan it
        return view('whatsapp_chat', compact('qrCode', 'friendNumber'));
    }

    public function getChat(Request $request)
    {
        $friendNumber = $request->input('friend_number');

        $client = new \GuzzleHttp\Client();
        $response = $client->post(env('NODE_SERVER_API_URL') . '/get-chat', [
            'json' => ['friend_number' => $friendNumber]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        if ($responseBody['success']) {

            // get max id table to set new json file 
            $lastId = Message::max('id');


            //check if the directory exists
            if (!File::isDirectory(storage_path($this->messageDirectoryFolder))) {
                //make the directory because it doesn't exists
                File::makeDirectory(storage_path($this->messageDirectoryFolder));
            }
            if (!File::isDirectory(storage_path($this->voiceMessageDirectoryFolder))) {
                //make the directory because it doesn't exists
                File::makeDirectory(storage_path($this->voiceMessageDirectoryFolder));
            }

            // filter arrays with attachment only 
            foreach ($responseBody['messages'] as $message) {
                if(isset($message['body']['attachment']) && $message['body']['attachment']['mimetype'] == "audio/ogg; codecs=opus" ) {
                  $filePath = storage_path($this->voiceMessageDirectoryFolder . "/" . $message['message_link'] . "_" . $message['datetime'] . ".ogg");
                    file_put_contents($filePath, base64_decode($message['body']['attachment']['data']));

                }
            }
            
            $jsonData = json_encode($responseBody['messages']);
            $filePath = storage_path($this->messageDirectoryFolder . '/order_' . ($lastId + 1) . '.json');
            file_put_contents($filePath, $jsonData);

            Message::create([
                'from' => $responseBody['from'],
                'to' => $responseBody['to'],
                'json' => $jsonData,
            ]);


            return view('whatsapp_chat', ['messages' => $responseBody['messages']]);
        } else {
            return back()->with('error', $responseBody['message']);
        }
    }
    public function getChat1(Request $request)
    {
        $friendNumber = $request->input('friend_number');

        // Call Node.js API to fetch chat messages
        $client = new Client();
        $response = $client->post(env('NODE_SERVER_API_URL') . '/get-chat', [
            'json' => [
                'friend_number' => $friendNumber
            ]
        ]);

        $chatData = json_decode($response->getBody(), true);

        if ($chatData['success']) {
            $messages = $chatData['messages'];
            return view('whatsapp_chat', compact('messages'));
        } else {
            return view('whatsapp_chat')->withErrors('Failed to fetch chat messages.');
        }
    }


    private function generateQRCode()
    {
        // Use Guzzle to send a POST request to the Node.js API
        $client = new Client();
        $response = $client->post('http://localhost:3000/generate-qr');

        // Get the QR code from the JSON response
        $qrData = json_decode($response->getBody(), true);

        return $qrData['qrCode'];
    }
}


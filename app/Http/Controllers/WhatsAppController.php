<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Message;

class WhatsAppController extends Controller
{
    public function fetch(Request $request)
    {
        $yourNumber = $request->input('your_number');
        $friendNumber = $request->input('friend_number');

        // Generate QR Code by calling the Node.js API
        $qrCode = $this->generateQRCode();

        // Display the QR code and wait for the user to scan it
        return view('whatsapp_chat', compact('qrCode', 'friendNumber'));
    }

    public function getChat(Request $request) {
        $friendNumber = $request->input('friend_number');

        $client = new \GuzzleHttp\Client();
        $response = $client->post( env('NODE_SERVER_API_URL').'/get-chat', [
            'json' => ['friend_number' => $friendNumber]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        if ($responseBody['success']) {
                $jsonData = json_encode($responseBody['messages']);
                $filePath = public_path('data.json');
                file_put_contents($filePath, $jsonData);
                 // get max id table
               $lastId = Message::max('id');
               var_dump($lastId);

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
        $response = $client->post(env('NODE_SERVER_API_URL').'/get-chat', [
            'json' => [
                'friend_number' => $friendNumber
            ]
        ]);

        $chatData = json_decode($response->getBody(), true);

        if ($chatData['success']) {
            $messages = $chatData['messages'];
            $filePath = public_path('data.json');
            //file_put_contents($filePath, $jsonData);

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


// whatsapp_fetch.js
const { Client } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');

const client = new Client();

client.on('qr', (qr) => {
    console.log('QR Code generated: ', qr);
    // You can pass this QR code back to your Laravel controller
});

client.on('ready', () => {
    console.log('Client is ready!');

    // Replace with the friend's number
    const chatId = 'yourfriendnumber@c.us';

    client.getChatById(chatId).then(chat => {
        chat.fetchMessages({ limit: 10 }).then(messages => {
            messages.forEach(msg => {
                console.log(msg.body);
            });
            // Send these messages back to Laravel for display
        });
    });
});

client.initialize();

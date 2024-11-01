const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const qrCode = require('qrcode');

const app = express();
const port = 3000;

app.use(express.json());

const client = new Client({
    authStrategy: new LocalAuth()
});

app.post('/generate-qr', async (req, res) => {
    client.on('qr', async (qr) => {
        // Generate QR code in base64 format
        const qrImage = await qrCode.toDataURL(qr);
        res.json({ qrCode: qrImage });
    });

    client.on('ready', () => {
        console.log('WhatsApp client is ready');
    });

    client.initialize();
});

client.on('ready', async () => {
    try {
        const chat = await client.getChatById('9849409161@c.us');
        console.log(chat);
    } catch (error) {
        console.error('Error fetching chat:', error);
    }
});

// Route to get chat messages
app.post('/get-chat', async (req, res) => {
    if (!client.info || !client.info.wid) {
        return res.status(500).json({
            success: false,
            message: 'WhatsApp client is not ready yet. Please wait.'
        });
    }

    const friendNumber = req.body.friend_number + '@c.us';

    try {
        const chat = await client.getChatById(friendNumber);

        if (chat) {
            const messages = await chat.fetchMessages({ limit: 20 });
            console.log("🚀 ~ app.post ~ messages:", messages)
            const promises = messages.map(async (msg) => {
                return {
                    from: msg.from,
                    to: msg.to,
                    body: { 
                        text: msg.body,
                        attachment: await msg.downloadMedia(),
                    },
                    datetime: msg.timestamp,
                    message_link: msg.id.id,

                }
            });
            const results = await Promise.all(promises);
            res.json({
                success: true,
                messages: results,
            })

        } else {
            res.json({
                success: false,
                message: 'Chat not found'
            });
        }
    } catch (error) {
        console.error('Detailed error:', error);
        res.status(500).json({
            success: false,
            message: 'An error occurred while fetching the chat',
            error: error.message
        });
    }
});


app.listen(port, () => {
    console.log(`WhatsApp QR API running on port ${port}`);
    
    client.on('ready', () => {
        console.log('WhatsApp client is ready');
    });
    client.initialize();
    
});

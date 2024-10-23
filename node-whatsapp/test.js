const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const qrcode = require('qrcode');

const app = express();
const port = 3000;

app.use(express.json());

const client = new Client({
    authStrategy: new LocalAuth()
});

app.post('/generate-qr', async (req, res) => {
    client.on('qr', async (qr) => {
        // Generate QR code in base64 format
        const qrImage = await qrcode.toDataURL(qr);

        res.json({ qrCode: qrImage });
    });

    client.on('ready', () => {
        console.log('WhatsApp client is ready');
    });

    client.initialize();
});

client.on('ready', async () => {
    try {
        const chat = await client.getChatById('923442854313@c.us');
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
            const messages = await chat.fetchMessages({ limit: 50 });
            res.json({
                success: true,
                messages: messages.map(msg => ({
                    from: msg.from,
                    body: msg.body,
                    timestamp: msg.timestamp
                }))
            });
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
});

// app.post('/get-chat', async (req, res) => {
//     if (!client.info || !client.info.wid) {
//         return res.status(500).json({
//             success: false,
//             message: 'WhatsApp client is not ready yet. Please wait.'
//         });
//     }
//
//     const friendNumber = req.body.friend_number + '@c.us';
//
//     try {
//         const chat = await client.getChatById(friendNumber);
//
//         if (chat) {
//             let messages;
//             const fetchAll = req.body.fetch_all === 'on'; // Checkbox value will be 'on' when checked
//
//             if (fetchAll) {
//                 // Fetch all messages
//                 messages = await chat.fetchMessages();
//             } else {
//                 // Fetch messages based on date range
//                 const startDate = new Date(req.body.start_date);
//                 const endDate = new Date(req.body.end_date);
//                 messages = await chat.fetchMessages({ limit: 1000 }); // Fetch up to 1000 messages
//
//                 // Filter messages based on date range
//                 messages = messages.filter(msg => {
//                     const msgDate = new Date(msg.timestamp * 1000); // Convert timestamp to Date
//                     return msgDate >= startDate && msgDate <= endDate;
//                 });
//             }
//
//             res.json({
//                 success: true,
//                 messages: messages.map(msg => ({
//                     from: msg.from,
//                     body: msg.body,
//                     timestamp: msg.timestamp
//                 }))
//             });
//         } else {
//             res.json({
//                 success: false,
//                 message: 'Chat not found'
//             });
//         }
//     } catch (error) {
//         console.error('Detailed error:', error);
//
//         res.status(500).json({
//             success: false,
//             message: 'An error occurred while fetching the chat',
//             error: error.message
//         });
//     }
// });


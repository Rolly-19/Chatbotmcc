import express from 'express';
import cors from 'cors'; // Import cors middleware
import { CohereClient } from 'cohere-ai';
import dotenv from 'dotenv';
// Load environment variables from .env file
dotenv.config();

const app = express();
const port = 3000;

// Use the CORS middleware
app.use(cors()); // Enable CORS for all origins

const cohere = new CohereClient({
    token: process.env.COHERE_API_KEY,
});

// Middleware to parse JSON bodies
app.use(express.json());

// Function to replace newline characters with <br> tags
function replaceNewlinesWithBr(text) {
    return text.replace(/\n/g, '<br>');
}

// Define a route
app.post('/ask', async (req, res) => {
    try {
        const { message } = req.body;

        const response = await cohere.chat({
            message,
            connectors: [{ id: 'web-search' }],
        });

        // Replace newline characters in the response
        const formattedResponse = {
            text: replaceNewlinesWithBr(response.text)
        };

        res.json(formattedResponse);
    } catch (error) {
        console.error('Error:', error);
        res.status(500).send('Internal Server Error');
    }
});



app.listen(port, () => {
    console.log(`Server is running on http://localhost:${port}`);
});

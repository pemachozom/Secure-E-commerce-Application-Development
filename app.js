const express = require('express');
const bodyParser = require('body-parser');
const winston = require('winston');
// const MongoDB = require('winston-mongodb').MongoDB;

const app = express();
app.use(bodyParser.json());

// Configure Winston
const logger = winston.createLogger({
  level: 'error',
  format: winston.format.json(),
  transports: [
    new winston.transports.File({ filename: 'logs/error.log' }),
    // new MongoDB({
    //   db: 'mongodb://12210024gcit:qwertyuiop@cluster0.7eaqbsc.mongodb.net/',
    //   options: { useUnifiedTopology: true },
    //   collection: 'error_logs',
    // }),
  ],
});

// Endpoint to receive error logs from PHP
app.post('/logs', (req, res) => {
  try {
    const { level, message } = req.body;
    logger.log(level, message);
    res.status(200).send('Log received successfully.');
  } catch (error) {
    res.status(500).send('Internal Server Error');
  }
});

const PORT = 8080;
app.listen(PORT, () => {
  console.log(`Server listening on port ${PORT}`);
});

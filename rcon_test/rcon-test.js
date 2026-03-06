const WebSocket = require('ws');

const ws = new WebSocket('ws://37.230.137.209:38015/xT8vR2sD6jQz');

ws.on('open', () => {
  console.log('Connected');
  ws.send(JSON.stringify({
    Identifier: 0,
    Message: 'status',
    Type: 3
  }));
});

ws.on('message', (data) => {
  console.log('Response:', data.toString());
});

ws.on('error', (err) => console.error('Error:', err));
ws.on('close', () => console.log('Closed'));
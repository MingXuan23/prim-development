var app = require('express')();
var http = require('http').createServer(app);
var io = require('socket.io')(http, {
  cors: {
    origin: "http://prim-development.herokuapp.com",
    methods: ["GET", "POST"]
  }
});

app.get('/', (req, res) => {
    res.sendFile(__dirname + '/index.html');
});

http.listen((process.env.PORT || 8081), () => {
    console.log('listening on *:8081');
  });

io.on('connection', socket => {
  socket.on('new-user', id => {
    socket.join(id)
  })
  socket.on('send-chat-message', data => {
    socket.to(data.to).emit('chat-message', { message: data.message, name: data.name })
  })
  // socket.on('disconnect', () => {
  //   socket.broadcast.emit('user-disconnected', )
  // })
})
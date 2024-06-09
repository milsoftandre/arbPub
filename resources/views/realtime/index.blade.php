<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Time Client</title>
</head>
<body>
<h1>Real Time Client</h1>

<ul id="messages-list"></ul>

<script>
    const messagesList = document.getElementById('messages-list');



    function listenForEvents() {
        const eventSource = new EventSource('/work/1arb/real-time-listener');

        eventSource.onmessage = function (event) {
            const data = JSON.parse(event.data);
            if(data.message){
            const messageItem = document.createElement('li');
            messageItem.style.whiteSpace = 'pre';
            messageItem.textContent = data.message;
            messagesList.appendChild(messageItem);
            }
        };

        eventSource.onerror = function (error) {
            console.error('Error:', error);
        };
    }

    // Вызывайте listenForEvents при загрузке страницы или по необходимости
    listenForEvents();
</script>
</body>
</html>

import Echo from "laravel-echo";

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'null',
});

new Vue({
    el: '#app',
    data: {
        messages: []
    },
    mounted() {
        this.listenForMessages();
    },
    methods: {
        listenForMessages() {
            window.Echo.channel('real-time-channel')
                .listen('RealTimeEvent', (event) => {
                    this.messages.push(event.data.message);
                });
        }
    }
});

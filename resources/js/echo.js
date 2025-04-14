// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// // Only create Echo instance if we have the required configuration
// if (import.meta.env.VITE_REVERB_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY) {
//     window.Echo = new Echo({
//         broadcaster: import.meta.env.VITE_REVERB_APP_KEY ? 'reverb' : 'pusher',
//         key: import.meta.env.VITE_REVERB_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY || 'app-key',
//         wsHost: import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
//         wsPort: import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT || 80,
//         wssPort: import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT || 443,
//         forceTLS: ((import.meta.env.VITE_REVERB_SCHEME || import.meta.env.VITE_PUSHER_SCHEME || 'https') === 'https'),
//         enabledTransports: ['ws', 'wss'],
//         cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
//     });
// } else {
//     // Provide a dummy Echo implementation to prevent errors
//     window.Echo = {
//         channel: () => ({
//             listen: () => {}
//         }),
//         private: () => ({
//             listen: () => {}
//         }),
//         join: () => ({
//             listen: () => {}
//         })
//     };

//     // Provide a dummy Pusher instance to prevent errors
//     window.Pusher.logToConsole = false;
// }

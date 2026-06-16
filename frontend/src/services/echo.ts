import Echo from "laravel-echo";
import Pusher from "pusher-js";

console.log("ECHO LOADED");
declare global {
  interface Window {
    Pusher: typeof Pusher;
  }
}

window.Pusher = Pusher;

const echo = new Echo({
  broadcaster: "reverb",
  key: "empsqvxogipqgsyguibn",
  wsHost: "localhost",
  wsPort: 8080,
  forceTLS: false,
  enabledTransports: ["ws"],
});

echo.connector.pusher.connection.bind(
  "connected",
  () => {
    console.log("REVERB CONNECTED");
  }
);

echo.connector.pusher.connection.bind(
  "error",
  (err: unknown) => {
    console.log("REVERB ERROR", err);
  }
);

export default echo;
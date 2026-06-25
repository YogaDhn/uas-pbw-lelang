import Echo from "laravel-echo";
import Pusher from "pusher-js";

declare global {
  interface Window {
    Pusher: typeof Pusher;
  }
}

window.Pusher = Pusher;

const token = localStorage.getItem("token");

const echo = new Echo({
  broadcaster: "reverb",
  key: "empsqvxogipqgsyguibn",

  wsHost: "localhost",
  wsPort: 8080,

  forceTLS: false,
  enabledTransports: ["ws"],

  authEndpoint: "http://127.0.0.1:8000/broadcasting/auth",

  auth: {
    headers: {
    get Authorization() {
      return `Bearer ${localStorage.getItem("token")}`;
    },
    Accept: "application/json",
    },
  },
});

echo.connector.pusher.connection.bind("connected", () => {
  console.log("REVERB CONNECTED");
});

echo.connector.pusher.connection.bind("error", (err: unknown) => {
  console.log("REVERB ERROR", err);
});

export default echo;
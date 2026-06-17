import { defineStore } from "pinia";
import api from "../services/api";

export const useAuthStore = defineStore(
  "auth",
  {
    state: () => ({
      user: null,
      token: localStorage.getItem("token")
    }),

    actions: {
      async login(email, password) {
  const response = await api.post(
    "/login",
    {
      email,
      password
    }
  );

  this.token = response.data.token;
  this.user = response.data.user;

  localStorage.setItem(
    "token",
    response.data.token
  );

  localStorage.setItem(
    "user",
    JSON.stringify(response.data.user)
  );
}
    }
  }
);
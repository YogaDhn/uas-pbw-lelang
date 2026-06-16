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

        this.token =
          response.data.token;

        localStorage.setItem(
          "token",
          response.data.token
        );
      }
    }
  }
);
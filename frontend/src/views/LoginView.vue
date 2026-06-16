<script setup lang="ts">
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

const email = ref("");
const password = ref("");

const login = async () => {
  try {
    await auth.login(
      email.value,
      password.value
    );

    alert("Login berhasil");

    router.push("/");
  } catch (error) {
    console.log("ERROR LOGIN:");
    console.log(error);

    if (error.response) {
      console.log("STATUS:");
      console.log(error.response.status);

      console.log("DATA:");
      console.log(error.response.data);

      alert(
        error.response.data.message ||
        "Login gagal"
      );
    } else {
      alert("Tidak dapat terhubung ke server");
    }
  }
};
</script>

<template>
  <div>
    <h1>Login</h1>

    <input
      v-model="email"
      type="email"
      placeholder="Email"
    />

    <br /><br />

    <input
      v-model="password"
      type="password"
      placeholder="Password"
    />

    <br /><br />

    <button @click="login">
      Login
    </button>
  </div>
</template>
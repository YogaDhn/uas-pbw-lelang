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

  } catch (error: unknown) {

    console.log(error);

    alert(
      error?.response?.data?.message ??
      "Login gagal"
    );
  }
};

</script>

<template>
  <div class="login-page">

    <div class="login-card">

      <h2>Login</h2>

      <input v-model="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Password" />

      <button @click="login">
        Masuk
      </button>
      <p class="link">
  Belum punya akun?

  <router-link to="/register">
    Register
  </router-link>
</p>

    </div>

  </div>
</template>

<style scoped>
.login-page {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f4f6f8;
  font-family: sans-serif;
}

.login-card {
  width: 320px;
  padding: 30px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  text-align: center;
}

.login-card h2 {
  margin-bottom: 20px;
}

.login-card input {
  width: 100%;
  margin-bottom: 10px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.login-card button {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 8px;
  background: #2563eb;
  color: white;
  cursor: pointer;
}

.login-card button:hover {
  background: #1e4fd1;
}
</style>
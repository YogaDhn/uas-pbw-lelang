<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");

const register = async () => {
  try {

    await api.post("/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password.value
    });

    alert("Register berhasil");

    router.push("/login");

  } catch (error: any) {

    console.log(error);

    alert(
      error.response?.data?.message ??
      "Register gagal"
    );

  }
};
</script>

<template>
  <div class="auth-page">

    <div class="auth-card">

      <h1>📝 Register</h1>

      <input
        v-model="name"
        placeholder="Nama"
      />

      <input
        v-model="email"
        placeholder="Email"
      />

      <input
        v-model="password"
        type="password"
        placeholder="Password"
      />

      <button @click="register">
        Daftar
      </button>

      <p class="link">
        Sudah punya akun?

        <router-link to="/login">
          Login
        </router-link>
      </p>

    </div>

  </div>
</template>

<style scoped>
.auth-page{
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:#f8fafc;
}

.auth-card{
  width:400px;
  background:white;
  padding:30px;
  border-radius:16px;
  box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.auth-card h1{
  margin-bottom:20px;
}

input{
  width:100%;
  padding:12px;
  margin-bottom:12px;
  border:1px solid #ddd;
  border-radius:10px;
}

button{
  width:100%;
  padding:12px;
  border:none;
  border-radius:10px;
  background:#2563eb;
  color:white;
  cursor:pointer;
  font-weight:600;
}

.link{
  margin-top:15px;
  text-align:center;
}
</style>
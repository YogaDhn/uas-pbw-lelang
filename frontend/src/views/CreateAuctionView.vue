<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

const router = useRouter();

const title = ref("");
const description = ref("");
const starting_price = ref(0);
const bid_increment = ref(0);
const start_time = ref("");
const end_time = ref("");
const image = ref<File | null>(null);
const handleImage = (e: Event) => {
  const target = e.target as HTMLInputElement;

  if (target.files?.length) {
    image.value = target.files[0];
  }
};

const createAuction = async () => {
  try {

    const formData = new FormData();

    formData.append("title", title.value);
    formData.append("description", description.value);

    formData.append(
      "starting_price",
      String(starting_price.value)
    );

    formData.append(
      "bid_increment",
      String(bid_increment.value)
    );

    formData.append(
      "start_time",
      start_time.value
    );

    formData.append(
      "end_time",
      end_time.value
    );

    if (image.value) {
      formData.append(
        "image",
        image.value
      );
    }

    await api.post(
      "/auctions",
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data"
        }
      }
    );

    alert("Lelang berhasil dibuat");

    router.push("/");

  } catch (error: any) {

    console.log("ERROR DETAIL:", error.response?.data);

    alert(
      error.response?.data?.message ??
      "Gagal membuat lelang"
    );
  }
};
</script>

<template>
  <div class="form-page">
  <button
      class="back-btn"
      @click="$router.push('/')"
    >
      ← Kembali ke Daftar Lelang
    </button>
    <h1>➕ Tambah Barang Lelang</h1>

    <input v-model="title" placeholder="Nama barang" />
    <input
  type="file"
  accept="image/*"
  @change="handleImage"
/>
    <textarea v-model="description" placeholder="Deskripsi"></textarea>

    <input v-model="starting_price" type="number" placeholder="Harga awal" />
    <input v-model="bid_increment" type="number" placeholder="Increment bid" />

    <input v-model="start_time" type="datetime-local" />
    <input v-model="end_time" type="datetime-local" />

    <button @click="createAuction">
      Simpan Lelang
    </button>

  </div>
</template>

<style scoped>
.form-page {
  max-width: 500px;
  margin: auto;
  padding: 20px;
  font-family: sans-serif;
}

input, textarea {
  width: 100%;
  margin-bottom: 10px;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

button {
  width: 100%;
  padding: 10px;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
}
</style>
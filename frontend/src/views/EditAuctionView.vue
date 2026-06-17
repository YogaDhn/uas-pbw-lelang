<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../services/api";

const route = useRoute();
const router = useRouter();

const title = ref("");
const description = ref("");
const starting_price = ref(0);
const bid_increment = ref(0);
const start_time = ref("");
const end_time = ref("");

const loadAuction = async () => {
  const res = await api.get(
    `/auctions/${route.params.id}`
  );

  title.value = res.data.title;
description.value = res.data.description;
starting_price.value = res.data.starting_price;
bid_increment.value = res.data.bid_increment;

start_time.value = res.data.start_time?.slice(0,16);
end_time.value = res.data.end_time?.slice(0,16);
};

const updateAuction = async () => {
  try {

    await api.put(`/auctions/${route.params.id}`, {
  title: title.value,
  description: description.value,
  starting_price: starting_price.value,
  bid_increment: bid_increment.value,
  start_time: start_time.value,
  end_time: end_time.value
});

    alert("Lelang berhasil diperbarui");

    router.push("/my-auctions");

  } catch (error) {
    console.log(error);
    alert("Gagal update");
  }
};

onMounted(loadAuction);
</script>

<template>
  <div class="edit-page">

    <div class="edit-card">

      <div class="header">
        <h1>✏️ Edit Lelang</h1>
        <p>Perbarui informasi lelang Anda</p>
      </div>

      <div class="form-group">
        <label>Nama Barang</label>
        <input
          v-model="title"
          type="text"
          placeholder="Masukkan nama barang"
        />
      </div>

      <div class="form-group">
        <label>Deskripsi</label>
        <textarea
          v-model="description"
          rows="5"
          placeholder="Masukkan deskripsi barang"
        ></textarea>
        <input
  v-model="starting_price"
  type="number"
  placeholder="Harga Awal"
/>

<input
  v-model="bid_increment"
  type="number"
  placeholder="Kenaikan Bid"
/>

<input
  v-model="start_time"
  type="datetime-local"
/>

<input
  v-model="end_time"
  type="datetime-local"
/>
      </div>
      

      <div class="button-group">

        <button
          class="btn-secondary"
          @click="$router.back()"
        >
          Batal
        </button>

        <button
          class="btn-primary"
          @click="updateAuction"
        >
          Simpan Perubahan
        </button>

      </div>

    </div>

  </div>
</template>
<style scoped>
.edit-page {
  min-height: 100vh;
  background: #f5f7fb;
  padding: 40px 20px;
  display: flex;
  justify-content: center;
}

.edit-card {
  width: 100%;
  max-width: 700px;
  background: white;
  padding: 32px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,.08);
}

.header {
  margin-bottom: 24px;
}

.header h1 {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
  color: #111827;
}

.header p {
  color: #6b7280;
  margin-top: 6px;
}

.form-group {
  margin-bottom: 18px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #374151;
}

input,
textarea {
  width: 100%;
  padding: 14px;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  font-size: 15px;
  font-family: sans-serif;
  transition: .2s;
}

input:focus,
textarea:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 4px rgba(37,99,235,.12);
}

.button-group {
  display: flex;
  gap: 12px;
  margin-top: 24px;
}

.btn-primary,
.btn-secondary {
  flex: 1;
  padding: 14px;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  font-weight: 600;
  transition: .2s;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-secondary {
  background: #f3f4f6;
  color: #111827;
}

.btn-secondary:hover {
  background: #e5e7eb;
}
</style>
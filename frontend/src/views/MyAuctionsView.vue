<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../services/api";
import { useRouter } from "vue-router";
const auctions = ref([]);
const router = useRouter();
const loadAuctions = async () => {
  const res = await api.get("/my-auctions");
  auctions.value = res.data;
};

const deleteAuction = async (id: number) => {
  const confirmDelete = confirm(
    "Yakin ingin menghapus lelang?"
  );

  if (!confirmDelete) return;

  try {
    await api.delete(`/auctions/${id}`);

    alert("Lelang berhasil dihapus");

    loadAuctions();
  } catch (error) {
    console.log(error);
    alert("Gagal menghapus lelang");
  }
};

const editAuction = (id: number) => {
  router.push(`/auctions/${id}/edit`);
};

onMounted(loadAuctions);
</script>

<template>
  <div class="page">
 <button
      class="back-btn"
      @click="$router.push('/')"
    >
      ← Kembali ke Daftar Lelang
    </button>
    <h1>📦 Lelang Saya</h1>

    <div
      v-for="auction in auctions"
      :key="auction.id"
      class="card"
    >
    <div class="action-buttons">
  <button
    class="edit-btn"
    @click="editAuction(auction.id)"
  >
    ✏️ Edit
  </button>

  <button
    class="delete-btn"
    @click="deleteAuction(auction.id)"
  >
    🗑 Hapus
  </button>
</div>
      <h3>{{ auction.title }}</h3>

      <p>{{ auction.description }}</p>

      <p>Status: {{ auction.status }}</p>

      <p>
        Harga Awal:
        Rp {{ auction.starting_price }}
      </p>
    </div>

  </div>
</template>

<style scoped>
.back-btn{
  background:#2563eb;
  color:white;
  border:none;
  padding:10px 16px;
  border-radius:10px;
  cursor:pointer;
  margin-bottom:20px;
  font-weight:600;
  transition:.2s;
}

.back-btn:hover{
  background:#1d4ed8;
}
.page{
  max-width:900px;
  margin:auto;
  padding:20px;
}

.card{
  background:white;
  padding:16px;
  border-radius:12px;
  margin-bottom:12px;
  box-shadow:0 4px 15px rgba(0,0,0,.08);
}
.action-buttons {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.edit-btn,
.delete-btn {
  flex: 1;
  border: none;
  border-radius: 10px;
  padding: 10px;
  cursor: pointer;
  font-weight: 600;
}

.edit-btn {
  background: #2563eb;
  color: white;
}

.delete-btn {
  background: #ef4444;
  color: white;
}
</style>
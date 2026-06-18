<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";


const router = useRouter();

const auctions = ref<unknown[]>([]);
const search = ref("");
const loading = ref(true);



const loadAuctions = async () => {
  loading.value = true;
  const res = await api.get("/auctions");
  auctions.value = res.data;
  loading.value = false;
};




const getHighestBid = (auction: unknown) => {
  if (!auction.bids || auction.bids.length === 0) {
    return auction.starting_price;
  }
  return auction.bids[0].amount;
};

const goDetail = (id: number) => {
  router.push(`/auctions/${id}`);
};

const logout = async () => {
  try {
    await api.post("/logout");
  } catch (error) {
    console.log(error);
  }

  localStorage.removeItem("token");
  localStorage.removeItem("user");

  router.push("/login");
};

const filteredAuctions = computed(() => {

  if (!search.value) {
    return auctions.value;
  }

  return auctions.value.filter((auction: any) =>
    auction.title
      .toLowerCase()
      .includes(search.value.toLowerCase())
  );

});

onMounted(() => {
  loadAuctions();
});


</script>

<template>
<div class="navbar">
  <div class="logo">
    🏷️ Lelang App
  </div>

  <button
    class="btn-logout"
    @click="logout"
  >
    Logout
  </button>
</div>
  <div class="page">

    <div class="header">

  <div class="header-left">
    <h1>🏷️ Daftar Lelang</h1>
    <p>Temukan barang lelang terbaik dan ikut bid sekarang</p>
  <div class="search-actions">

  <div class="search-box">
    <input
      v-model="search"
      type="text"
      placeholder="🔍 Cari barang lelang..."
    />
  </div>

  <div class="actions">

    <router-link
      to="/auctions/create"
      class="btn-primary"
    >
      Buat Lelang
    </router-link>

    <router-link
      to="/my-auctions"
      class="btn-secondary"
    >
      Lelang Saya
    </router-link>

  </div>

</div>
  </div>
  


</div>

    <div v-if="loading" class="loading-card">
      Loading data...
    </div>

    <div v-else class="grid">

      <div
        v-for="auction in filteredAuctions"
        :key="auction.id"
        class="card"
      >
<img
  v-if="auction.image"
  :src="`http://127.0.0.1:8000/storage/${auction.image}`"
  class="auction-image"
/>
        <div class="top">
          <h2>{{ auction.title }}</h2>

          <span
            class="badge"
            :class="auction.status"
          >
            {{ auction.status }}
          </span>
        </div>

        <p class="desc">{{ auction.description }}</p>

        <div>
  ⏱ {{
    new Date(auction.end_time).toLocaleString("id-ID", {
      dateStyle: "medium",
      timeStyle: "short"
    })
  }}
</div>

        <div class="price">
          🔥 Rp {{ getHighestBid(auction) }}
        </div>

        <button class="btn" @click="goDetail(auction.id)">
          Lihat Detail
        </button>

      </div>

    </div>

  </div>
</template>
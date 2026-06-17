<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../services/api";

const auctions = ref([]);

const load = async () => {
  const res = await api.get("/auctions");
  auctions.value = res.data;
};

onMounted(load);
</script>

<template>
  <div>
    <h1>Dashboard Lelang</h1>

    <div v-for="a in auctions" :key="a.id" class="card">
      <h3>{{ a.title }}</h3>
      <p>Status: {{ a.status }}</p>
      <p>Harga Awal: Rp {{ a.starting_price }}</p>
    </div>
  </div>
</template>

<style scoped>
.card {
  padding: 12px;
  margin-bottom: 10px;
  border: 1px solid #ddd;
  border-radius: 10px;
}
</style>
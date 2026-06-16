<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "../services/api";

const auctions = ref([]);

const loadAuctions = async () => {
  try {
    const response = await api.get(
      "/auctions"
    );

    auctions.value = response.data;
  } catch (error) {
    console.log(error);
  }
};

onMounted(() => {
  loadAuctions();
});
</script>

<template>
  <div>
    <h1>Daftar Lelang</h1>

    <div
      v-for="auction in auctions"
      :key="auction.id"
      style="
        border:1px solid #ccc;
        padding:15px;
        margin-bottom:10px;
      "
    >
      <RouterLink
        :to="'/auction/' + auction.id"
      >
        <h3>
          {{ auction.title }}
        </h3>
      </RouterLink>

      <p>
        {{ auction.description }}
      </p>

      <p>
        Harga Awal:
        Rp {{ auction.starting_price }}
      </p>

      <p>
        Status:
        {{ auction.status }}
      </p>
    </div>
  </div>
</template>
<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import api from "../services/api";
import echo from "../services/echo";
const route = useRoute();

const auction = ref<unknown>(null);
const bidAmount = ref("");

const loadAuction = async () => {
  const response = await api.get(
    `/auctions/${route.params.id}`
  );

  auction.value = response.data;
};

const placeBid = async () => {
  try {
    const response = await api.post(
      `/auctions/${route.params.id}/bids`,
      {
        amount: bidAmount.value,
      }
    );

    alert(response.data.message);

    bidAmount.value = "";

    await loadAuction();
  } catch (error: unknown) {
    console.log(error);

    alert(
      error.response?.data?.message ||
      "Bid gagal"
    );
  }
};

onMounted(async () => {
  await loadAuction();

  updateCountdown();

  setInterval(() => {
    updateCountdown();
  }, 1000);

  echo
    .channel(`auction.${route.params.id}`)
    .listen(".bid.placed", (event) => {
  console.log(
    "BID REALTIME BERHASIL",
    event
  );

  loadAuction();
    });
});
const countdown = ref("");

const updateCountdown = () => {
  if (!auction.value) return;

  const endTime = new Date(
    auction.value.end_time
  ).getTime();

  const now = new Date().getTime();

  const distance = endTime - now;

  if (distance <= 0) {
    countdown.value = "Lelang Selesai";
    return;
  }

  const days = Math.floor(
    distance / (1000 * 60 * 60 * 24)
  );

  const hours = Math.floor(
    (distance % (1000 * 60 * 60 * 24))
    / (1000 * 60 * 60)
  );

  const minutes = Math.floor(
    (distance % (1000 * 60 * 60))
    / (1000 * 60)
  );

  const seconds = Math.floor(
    (distance % (1000 * 60))
    / 1000
  );

  countdown.value =
    `${days}h ${hours}j ${minutes}m ${seconds}d`;
};

</script>

<template>
  <div v-if="auction">
    <h1>{{ auction.title }}</h1>

    <p>{{ auction.description }}</p>

    <p>
      Harga Awal:
      Rp {{ auction.starting_price }}
    </p>

    <p>
      Increment:
      Rp {{ auction.bid_increment }}
    </p>

    <p>
      Status:
      {{ auction.status }}
    </p>
<p>
  Countdown:
  {{ countdown }}
</p>
    <h2>Pasang Bid</h2>

    <input
      v-model="bidAmount"
      type="number"
      placeholder="Masukkan nominal bid"
    />

    <button @click="placeBid">
      Bid Sekarang
    </button>

    <br /><br />

    <h2>Bid Tertinggi</h2>

    <div v-if="auction.bids.length > 0">
      Rp {{ auction.bids[0].amount }}
    </div>

    <div v-else>
      Belum ada bid
    </div>

    <h2>Riwayat Bid</h2>

    <div v-if="auction.bids.length === 0">
      Belum ada bid
    </div>

    <div
      v-for="bid in auction.bids"
      :key="bid.id"
      style="
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
      "
    >
      <p>User ID: {{ bid.user_id }}</p>
      <p>Nominal: Rp {{ bid.amount }}</p>
    </div>
  </div>
</template>
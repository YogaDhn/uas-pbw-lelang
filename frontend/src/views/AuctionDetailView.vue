<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import api from "../services/api";
import echo from "../services/echo";

type Auction = {
  id: number;
  title: string;
  description: string;
  end_time: string;
  starting_price: number;
  bid_increment: number;
  status: string;
  bids: { id: number; user_id: number; amount: number }[];
};

type AuctionEndedEvent = {
  winner?: string;
  winning_bid?: number;
};

const route = useRoute();
const auctionId = route.params.id as string;

const auction = ref<Auction | null>(null);
const bidAmount = ref<string>("");
const notification = ref<string | null>(null);
const winner = ref<string | null>(null);
const winningBid = ref<number | null>(null);
const isEnded = ref(false);
const isLoading = ref(true);
const countdown = ref("");

let countdownInterval: ReturnType<typeof setInterval> | null = null;

/* LOAD AUCTION */
const loadAuction = async () => {
  isLoading.value = true;

  const response = await api.get(`/auctions/${auctionId}`);
  auction.value = response.data;

  console.log("END TIME RAW:", auction.value.end_time);
  console.log("END TIME PARSED:", new Date(auction.value.end_time));

  winner.value = response.data.winner ?? null;
  winningBid.value = response.data.winning_bid ?? null;
  isEnded.value = response.data.status === "ended";

  isLoading.value = false;
};

/* PLACE BID */
const placeBid = async () => {
  try {
    const response = await api.post(
      `/auctions/${auctionId}/bids`,
      {
        amount: bidAmount.value
      }
    );

    alert(response.data.message);

    bidAmount.value = "";

    await loadAuction();

  } catch (error: unknown) {

    console.log(error);

    alert(
      error.response?.data?.message ??
      "Bid gagal"
    );
  }
};

const highestBid = computed(() => {

  if (!auction.value?.bids?.length) {
    return auction.value?.starting_price ?? 0;
  }

  return Math.max(
    ...auction.value.bids.map(
      bid => Number(bid.amount)
    )
  );

});

/* COUNTDOWN */
const updateCountdown = () => {
  if (!auction.value?.end_time) {
    countdown.value = "-";
    return;
  }

  const endTime = new Date(auction.value.end_time).getTime();

if (!auction.value?.end_time) {
  countdown.value = "-";
  return;
}

  if (isNaN(endTime)) {
    console.log("INVALID DATE:", auction.value.end_time);
    countdown.value = "Invalid time";
    return;
  }

  const distance = endTime - Date.now();

  if (distance <= 0) {
    countdown.value = "Lelang Selesai";
    isEnded.value = true;
    return;
  }

  const hours = Math.floor(distance / (1000 * 60 * 60));
  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((distance % (1000 * 60)) / 1000);

  countdown.value = `${hours}j ${minutes}m ${seconds}d`;
};

/* REALTIME */
onMounted(async () => {
  await loadAuction();

  countdownInterval = setInterval(updateCountdown, 1000);

  echo.channel(`auction.${auctionId}`)
    .subscribed(() => {
      console.log("SUBSCRIBED SUCCESS");
    })
    .listen(".auction.ended", (e: AuctionEndedEvent) => {
      console.log("🔥 AUCTION ENDED:", e);

      winner.value = e.winner ?? "-";
      winningBid.value = e.winning_bid ?? 0;
      isEnded.value = true;
    })
   .listen(".bid.placed", async (e) => {

  console.log("================================");
  console.log("BID REALTIME MASUK");
  console.log(e);

  await loadAuction();

  console.log(
    "DATA SETELAH RELOAD:",
    auction.value
  );

})
    .listen(".OutbidNotification", () => {
     notification.value = "Kamu sudah dikalahkan bid lain!";
setTimeout(() => notification.value = null, 3000);
      loadAuction();
    });
});

/* CLEANUP */
onUnmounted(() => {
  if (countdownInterval) clearInterval(countdownInterval);
});
</script>


<template>
  <div class="page" v-if="auction">

    <!-- HEADER -->
    <div class="card header-card">
      <h1>{{ auction.title }}</h1>
      <p class="desc">{{ auction.description }}</p>

      <div class="status-row">
        <span class="label">Status:</span>

        <span class="badge" :class="auction.status">
          {{ auction.status }}
        </span>
      </div>

      <div class="countdown">
        ⏱ {{ countdown }}
      </div>
    </div>

    <!-- WINNER -->
    <div v-if="isEnded" class="card winner-card">
      🏆 AUCTION SELESAI

      <p>
        Pemenang:
        <b>{{ winner || 'Tidak ada pemenang' }}</b>
      </p>

      <p>
        Harga Menang:
        <b class="price">Rp {{ winningBid ?? 0 }}</b>
      </p>
    </div>

    <!-- BID SECTION -->
    <div class="card">

      <h3>Pasang Bid</h3>

      <input
        v-model="bidAmount"
        type="number"
        class="input"
        placeholder="Masukkan nominal bid"
        :disabled="auction.status !== 'active'"
      />

      <button
  class="btn"
  @click="placeBid"
:disabled="auction.status !== 'active'"
>
  Bid Sekarang
</button>

    </div>

    <!-- HIGHEST BID -->
    <div class="card highlight">

      <h3>Bid Tertinggi</h3>

      <div class="big-price">
        Rp {{ highestBid }}
      </div>

    </div>

    <!-- HISTORY -->
    <div class="card">

      <h3>Riwayat Bid</h3>

      <div
        v-for="bid in auction.bids"
        :key="bid.id"
        class="bid-item"
      >
        <div>User ID: {{ bid.user_id }}</div>
        <div>Rp {{ bid.amount }}</div>
      </div>

    </div>

  </div>

  <div v-else class="loading">
    Loading detail lelang...
  </div>
</template>

<style scoped>
.page {
  max-width: 900px;
  margin: auto;
  padding: 20px;
  font-family: sans-serif;
  background: #f6f7fb;
}

.card {
  background: white;
  padding: 16px;
  margin-bottom: 16px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}

.header-card h1 {
  margin: 0;
}

.desc {
  color: #666;
}

.status-row {
  display: flex;
  gap: 10px;
  align-items: center;
}

.badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
}

.badge.active {
  background: #e6ffed;
  color: #1a7f37;
}

.badge.ended {
  background: #ffe6e6;
  color: #c62828;
}

.countdown {
  margin-top: 10px;
  font-weight: bold;
}

.input {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.btn {
  margin-top: 10px;
  width: 100%;
  padding: 10px;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.highlight {
  background: linear-gradient(135deg, #e0f2fe, #ffffff);
}

.big-price {
  font-size: 24px;
  font-weight: bold;
  color: #16a34a;
}

.bid-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #eee;
}

.loading {
  text-align: center;
  padding: 50px;
  color: #888;
}
</style>
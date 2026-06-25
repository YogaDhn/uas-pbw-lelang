<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import api from "../services/api";
import echo from "../services/echo";

type Auction = {
  id: number;
  title: string;
  description: string;
  image: string;
  end_time: string;
  starting_price: number;
  bid_increment: number;
  status: string;
  bids: {
  id: number;
  user_id: number;
  amount: number;
  user?: {
    id: number;
    name: string;
  };
}[];
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

const currentUser = JSON.parse(
  localStorage.getItem("user") || "{}"

);
  echo.channel(`auction.${auctionId}`)
    .subscribed(() => {
      console.log("SUBSCRIBED SUCCESS");
    })


    .listen(".auction.ended", async (e: AuctionEndedEvent) => {
    console.log("===== AUCTION ENDED EVENT =====");
  console.log(e);

  isEnded.value = true;

  if (auction.value) {
    auction.value.status = "ended";
  }

  winner.value = e.winner ?? "-";
  winningBid.value = e.winning_bid ?? 0;

    if (auction.value) {
        auction.value.status = "ended";
    }
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
    .listen(".OutbidNotification", (e: any) => {

  // Hanya tampilkan ke user yang kalah
  if (e.old_user_id !== currentUser.id) {
    return;
  }

  notification.value = e.message;

  setTimeout(() => {
    notification.value = null;
  }, 3000);

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
  <div
  v-if="notification"
  class="notification"
>
  {{ notification }}
</div>
<div class="back-wrapper">
  <button
    class="back-btn"
    @click="$router.push('/')"
  >
    ← Kembali ke Daftar Lelang
  </button>
</div>
    <div class="product-layout">

      <div class="image-section">
        <img
          v-if="auction.image"
          :src="`http://127.0.0.1:8000/storage/${auction.image}`"
          class="auction-image"
        />
      </div>

      <div class="info-section">

        <h1>{{ auction.title }}</h1>

        <p class="desc">
          {{ auction.description }}
        </p>

        <div class="status-row">
          <span>Status:</span>

          <span
            class="badge"
            :class="auction.status"
          >
            {{ auction.status }}
          </span>
        </div>

        <div class="countdown">
          ⏱ {{ countdown }}
        </div>

        <div class="highest-bid-section">

  <div class="highest-label">
    Bid Tertinggi
  </div>

  <div class="highest-price">
    Rp {{ highestBid }}
  </div>

</div>

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

    </div>

    <div class="card">
<!-- WINNER -->
<div
  v-if="isEnded"
  class="winner-card"
>
  <h2>🏆 Lelang Selesai</h2>

  <div class="winner-info">
    <p>
      Pemenang:
      <strong>
        {{ winner || 'Tidak ada pemenang' }}
      </strong>
    </p>

    <p>
      Harga Menang:
      <strong>
        Rp {{ winningBid ?? 0 }}
      </strong>
    </p>
  </div>
</div>
      <h3>Riwayat Bid</h3>

      <div
  v-for="bid in auction.bids"
  :key="bid.id"
  class="bid-item"
>
  <div>
    {{ bid.user?.name || 'Unknown User' }}
  </div>

  <div>
    Rp {{ Number(bid.amount).toLocaleString('id-ID') }}
  </div>
</div>

    </div>

  </div>

  <div v-else class="loading">
    Loading detail lelang...
  </div>
</template>

<style scoped>
.product-layout{
  display:flex;
  gap:30px;
  background:white;
  padding:25px;
  border-radius:16px;
  margin-bottom:20px;
  box-shadow:0 6px 20px rgba(0,0,0,.05);
}

.image-section{
  flex:1;
}

.info-section{
  flex:1;
}

.highest-price{
  font-size:32px;
  font-weight:bold;
  color:#16a34a;
  margin:20px 0;
}

.page{
  max-width:1200px;
}
.auction-image{
  width:100%;
  max-height:400px;
  object-fit:cover;
  border-radius:12px;
  margin:15px 0;
}

.product-layout{
  display:flex;
  gap:30px;
  background:white;
  padding:25px;
  border-radius:16px;
  margin-bottom:20px;
  box-shadow:0 6px 20px rgba(0,0,0,.05);
}

.image-section{
  flex:1;
}

.info-section{
  flex:1;
}

.auction-image{
  width:100%;
  height:450px;
  object-fit:cover;
  border-radius:12px;
}

.highest-price{
  font-size:32px;
  font-weight:bold;
  color:#16a34a;
  margin:20px 0;
}

@media(max-width:768px){
  .product-layout{
    flex-direction:column;
  }

  .auction-image{
    height:300px;
  }
}

.page {
  max-width: 1200px;
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
.highest-bid-section{
  margin:20px 0;
}

.highest-label{
  font-size:14px;
  color:#666;
  margin-bottom:6px;
  font-weight:600;
}

.highest-price{
  font-size:32px;
  font-weight:bold;
  color:#16a34a;
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
.notification{
  background:#fef3c7;
  color:#92400e;
  padding:12px;
  border-radius:8px;
  margin-bottom:15px;
  font-weight:600;
}
.loading {
  text-align: center;
  padding: 50px;
  color: #888;
}
.auction-image{
  width:100%;
  max-height:400px;
  object-fit:cover;
  border-radius:12px;
  margin:15px 0;
}
.back-wrapper{
  margin-bottom:20px;
}

.back-btn{
  background:#f3f4f6;
  border:none;
  padding:10px 16px;
  border-radius:10px;
  cursor:pointer;
  font-weight:600;
  transition:.2s;
}

.back-btn:hover{
  background:#e5e7eb;
}
.winner-card{
  background:#ecfdf5;
  border:2px solid #10b981;
  border-radius:16px;
  padding:20px;
  margin-bottom:20px;
}

.winner-card h2{
  margin:0 0 12px 0;
  color:#065f46;
}

.winner-info p{
  margin:8px 0;
  font-size:16px;
}

.winner-info strong{
  color:#16a34a;
}
</style>
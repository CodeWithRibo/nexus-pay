<script setup>
import { Dot, Wifi, WifiOff } from "lucide-vue-next";
import { onMounted, onUnmounted, ref } from "vue";

const isConnected = ref(navigator.onLine);

const updateConnectionStatus = () => {
    isConnected.value = navigator.onLine;
};

onMounted(() => {
    window.addEventListener("online", updateConnectionStatus);
    window.addEventListener("offline", updateConnectionStatus);
});
onUnmounted(() => {
    window.removeEventListener("online", updateConnectionStatus);
    window.removeEventListener("offline", updateConnectionStatus);
});

const currentDate = new Date();
const options = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
};
const formattedDate = currentDate.toLocaleDateString("en-PH", options);

const currentTime = currentDate.toLocaleTimeString("en-PH", {
    hour12: true,
    hour: "2-digit",
    minute: "2-digit"
});

</script>

<template>
    <footer
        class="w-full p-4 mt-auto border-t border-white/20 bg-[#FFFFFF0D] shadow-sm md:flex md:items-center md:justify-between"
    >
        <div class="flex items-center">
            <span
                class="text-sm text-body sm:text-center flex items-center text-gray-300 uppercase tracking-wider font-semibold"
            >
                <Dot class="size-10" />
                System Online
            </span>
            <span class="mr-3 ml-5">|</span>
            <span
                class="text-sm text-body sm:text-center flex items-center text-gray-300 uppercase tracking-wider font-semibold"
            >
                <span v-if="isConnected" class="flex">
                    <Wifi class="size-5 mr-3" />
                    Connected
                </span>
                <span v-else class="flex">
                    <WifiOff class="size-5 mr-3" />
                    Offline
                </span>
            </span>
        </div>
        <div
            class="flex flex-wrap items-center font-semibold uppercase tracking-wider text-gray-300 space-x-3"
        >
            <h1>{{ formattedDate }}</h1>
            <Dot class="size-7" />
            <span>{{ currentTime }}</span>
        </div>
    </footer>
</template>

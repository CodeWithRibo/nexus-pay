<script setup>
import { Dot, Wifi } from "lucide-vue-next";
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
                <Wifi class="size-5 mr-3" />
                Connected
            </span>
        </div>
        <div
            class="flex flex-wrap items-center font-semibold uppercase tracking-wider text-gray-300 space-x-3"
        >
            <h1>Wednesday, March 18, 2026</h1>
            <Dot class="size-7" />
            <span>10:14 PM</span>
        </div>
    </footer>
</template>

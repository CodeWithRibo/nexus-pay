<script setup>
import { Label } from "@/components/ui/label/index.js";
import { Button } from "@/components/ui/button/index.js";
import { BanknoteArrowUp, QrCode } from "lucide-vue-next";
import { router, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);

console.log(user ? user.value.email : "guest");
</script>

<template>
    <div class="flex flex-1 w-full min-h-0 overflow-hidden">
        <!--Aside-->
        <div class="hidden md:flex flex-col w-120 border border-white/20">
            <div class="flex flex-col flex-1 overflow-y-auto">
                <nav class="flex-1 pt-15 pl-7 bg-[#FFFFFF0D] space-y-10">
                    <div class="space-y-5">
                        <h1
                            class="text-[45px] font-bold tracking-tight text-white"
                        >
                            Bill Summary
                        </h1>
                        <p
                            class="text-gray-400 font-semibold text-[18px] w-[70%] tracking-wider"
                        >
                            Please verify your details before proceeding to
                            payment
                        </p>
                    </div>
                    <div>
                        <div>
                            <Label
                                for="student_name"
                                class="uppercase font-bold text-base text-gray-400 tracking-wider mb-2"
                            >
                                Student name
                            </Label>
                            <h2
                                class="text-white font-semibold text-3xl tracking-tighter"
                            >
                                Jonathan Doe
                            </h2>
                            <div
                                class="w-[85%] border-b-[0.01rem] border-b-gray-500 mt-8 mb-4"
                            ></div>
                        </div>
                        <div>
                            <Label
                                for="student_name"
                                class="uppercase font-bold text-base text-gray-400 tracking-wider mb-2"
                            >
                                Student ID
                            </Label>
                            <h2
                                class="text-white font-semibold text-3xl tracking-tighter"
                            >
                                02000411496
                            </h2>
                            <div
                                class="w-[85%] border-b-[0.01rem] border-b-gray-500 mt-8 mb-4"
                            ></div>
                        </div>
                        <div>
                            <Label
                                for="student_name"
                                class="uppercase font-bold text-base text-gray-400 tracking-wider mb-2"
                            >
                                Description
                            </Label>
                            <h2
                                class="text-white font-semibold text-3xl tracking-tighter"
                            >
                                Tuition Fee Payment
                            </h2>
                            <div
                                class="w-[85%] border-b-[0.01rem] border-b-gray-500 mt-8 mb-4"
                            ></div>
                        </div>
                    </div>
                    <div
                        class="bg-[#FFFFFF14] rounded-lg p-5 w-92 text-white space-y-5"
                    >
                        <span class="text-gray-400 text-lg"
                            >Current Balance</span
                        >
                        <h1 class="text-5xl font-bold">₱100,50.50</h1>
                    </div>
                </nav>
            </div>
        </div>
        <!--Hero-->
        <div class="flex flex-1 flex-col mt-20 space-y-10">
            <div class="space-y-5">
                <h1 class="text-center text-5xl font-bold tracking-tighter">
                    Select Payment Method
                </h1>
                <p class="text-center text-xl tracking-tight text-gray-400">
                    Choose your preferred way to pay
                </p>
            </div>
            <div class="flex gap-5 mx-10">
                <Button
                    @click="
                        router.visit(route('kiosk.tuition-fee.cash-insertion'))
                    "
                    class="group flex-1 h-42 p-15 rounded-2xl border-2 border-white/20 transition-all duration-300 flex items-center justify-start gap-6 text-left hover:border-white/60 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]"
                >
                    <div class="flex items-center gap-6">
                        <div
                            class="rounded-full p-4 border border-white/30 text-white transition-colors duration-300 group-hover:bg-white/15 group-hover:border-white/60"
                        >
                            <BanknoteArrowUp class="size-10" />
                        </div>
                        <div class="space-y-3">
                            <span class="text-3xl leading-tight"
                                >Pay via Kiosk</span
                            >
                            <br />
                            <span class="text-gray-400 text-lg tracking-wide"
                                >Insert cash bills into the machine</span
                            >
                        </div>
                    </div>
                </Button>
                <Button
                    class="group flex-1 h-42 p-10 rounded-2xl border-2 border-white/20 transition-all duration-300 flex items-center justify-start gap-6 text-left hover:border-white/60 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]"
                >
                    <div
                        class="rounded-full p-4 border border-white/30 text-white transition-colors duration-300 group-hover:bg-white/15 group-hover:border-white/60"
                    >
                        <QrCode class="size-10" />
                    </div>
                    <div class="space-y-3">
                        <span class="text-3xl leading-tight"
                            >Pay via Paymongo
                        </span>
                        <br />
                        <span class="text-gray-400 text-lg tracking-wide"
                            >Scan qr code
                        </span>
                    </div>
                </Button>
            </div>
            <div class="flex item-center justify-center mt-52">
                <p class="text-gray-300 text-lg font-sembold tracking-wider">
                    This kiosk is monitored for security.Please do not leave
                    during a transaction
                </p>
            </div>
        </div>
    </div>
</template>

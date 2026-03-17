<script setup>
import { GraduationCap, Receipt, ShieldCheck } from "lucide-vue-next";
import { ref } from "vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog/index.js";
import { Button } from "@/components/ui/button/index.js";
import { router } from "@inertiajs/vue3";

const cardData = [
    {
        name: "Tuition Fees",
        icon: GraduationCap,
        description: "Pay your semester tuition and enrollment fees",
        routeName: route("kiosk.tuition-fee.payment-method"),
    },
    {
        name: "Other School Fees",
        icon: Receipt,
        description: "Laboratory, library, and miscellaneous fees",
        routeName: route("kiosk.other-fee.payment-method"),
    },
];

const isDpaOpen = ref(false);
const serviceVal = ref(null);
const handleService = (item) => {
    isDpaOpen.value = true;
    serviceVal.value = item;
};
const acceptServiceVal = () => {
    router.visit(serviceVal.value.routeName);
    isDpaOpen.value = false;
};

const DpaCondition = [
    "Your personal information will be collected and processed for payment verification and record-keeping purposes.",
    "Data collected includes: Student ID, Name, Payment Amount, Transaction Date & Time.",
    "Your information will be stored securely and accessed only by authorized school personnel.",
    "You have the right to access, correct, or request deletion of your personal data.",
    "We will not share your information with third parties without your consent, except as required by law.",
    "Transaction records will be retained for a period of 5 years as per institutional policy.",
];
</script>

<template>
    <div class="flex flex-col items-center space-y-32">
        <div class="text-5xl font-bold">Please select a service</div>

        <div class="flex gap-5">
            <div v-for="(item, index) in cardData" :key="index">
                <button
                    @click="handleService(item)"
                    class="border border-gray-500 rounded-lg bg-[#FFFFFF0D] w-112.5 h-96 transition-all duration-500 ease-in-out hover:border-white hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]"
                >
                    <div class="flex flex-col items-center p-10 space-y-10">
                        <div
                            class="rounded-full w-32 h-32 flex items-center justify-center bg-white/10 group-hover:bg-white/20 transition-colors hover:bg-white/20 duration-500"
                        >
                            <component :is="item.icon" class="size-16" />
                        </div>
                        <p class="text-4xl font-semibold">{{ item.name }}</p>
                        <p class="text-gray-400 font-semibold text-lg">
                            {{ item.description }}
                        </p>
                    </div>
                </button>
            </div>
            <!--DPA MODAL-->
            <Dialog v-model:open="isDpaOpen" class="">
                <DialogContent
                    class="sm:max-w-3xl p-10 overflow-y-auto rounded-3xl border-2 border-white/30 bg-[#171717] shadow-[0_0_20px_rgba(255,255,255,0.3)]"
                >
                    <DialogHeader>
                        <DialogTitle class="flex items-center gap-2 mb-5">
                            <ShieldCheck class="size-11 text-white" />
                            <h1 class="text-4xl font-bold text-white">
                                Data Privacy Notice
                            </h1>
                        </DialogTitle>
                        <DialogDescription
                            class="text-gray-300 space-y-5 leading-8 text-lg"
                        >
                            <p class="w-[90%]">
                                In compliance with the Data Privacy Act of 2012
                                (Republic Act No.10173), we inform you that:
                            </p>
                            <ul
                                class="list-disc list-inside w-[90%] space-y-2 ml-4"
                            >
                                <li v-for="item in DpaCondition">
                                    {{ item }}
                                </li>
                            </ul>
                            <p class="w-[90%] mb-10">
                                By proceeding, you consent to the collection and
                                processing of your personal information as
                                described above.
                            </p>
                            <div class="space-x-5">
                                <Button
                                    @click="acceptServiceVal"
                                    class="w-72 h-20 hover:w-73 hover:h-21 text-2xl bg-white hover:bg-white border-gray-400 hover:border-white hover:shadow-[0_0_20px_rgba(255,255,255,0.3)] text-black font-semibold transition-all duration-300"
                                >
                                    I Agree & Continue
                                </Button>
                                <Button
                                    @click="isDpaOpen = false"
                                    variant="outline"
                                    class="w-72 h-20 hover:w-73 hover:h-21 text-2xl bg-transparent hover:bg-transparent border-gray-400 hover:border-white hover:text-white"
                                >
                                    Cancel
                                </Button>
                            </div>
                        </DialogDescription>
                    </DialogHeader>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>

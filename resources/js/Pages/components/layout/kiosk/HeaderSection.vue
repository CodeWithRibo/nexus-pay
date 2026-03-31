<script setup>
import { Button } from "@/components/ui/button/index.js";
import { LogIn, ArrowLeft, X, ShieldCheck } from "lucide-vue-next";
import { ref } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import LogoutForm from "@/Pages/components/auth/LogoutForm.vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";

const showActionBtn = ref(false);
const page = usePage();

const isAuth = page.props.auth.user !== null;

const toLogin = () => {
    router.visit(route("login"));
};

if (
    page.url === "/kiosk/service-selection" ||
    page.url === "/kiosk/outstanding-balance"
) {
    showActionBtn.value = true;
}

const backPage = () => {
    history.back();
};
</script>

<template>
    <header class="p-4 dark:bg-gray-100 border border-white/20">
        <div class="px-5 flex justify-between items-center h-16 mx-auto">
            <Button
                @click="backPage"
                v-if="showActionBtn"
                class="text-2xl bg-transparent hover:bg-transparent py-6 w-25 font-bold tracking-wider"
            >
                <ArrowLeft class="size-8" />
                Back
            </Button>
            <div aria-label="Logo" class="flex items-center p-2">
                <img
                    :src="'/storage/nexus_logo.png'"
                    class="w-18 h-15"
                    alt="nexus_logo"
                />
                <p class="tracking-widest uppercase font-bold">Nexus Pay</p>
            </div>

            <div class="items-center shrink-0 hidden lg:flex gap-5">
                <Button
                    @click="toLogin"
                    v-if="!isAuth"
                    class="text-xl bg-white/20 hover:bg-white/20 py-6 w-full font-bold tracking-wider"
                >
                    <LogIn class="size-5 text-white" />
                    Login
                </Button>
                <AlertDialog>
                    <AlertDialogTrigger>
                        <Button
                            v-if="showActionBtn && isAuth"
                            class="text-xl bg-white/20 hover :bg-white/20 py-6 w-full font-bold tracking-wider"
                        >
                            <LogIn class="size-5 text-white" />
                            Logout
                        </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle
                                class="text-xl flex items-center gap-2"
                            >
                                <ShieldCheck class="size-7" />
                                <p>Secure Logout</p>
                            </AlertDialogTitle>
                            <AlertDialogDescription class="space-y-5">
                                <p class="text-lg">
                                    To prevent unauthorized access to your
                                    student account, please confirm you are
                                    finished using this kiosk.
                                </p>
                                <div
                                    class="flex items-center justify-start flex-row-reverse gap-3"
                                >
                                    <LogoutForm>
                                        <Button
                                            variant="destructive"
                                            class="text-lg py-6 -mt-0.5"
                                        >
                                            <LogIn class="size-5 text-white" />
                                            <p>Securely Logout</p>
                                        </Button>
                                    </LogoutForm>
                                    <AlertDialogCancel
                                        class="text-lg py-6 -mt-0.5 bg-black hover:bg-black hover:opacity-85 text-white hover:text-white border-none"
                                    >
                                        <X class="size-6" />
                                        <p>Go Back</p>
                                    </AlertDialogCancel>
                                </div>
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                    </AlertDialogContent>
                </AlertDialog>

                <Button
                    v-if="!showActionBtn"
                    class="text-xl bg-white/20 hover:bg-white/20 py-6 w-full font-bold tracking-wider"
                >
                    <LogIn class="size-5 text-white" />
                    Cancel Transaction
                </Button>
            </div>
        </div>
    </header>
</template>

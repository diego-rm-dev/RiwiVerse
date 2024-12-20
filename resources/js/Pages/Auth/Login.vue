<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import githubIcon from '../../../Icons/Auth/github-dark.svg';
import googleIcon from '../../../Icons/Auth/google-dark.svg';
import riwiVerseIcon from '../../../Icons/riwiverse-icon.svg';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
});

const redirectGithub = () => {
    window.location.href = '/redirect/github';
}

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Log in" />
    <div class="relative min-h-screen bg-white flex items-center justify-start pl-[300px]">
        <div class="absolute top-0 left-0 w-full h-full"
            style="background: #6B5CFF; clip-path: polygon(0 0, 30% 0, 70% 100%, 0% 100%);"></div>

        <div class="absolute top-6 right-6 text-2xl font-bold text-indigo-700">
            <img :src=riwiVerseIcon alt="RiwiVerse" class="w-18 h-10" />
        </div>

        <div class="relative z-10 w-full max-w-sm bg-[#2f2d42] rounded-xl p-8 shadow-lg">
            <h1 class="text-white text-2xl font-bold text-center mb-6">Ingresa</h1>

            <div class="flex items-center justify-center space-x-6 mb-8">
                <button type="button"
                    class="bg-gray-200 w-12 h-12 rounded-full flex items-center justify-center hover:opacity-90 transition">
                    <img :src=googleIcon alt="G" class="w-6 h-6 text-gray-800" />
                </button>

                <button @click="redirectGithub" type="button"
                    class="bg-gray-200 w-12 h-12 rounded-full flex items-center justify-center hover:opacity-90 transition">
                    <img :src=githubIcon alt="G" class="w-6 h-6 text-gray-800" />
                </button>
            </div>

            <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-400">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="flex flex-col items-center">
                    <label for="email" class="block text-white mb-2 font-bold text-center">Email</label>
                    <input id="email" v-model="form.email" type="text"
                        class="w-3/4 mx-auto px-4 py-2 rounded-full bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required />
                    <InputError :message="form.errors.email" class="mt-2 text-red-500 text-sm text-center" />
                </div>

                <div class="flex flex-col items-center mt-5">
                    <label for="password" class="block text-white mb-2 font-bold text-center">Password</label>
                    <input id="password" v-model="form.password" type="password"
                        class="w-3/4 mx-auto px-4 py-2 rounded-full bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required />
                    <InputError :message="form.errors.password" class="mt-2 text-red-500 text-sm text-center" />
                </div>

                <div class="flex justify-center">
                    <button type="submit" :disabled="form.processing"
                        class="relative inline-block bg-[#EC5F5F] text-white font-semibold px-6 py-2 focus:outline-none transform skew-x-[-10deg] hover:bg-red-600 transition focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50">
                        <span class="inline-block transform skew-x-[10deg]">ingresar</span>
                    </button>
                </div>
            </form>

            <!-- Decoraciones -->
            <div class="absolute top-[50px] -left-12 ">
                <div class="relative">
                    <div class="bg-[#E5C558] w-[100px] h-6 rounded-full"></div>
                    <div class="bg-[#E5C558] w-[100px] h-6 rounded-full absolute top-7 left-6"></div>
                    <div class="bg-[#E5C558] w-10 h-6 rounded-full absolute top-14 left-3"></div>
                </div>
            </div>

            <div class="absolute bottom-[50px] right-[-40px]">
                <div class="relative">
                    <div class="bg-[#E5C558] w-[100px] h-6 rounded-full"></div>
                    <div class="bg-[#E5C558] w-[100px] h-6 rounded-full absolute top-7 left-6"></div>
                </div>
            </div>
        </div>
    </div>
</template>

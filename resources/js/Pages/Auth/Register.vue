<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import riwiVerseIcon from '../../../Icons/riwiverse-icon.svg';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const redirectHome = () => {
    window.location.href = '/';
}
</script>

<template>

    <Head title="Register" />
    <div class="relative min-h-screen bg-white flex items-center justify-end pr-[200px]">
        <div class="absolute top-0 left-0 w-full h-full"
            style="background: #6B5CFF; clip-path: polygon(100% 0, 70% 0, 30% 100%, 100% 100%);"></div>

        <div class="absolute top-6 left-6 text-2xl font-bold text-indigo-700 logo">
            <img :src="riwiVerseIcon" v-on:click="redirectHome" alt="RiwiVerse" class="w-18 h-10" />
        </div>

        <div class="relative z-10 w-full max-w-xl bg-[#2f2d42] rounded-xl p-8 shadow-lg">
            <h1 class="text-white text-2xl font-bold text-center mb-6">Registro</h1>

            <div v-if="$page.props.jetstream.flash.success" class="mb-4 text-center text-sm font-medium text-green-400">
                {{ $page.props.jetstream.flash.success }}
            </div>

            <!-- Formulario con dos columnas -->
            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid grid-cols-2 gap-8 items-start">
                    <div class="flex flex-col items-center">
                        <label for="name" class="block text-white mb-2 font-bold text-center">Nombre</label>
                        <input id="name" v-model="form.name" type="text"
                            class="w-3/4 mx-auto px-4 py-2 rounded-full bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required autofocus autocomplete="name" />
                        <InputError :message="form.errors.name" class="mt-2 text-red-500 text-sm text-center" />
                    </div>

                    <div class="flex flex-col items-center">
                        <label for="email" class="block text-white mb-2 font-bold text-center">Email</label>
                        <input id="email" v-model="form.email" type="email"
                            class="w-3/4 mx-auto px-4 py-2 rounded-full bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required autocomplete="username" />
                        <InputError :message="form.errors.email" class="mt-2 text-red-500 text-sm text-center" />
                    </div>

                    <div class="flex flex-col items-center">
                        <label for="password" class="block text-white mb-2 font-bold text-center">Password</label>
                        <input id="password" v-model="form.password" type="password"
                            class="w-3/4 mx-auto px-4 py-2 rounded-full bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required autocomplete="new-password" />
                        <InputError :message="form.errors.password" class="mt-2 text-red-500 text-sm text-center" />
                    </div>

                    <div class="flex flex-col items-center">
                        <label for="password_confirmation" class="block text-white mb-2 font-bold text-center">Confirm
                            Password</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password"
                            class="w-3/4 mx-auto px-4 py-2 rounded-full bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required autocomplete="new-password" />
                        <InputError :message="form.errors.password_confirmation"
                            class="mt-2 text-red-500 text-sm text-center" />
                    </div>
                </div>

                <div class="flex justify-center mt-8">
                    <button type="submit" :disabled="form.processing"
                        class="relative inline-block bg-[#EC5F5F] text-white font-semibold px-6 py-2 focus:outline-none transform skew-x-[-10deg] hover:bg-red-600 transition focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50">
                        <span class="inline-block transform skew-x-[10deg]">Registrarme</span>
                    </button>
                </div>
                <a class="font-bold text-white text-center block" href="/login">¿Ya tienes una cuenta? ¡Logéate!</a>
            </form>

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
<style>
.logo img {
    width: 200px;
    /* Tamaño del logo */
    transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    cursor: pointer;
    /* Indica que es interactivo */
}

.logo img:hover {
    transform: scale(1.07);
    /* Aumenta ligeramente de tamaño */
    opacity: 0.9;
    /* Reduce un poco la opacidad */
}
</style>
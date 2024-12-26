<template>
    <div>
        <h1 class="text-xl font-bold mb-4">Publicación #{{ publication.id }}</h1>

        <div class="mb-2">
            <h2 class="font-semibold">{{ publication.title }}</h2>
            <p>{{ publication.text }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Likes: {{ publication.likers?.length || 0 }}</p>
            <p class="text-sm text-gray-500">Comentarios: {{ publication.comments?.length || 0 }}</p>
        </div>

        <div v-for="comment in publication.comments" :key="comment.id" class="border p-2 my-2">
            <p>
                <strong>{{ comment.user?.name }}:</strong> {{ comment.text }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const publication = props.publication || {}
</script>